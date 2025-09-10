<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    public function issue(string $email, ?int $ttlSeconds = null): string
    {
        $now = time();
        $secret = (string) env('app.jwtSecret');
        $iss = (string) env('app.jwtIssuer', 'sims-ppob');
        $ttl = $ttlSeconds ?? (int) env('app.jwtTTL', 43200); 

        $payload = [
            'iss' => $iss,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttl,
            'email' => $email,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    public function verify(string $token): array
    {
        $secret = (string) env('app.jwtSecret');
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        return (array) $decoded;
    }
}
