<?php

namespace Shipmate\Shipmate\Support;

use Google\Auth\AccessToken;

class OpenId
{
    public static function new(): static
    {
        return app(static::class);
    }

    public function validateToken(string $token, string $audience): void
    {
        (new AccessToken)->verify(
            token: $token,
            options: [
                'audience' => $audience,
                'throwException' => true,
            ]
        );
    }
}
