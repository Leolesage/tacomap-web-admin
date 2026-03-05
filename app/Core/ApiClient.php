<?php
declare(strict_types=1);

namespace App\Core;

final class ApiClient
{
    private string $publicUrl;
    private string $internalUrl;
    private int $timeout;

    public function __construct(array $config)
    {
        $this->publicUrl = rtrim($config['public_url'] ?? '', '/');
        $this->internalUrl = rtrim($config['internal_url'] ?? '', '/');
        $this->timeout = (int)($config['timeout_seconds'] ?? 12);
    }

    public function login(string $email, string $password): array
    {
        $url = $this->internalUrl . '/api/auth/login';
        $res = $this->request('POST', $url, [
            'Content-Type: application/json',
        ], json_encode([
            'email' => $email,
            'password' => $password,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        if (!$res['ok']) {
            return $res;
        }

        return [
            'ok' => true,
            'token' => (string)($res['data']['token'] ?? ''),
            'expires_in' => (int)($res['data']['expires_in'] ?? 3600),
        ];
    }

    public function listTacosPlaces(int $page, int $limit, string $q, string $token = ''): array
    {
        $query = [
            'page' => (string)max(1, $page),
            'limit' => (string)max(1, $limit),
        ];
        if ($q !== '') {
            $query['q'] = $q;
        }

        $url = $this->internalUrl . '/api/tacos-places?' . http_build_query($query);
        $headers = [];
        if ($token !== '') {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        return $this->request('GET', $url, $headers);
    }

    public function getTacosPlace(int $id, string $token = ''): array
    {
        $url = $this->internalUrl . '/api/tacos-places/' . $id;
        $headers = [];
        if ($token !== '') {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        return $this->request('GET', $url, $headers);
    }

    public function createTacosPlace(string $token, array $fields, array $photo): array
    {
        $tmp = $photo['tmp_name'] ?? '';
        if ($tmp === '' || !is_file($tmp)) {
            return ['ok' => false, 'error' => 'Invalid upload file'];
        }

        $payload = $this->toMultipartFields($fields);
        $payload['photo'] = curl_file_create($tmp, $photo['type'] ?? 'image/jpeg', $photo['name'] ?? 'photo.jpg');

        return $this->request('POST', $this->internalUrl . '/api/tacos-places', [
            'Authorization: Bearer ' . $token,
        ], $payload);
    }

    public function updateTacosPlace(string $token, int $id, array $fields, ?array $photo): array
    {
        $payload = $this->toMultipartFields($fields);
        if ($photo !== null) {
            $tmp = $photo['tmp_name'] ?? '';
            if ($tmp === '' || !is_file($tmp)) {
                return ['ok' => false, 'error' => 'Invalid upload file'];
            }
            $payload['photo'] = curl_file_create($tmp, $photo['type'] ?? 'image/jpeg', $photo['name'] ?? 'photo.jpg');
        }

        return $this->request('PUT', $this->internalUrl . '/api/tacos-places/' . $id, [
            'Authorization: Bearer ' . $token,
        ], $payload);
    }

    public function deleteTacosPlace(string $token, int $id): array
    {
        return $this->request('DELETE', $this->internalUrl . '/api/tacos-places/' . $id, [
            'Authorization: Bearer ' . $token,
        ]);
    }

    public function downloadTacosPlacePdf(string $token, int $id): array
    {
        return $this->request('GET', $this->internalUrl . '/api/tacos-places/' . $id . '/pdf', [
            'Authorization: Bearer ' . $token,
        ], null, false);
    }

    public function publicImageUrl(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/');
        if ($relativePath === '') {
            return '';
        }
        if (str_starts_with($relativePath, 'http://') || str_starts_with($relativePath, 'https://')) {
            return $relativePath;
        }
        return $this->publicUrl . '/' . $relativePath;
    }

    private function toMultipartFields(array $fields): array
    {
        $result = [];
        foreach ($fields as $key => $value) {
            $result[(string)$key] = (string)$value;
        }
        return $result;
    }

    private function request(string $method, string $url, array $headers = [], mixed $body = null, bool $decodeJson = true): array
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $raw = curl_exec($ch);
        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['ok' => false, 'error' => $error ?: 'Request failed'];
        }

        $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $rawHeaders = substr($raw, 0, $headerSize);
        $bodyString = substr($raw, $headerSize);
        curl_close($ch);

        $responseHeaders = $this->parseHeaders($rawHeaders);
        $contentType = strtolower((string)($responseHeaders['content-type'] ?? ''));

        $data = [];
        if ($decodeJson && str_contains($contentType, 'application/json')) {
            $decoded = json_decode($bodyString, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        if ($status >= 200 && $status < 300) {
            return [
                'ok' => true,
                'status' => $status,
                'data' => $data,
                'body' => $bodyString,
                'headers' => $responseHeaders,
            ];
        }

        $message = 'API error (' . $status . ')';
        if (isset($data['error'])) {
            $message = (string)$data['error'];
        }
        if (isset($data['fields']) && is_array($data['fields'])) {
            return [
                'ok' => false,
                'error' => $message,
                'status' => $status,
                'fields' => $data['fields'],
            ];
        }

        return ['ok' => false, 'error' => $message, 'status' => $status];
    }

    private function parseHeaders(string $rawHeaders): array
    {
        $headers = [];
        $lines = preg_split('/\r\n|\n|\r/', trim($rawHeaders)) ?: [];
        foreach ($lines as $line) {
            if (!str_contains($line, ':')) {
                continue;
            }
            [$name, $value] = explode(':', $line, 2);
            $headers[strtolower(trim($name))] = trim($value);
        }
        return $headers;
    }
}
