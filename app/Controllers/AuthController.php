<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\ApiClient;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;

final class AuthController
{
    private array $config;
    private ApiClient $apiClient;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->apiClient = new ApiClient($config['api'] ?? []);
    }

    public function showLogin(Request $request): void
    {
        if (Auth::check()) {
            Response::redirect('/admin/tacos-places');
        }

        $html = View::render('auth/login', [
            'title' => 'Admin Login',
            'error' => null,
            'email' => '',
        ]);
        Response::html($html);
    }

    public function login(Request $request): void
    {
        $email = trim((string)$request->postParam('email', ''));
        $password = (string)$request->postParam('password', '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $html = View::render('auth/login', [
                'title' => 'Admin Login',
                'error' => 'Invalid credentials.',
                'email' => $email,
            ]);
            Response::html($html, 401);
        }

        $res = $this->apiClient->login($email, $password);
        if (!$res['ok'] || empty($res['token'])) {
            $html = View::render('auth/login', [
                'title' => 'Admin Login',
                'error' => 'Invalid credentials.',
                'email' => $email,
            ]);
            Response::html($html, 401);
        }

        Auth::login($email, (string)$res['token'], (int)($res['expires_in'] ?? 3600));
        Response::redirect('/admin/tacos-places');
    }

    public function logout(Request $request): void
    {
        Auth::logout();
        Response::redirect('/tacos-places');
    }
}
