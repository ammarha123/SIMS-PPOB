<?php

namespace App\Libraries;

use Config\Services;
use RuntimeException;

class NutechAPI
{
    protected $client;
    protected $headers;
    protected $base;

    public function __construct()
    {
        $this->base = rtrim((string) env('app.apiBaseURL'), '/');
        if ($this->base === '') {
            throw new RuntimeException('Missing apiBaseURL in .env');
        }
        $this->headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $this->client = Services::curlrequest([
            'baseURI'     => $this->base,
            'headers'     => $this->headers,
            'http_errors' => false,
            'timeout'     => 20,
            'verify'      => false,
        ]);
    }

    public function register(array $payload): array
    {
        $res    = $this->client->post($this->base . '/registration', ['json' => $payload]);
        $status = (int) $res->getStatusCode();
        $body   = json_decode((string) $res->getBody(), true);
        return ['status' => $status, 'body' => is_array($body) ? $body : [], 'sent' => true];
    }

     public function login(array $payload): array
    {
        $res    = $this->client->post($this->base . '/login', ['json' => $payload]);
        $status = (int) $res->getStatusCode();
        $body   = json_decode((string) $res->getBody(), true);
        return ['status' => $status, 'body' => is_array($body) ? $body : []];
    }
}
