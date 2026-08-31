import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/main.css' // Tailwind CSS

// --- Version watcher (inline) ---
const VERSION_FILE_URL = '/version.txt'
const VERSION_KEY = 'app-version'

function startVersionWatcher({ intervalMs = 60000, auto = false } = {}) {
  let timer = null

  async function check() {
    try {
      const res = await fetch(`${VERSION_FILE_URL}?t=${Date.now()}`, { cache: 'no-store' })
      if (!res.ok) return
      const serverVersion = (await res.text()).trim()
      if (!serverVersion) return

      const currentVersion = localStorage.getItem(VERSION_KEY)

      // First run: record current server version
      if (!currentVersion) {
        localStorage.setItem(VERSION_KEY, serverVersion)
        return
      }

      // Version changed -> update available
      if (currentVersion !== serverVersion) {
        const doReload =
          auto || window.confirm('A new version of You Glow Babe Page is available. Reload now?')

        if (doReload) {
          // Prevent repeat prompts if the reload is delayed by intermediaries
          localStorage.setItem(VERSION_KEY, serverVersion)

          const { pathname, search, hash } = window.location
          const sep = search ? '&' : '?'
          window.location.replace(
            `${pathname}${search}${sep}v=${encodeURIComponent(serverVersion)}${hash}`,
          )
        }
        // If user declines, continue polling silently
      }
    } catch {
      // ignore network errors
    }
  }

  // initial + interval + when tab becomes visible
  check()
  timer = setInterval(check, intervalMs)
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') check()
  })

  return () => clearInterval(timer)
}

// --- Vue boot ---
const app = createApp(App)
app.use(router)
app.mount('#app')

// Start watcher only in production to avoid dev/HMR noise
let stopWatcher = null
if (import.meta.env.PROD) {
  stopWatcher = startVersionWatcher({ intervalMs: 60000, auto: false }) // set auto:true to reload without asking
}

// Clean up interval on HMR (dev)
if (import.meta.hot && typeof stopWatcher === 'function') {
  import.meta.hot.dispose(() => stopWatcher())
}
