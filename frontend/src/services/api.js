const configuredBaseURL = (import.meta.env.VITE_API_BASE_URL || '').replace(/\/$/, '')

let csrfToken = ''

export class ApiError extends Error {
  constructor(message, status = 0, data = {}) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.data = data
  }
}

const parseResponse = async (response) => {
  const responseText = await response.text()

  if (!responseText) return {}

  try {
    return JSON.parse(responseText)
  } catch {
    throw new ApiError('The server returned an invalid response.', response.status)
  }
}

const getCsrfToken = async (forceRefresh = false) => {
  if (!configuredBaseURL) {
    throw new ApiError('VITE_API_BASE_URL is not configured.')
  }

  if (csrfToken && !forceRefresh) return csrfToken

  const response = await fetch(`${configuredBaseURL}/api/auth/csrf.php`, {
    method: 'GET',
    headers: {
      Accept: 'application/json',
    },
    credentials: 'include',
    cache: 'no-store',
  })

  const data = await parseResponse(response)

  if (!response.ok || !data.success || !data.csrf) {
    throw new ApiError(data.message || 'Unable to retrieve the security token.', response.status, data)
  }

  csrfToken = data.csrf
  return csrfToken
}

const apiRequest = async (path, options = {}, retryOnCsrf = true) => {
  if (!configuredBaseURL) {
    throw new ApiError('VITE_API_BASE_URL is not configured.')
  }

  const method = String(options.method || 'GET').toUpperCase()
  const unsafe = ['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)
  const headers = {
    Accept: 'application/json',
    ...(options.headers || {}),
  }

  if (unsafe) {
    headers['X-CSRF-Token'] = await getCsrfToken()
  }

  const fetchOptions = {
    ...options,
    method,
    headers,
    credentials: 'include',
    cache: 'no-store',
  }

  if (options.body && !(options.body instanceof FormData) && typeof options.body !== 'string') {
    headers['Content-Type'] = 'application/json'
    fetchOptions.body = JSON.stringify(options.body)
  }

  const response = await fetch(`${configuredBaseURL}${path}`, fetchOptions)
  const data = await parseResponse(response)

  if (response.status === 419 && unsafe && retryOnCsrf) {
    csrfToken = ''
    await getCsrfToken(true)
    return apiRequest(path, options, false)
  }

  if (!response.ok || data.success === false) {
    throw new ApiError(data.message || 'The request could not be completed.', response.status, data)
  }

  return data
}

export const apiGet = (path) => apiRequest(path, { method: 'GET' })

export const apiPost = (path, body = {}) =>
  apiRequest(path, {
    method: 'POST',
    body,
  })

export const clearApiCsrfCache = () => {
  csrfToken = ''
}
