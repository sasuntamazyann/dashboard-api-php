<?php

namespace Dashboard\DashboardApi\Integrations\Firebase\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtDecoder
{
    private array $jwtConfig;

    public function __construct(array $jwtConfig)
    {
        $this->jwtConfig = $jwtConfig;
    }

    public function decode(string $jwt): array
    {
        $decoded = JWT::decode($jwt, new Key($this->jwtConfig['key'], $this->jwtConfig['algo']));

        return json_decode(json_encode($decoded), true);
    }
}