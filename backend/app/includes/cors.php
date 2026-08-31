<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function apescon_allowed_origins(): array
{
    $origins = config('allowed_origins', []);

    if (!is_array($origins)) {
        return [];
    }

    return array_values(array_filter(array_map(
        static fn ($origin): string => rtrim(trim((string) $origin), '/'),
        $origins
    )));
}

function apescon_origin_from_url(string $url): string
{
    $parts = parse_url($url);

    if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
        return '';
    }

    $origin = strtolower((string) $parts['scheme']) . '://' . strtolower((string) $parts['host']);

    if (!empty($parts['port'])) {
        $origin .= ':' . (int) $parts['port'];
    }

    return $origin;
}

function apescon_request_origin(): string
{
    $origin = trim((string) ($_SERVER['HTTP_ORIGIN'] ?? ''));

    if ($origin !== '') {
        return rtrim($origin, '/');
    }

    $referer = trim((string) ($_SERVER['HTTP_REFERER'] ?? ''));

    return $referer !== '' ? apescon_origin_from_url($referer) : '';
}

function apescon_is_trusted_origin(): bool
{
    $requestOrigin = apescon_request_origin();

    return $requestOrigin !== ''
        && in_array($requestOrigin, apescon_allowed_origins(), true);
}

$origin = trim((string) ($_SERVER['HTTP_ORIGIN'] ?? ''));
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($origin !== '' && in_array(rtrim($origin, '/'), apescon_allowed_origins(), true)) {
    header('Access-Control-Allow-Origin: ' . rtrim($origin, '/'));
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, X-Requested-With');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 600');
    header('Vary: Origin');
}

if ($method === 'OPTIONS') {
    if ($origin === '' || !in_array(rtrim($origin, '/'), apescon_allowed_origins(), true)) {
        http_response_code(403);
        exit;
    }

    http_response_code(204);
    exit;
}
