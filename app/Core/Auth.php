<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function check(): bool
    {
        $session = $_SESSION['admin_auth'] ?? null;
        if (!is_array($session)) {
            return false;
        }

        $token = (string)($session['token'] ?? '');
        $expiresAt = (int)($session['expires_at'] ?? 0);
        if ($token === '' || $expiresAt <= time()) {
            self::logout();
            return false;
        }

        return true;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'email' => (string)($_SESSION['admin_auth']['email'] ?? ''),
        ];
    }

    public static function token(): string
    {
        if (!self::check()) {
            return '';
        }
        return (string)($_SESSION['admin_auth']['token'] ?? '');
    }

    public static function login(string $email, string $token, int $expiresIn): void
    {
        session_regenerate_id(true);
        $_SESSION['admin_auth'] = [
            'email' => $email,
            'token' => $token,
            'expires_at' => time() + max(60, $expiresIn - 10),
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_auth']);
    }

    public static function handle(Request $request): bool
    {
        if (!self::check()) {
            Response::redirect('/login');
            return false;
        }
        return true;
    }
}
