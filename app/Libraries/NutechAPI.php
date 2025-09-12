<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Services;
use RuntimeException;

class NutechAPI
{
    protected $client;
    protected $headers;
    protected $base;

    public function __construct()
    {
        $this->base = rtrim((string) (env('app.apiBaseURL') ?: getenv('APP_API_BASEURL')), '/');
        if ($this->base === '') {
            throw new RuntimeException('Missing apiBaseURL in .env');
        }
        $this->headers = [
            'Accept'       => 'application/json',
            // 'Content-Type' => 'application/json',
        ];
        $this->client = Services::curlrequest([
            'base_uri'    => $this->base,
            'headers'     => $this->headers,
            'http_errors' => false,
            'timeout'     => 20,
            'verify'      => false,
        ]);
    }

    protected function authHeaders(string $token): array
    {
        $h = $this->headers;
        $h['Authorization'] = 'Bearer ' . $token;
        return $h;
    }

    public function updateProfile(string $token, array $payload): array
    {
        $res = $this->client->put(
            $this->base . '/profile/update',
            ['headers' => $this->authHeaders($token), 'json' => $payload]
        );
        return [
            'status' => (int)$res->getStatusCode(),
            'body'   => json_decode((string)$res->getBody(), true) ?? [],
        ];
    }

    public function updateProfileImage(string $token, UploadedFile $file): array
    {
        $url     = $this->base . '/profile/image';
        $tmpPath = $file->getTempName();
        $name    = $file->getClientName();
        $mime    = $file->getMimeType() ?: 'application/octet-stream';

        $curlFile = new \CURLFile($tmpPath, $mime, $name);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'PUT',
            CURLOPT_POSTFIELDS     => ['file' => $curlFile],
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => 0, 'body' => ['status' => 1, 'message' => 'Curl error: ' . $error]];
        }

        $body = json_decode((string) $response, true);
        if (! is_array($body)) {
            $body = ['status' => 1, 'message' => 'Non-JSON response', 'raw' => (string) $response];
        }

        return ['status' => $httpCode, 'body' => $body];
    }

    public function register(array $payload): array
    {
        $res = $this->client->post($this->base . '/registration', ['json' => $payload]);
        return ['status' => (int)$res->getStatusCode(), 'body' => json_decode((string)$res->getBody(), true) ?? []];
    }

    public function login(array $payload): array
    {
        $res = $this->client->post($this->base . '/login', ['json' => $payload]);
        return ['status' => (int)$res->getStatusCode(), 'body' => json_decode((string)$res->getBody(), true) ?? []];
    }

    public function profile(string $token): array
    {
        $res = $this->client->get($this->base . '/profile', ['headers' => $this->authHeaders($token)]);
        return ['status' => (int)$res->getStatusCode(), 'body' => json_decode((string)$res->getBody(), true) ?? []];
    }

    public function balance(string $token): array
    {
        $res = $this->client->get($this->base . '/balance', ['headers' => $this->authHeaders($token)]);
        return ['status' => (int)$res->getStatusCode(), 'body' => json_decode((string)$res->getBody(), true) ?? []];
    }

    public function services(string $token): array
    {
        $res = $this->client->get($this->base . '/services', ['headers' => $this->authHeaders($token)]);
        return ['status' => (int)$res->getStatusCode(), 'body' => json_decode((string)$res->getBody(), true) ?? []];
    }

    public function serviceDetail(string $token, string $serviceCode): array
    {
        $res = $this->client->get($this->base . '/services/' . $serviceCode, ['headers' => $this->authHeaders($token)]);
        $out = ['status' => (int)$res->getStatusCode(), 'body' => json_decode((string)$res->getBody(), true) ?? []];

        if ($out['status'] === 404 || empty($out['body']['data'])) {
            $all = $this->services($token);
            foreach ($all['body']['data'] ?? [] as $s) {
                if (($s['service_code'] ?? null) === $serviceCode) {
                    $out = ['status' => 200, 'body' => ['status' => 0, 'data' => $s]];
                    break;
                }
            }
        }
        return $out;
    }

    public function banners(string $token): array
    {
        $res = $this->client->get($this->base . '/banner', ['headers' => $this->authHeaders($token)]);
        return ['status' => (int)$res->getStatusCode(), 'body' => json_decode((string)$res->getBody(), true) ?? []];
    }

    public function topup(string $token, int $amount): array
    {
        $res = $this->client->post($this->base . '/topup', [
            'headers' => $this->authHeaders($token),
            'json'    => ['top_up_amount' => $amount],
        ]);
        return ['status' => (int)$res->getStatusCode(), 'body' => json_decode((string)$res->getBody(), true) ?? []];
    }

    public function transaction(string $token, string $serviceCode): array
    {
        $res = $this->client->post($this->base . '/transaction', [
            'headers' => $this->authHeaders($token),
            'json'    => ['service_code' => $serviceCode],
        ]);
        return ['status' => (int)$res->getStatusCode(), 'body' => json_decode((string)$res->getBody(), true) ?? []];
    }

    public function history(string $token, int $offset = 0, int $limit = 5): array
    {
        $offset = max(0, $offset);
        $limit  = max(1, $limit);

        $headers = $this->authHeaders($token);
        $res  = $this->client->get($this->base . '/history?limit=' . $limit . '&offset=' . $offset, [
            'headers' => $headers
        ]);
        $code = (int) $res->getStatusCode();
        $body = json_decode((string) $res->getBody(), true) ?? [];
        if ($code === 404) {
            $res  = $this->client->get($this->base . '/transaction/history?limit=' . $limit . '&offset=' . $offset, [
                'headers' => $headers
            ]);
            $code = (int) $res->getStatusCode();
            $body = json_decode((string) $res->getBody(), true) ?? [];
        }
        if ($code === 404) {
            $res  = $this->client->get($this->base . '/transactions?limit=' . $limit . '&offset=' . $offset, [
                'headers' => $headers
            ]);
            $code = (int) $res->getStatusCode();
            $body = json_decode((string) $res->getBody(), true) ?? [];
        }
        return ['status' => $code, 'body' => $body];
    }
}
