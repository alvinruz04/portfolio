<?php
declare(strict_types=1);

function audit_client_ip(): ?string
{
    $keys = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR',
    ];

    foreach ($keys as $key) {
        $value = trim((string)($_SERVER[$key] ?? ''));
        if ($value === '') continue;

        // X-Forwarded-For may contain multiple IPs
        if ($key === 'HTTP_X_FORWARDED_FOR') {
            $parts = array_map('trim', explode(',', $value));
            foreach ($parts as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
            continue;
        }

        if (filter_var($value, FILTER_VALIDATE_IP)) {
            return $value;
        }
    }

    return null;
}

function audit_user_agent(): ?string
{
    $ua = trim((string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
    if ($ua === '') return null;
    return mb_substr($ua, 0, 255);
}

function audit_write(mysqli $conn, array $data): void
{
    $branchId   = (int)($data['branch_id'] ?? 0);
    $userId     = (int)($data['user_id'] ?? 0);
    $action     = strtoupper(trim((string)($data['action'] ?? '')));
    $entityType = trim((string)($data['entity_type'] ?? 'system'));
    $entityId   = (int)($data['entity_id'] ?? 0);
    $meta       = $data['meta'] ?? [];

    if ($branchId <= 0 || $userId <= 0 || $action === '') {
        return;
    }

    if (!is_array($meta)) {
        $meta = ['value' => $meta];
    }

    $ipAddress = trim((string)($data['ip_address'] ?? audit_client_ip() ?? ''));
    $userAgent = trim((string)($data['user_agent'] ?? audit_user_agent() ?? ''));

    if ($ipAddress !== '' && !isset($meta['ip_address'])) {
        $meta['ip_address'] = $ipAddress;
    }

    if ($userAgent !== '' && !isset($meta['user_agent'])) {
        $meta['user_agent'] = $userAgent;
    }

    $metaJson = null;
    if (!empty($meta)) {
        $metaJson = json_encode(
            $meta,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($metaJson !== false) {
            $metaJson = mb_substr($metaJson, 0, 65000);
        } else {
            $metaJson = null;
        }
    }

    $sql = "
        INSERT INTO audit_logs (
            branch_id,
            user_id,
            ip_address,
            user_agent,
            action,
            entity_type,
            entity_id,
            meta_json,
            created_at
        )
        VALUES (
            ?,
            ?,
            NULLIF(?, ''),
            NULLIF(?, ''),
            ?,
            ?,
            IF(? > 0, ?, NULL),
            ?,
            NOW()
        )
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return;

    $stmt->bind_param(
        "iissssiis",
        $branchId,
        $userId,
        $ipAddress,
        $userAgent,
        $action,
        $entityType,
        $entityId,
        $entityId,
        $metaJson
    );

    $stmt->execute();
    $stmt->close();
}