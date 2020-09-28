<?php

namespace Wimil\LighthouseGraphqlJwtAuth\GraphQL\Mutations;

use Wimil\LighthouseGraphqlJwtAuth\Exceptions\AuthenticationException;
use Wimil\LighthouseGraphqlJwtAuth\Traits\AuthHelpers;
use Wimil\LighthouseGraphqlJwtAuth\Events\UserRefreshedToken;

class RefreshToken
{
    use AuthHelpers;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args)
    {
        if (!auth()->guard('api')->check()) {
            throw new AuthenticationException('Not Authenticated', 'Not Authenticated');
        }

        $token = auth()->refresh();

        event(new UserRefreshedToken(auth()->user()));

        return $this->respondWithToken($token);
    }
}
