<?php

namespace Wimil\LighthouseGraphqlJwtAuth\Traits;

use Wimil\LighthouseGraphqlJwtAuth\Factories\AuthModelFactory;


trait AuthHelpers
{
    protected function respondWithToken($token, $user = null)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];
    }

    protected function getAuthModelFactory(): AuthModelFactory
    {
        return app(AuthModelFactory::class);
    }
}
