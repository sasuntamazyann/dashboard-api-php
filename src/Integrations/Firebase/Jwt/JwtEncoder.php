<?php

namespace Dashboard\DashboardApi\Integrations\Firebase\Jwt;

use Firebase\JWT\JWT;

class JwtEncoder
{
    private array $jwtConfig;

    public function __construct(array $jwtConfig)
    {
        $this->jwtConfig = $jwtConfig;
    }

    public function encode(array $customPayload): string
    {
        $registeredClaims = [
            'iss' => $this->jwtConfig['issuer'],
            'exp' => time() + $this->jwtConfig['expiration_time_seconds'],
        ];

        return JWT::encode(
            array_merge($registeredClaims, $customPayload),
            $this->jwtConfig['key'],
            $this->jwtConfig['algo']
        );
    }
}