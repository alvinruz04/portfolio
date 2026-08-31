<?php
declare(strict_types=1);

/**
 * APESCON password policy.
 *
 * Current internal policy:
 * - 12 to 128 characters
 * - At least one uppercase letter
 * - At least one lowercase letter
 * - At least one number
 * - At least one special character
 * - Reject obvious/repetitive/common passwords
 */
function validate_apescon_password(string $password): array
{
    $length = mb_strlen($password, 'UTF-8');

    /*
    |--------------------------------------------------------------------------
    | Length
    |--------------------------------------------------------------------------
    */

    if ($length < 12) {
        return [
            false,
            'Password must contain at least 12 characters.',
        ];
    }

    if ($length > 128) {
        return [
            false,
            'Password must not exceed 128 characters.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Character requirements
    |--------------------------------------------------------------------------
    */

    if (!preg_match('/\p{Lu}/u', $password)) {
        return [
            false,
            'Password must contain at least one uppercase letter.',
        ];
    }

    if (!preg_match('/\p{Ll}/u', $password)) {
        return [
            false,
            'Password must contain at least one lowercase letter.',
        ];
    }

    if (!preg_match('/\p{N}/u', $password)) {
        return [
            false,
            'Password must contain at least one number.',
        ];
    }

    /*
     * Requires punctuation/symbol rather than treating a space
     * as the required special character.
     */
    if (!preg_match('/[^\p{L}\p{N}\s]/u', $password)) {
        return [
            false,
            'Password must contain at least one special character.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Control characters
    |--------------------------------------------------------------------------
    */

    if (preg_match('/[\x00-\x1F\x7F]/u', $password)) {
        return [
            false,
            'Password contains unsupported characters.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Repeated single character
    |--------------------------------------------------------------------------
    |
    | Examples:
    | aaaaaaaaaaaa
    | AAAAAAAAAAAA
    | 111111111111
    |
    */

    if (preg_match('/^(.)\1+$/us', $password)) {
        return [
            false,
            'Password is too easy to guess. Please choose a stronger password.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Predictable/common password checks
    |--------------------------------------------------------------------------
    */

    $normalized = mb_strtolower($password, 'UTF-8');

    $blockedFragments = [
        'password',
        'qwerty',
        'letmein',
        'administrator',
        'admin123',
        'password123',
        'apescon123',
        'apesconadmin',
    ];

    foreach ($blockedFragments as $fragment) {
        if (str_contains($normalized, $fragment)) {
            return [
                false,
                'Password contains a common or predictable pattern. Please choose another password.',
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Company-specific predictable values
    |--------------------------------------------------------------------------
    */

    if (str_contains($normalized, 'apescon')) {
        return [
            false,
            'Password should not contain the company name APESCON.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Simple keyboard / numeric sequences
    |--------------------------------------------------------------------------
    */

    $sequences = [
        '123456',
        '654321',
        'abcdef',
        'fedcba',
        'qwerty',
        'asdfgh',
        'zxcvbn',
    ];

    foreach ($sequences as $sequence) {
        if (str_contains($normalized, $sequence)) {
            return [
                false,
                'Password contains an easily predictable sequence.',
            ];
        }
    }

    return [true, ''];
}