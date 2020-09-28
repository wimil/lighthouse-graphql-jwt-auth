<?php

namespace Wimil\LighthouseGraphqlJwtAuth\GraphQL\Mutations;

use Wimil\LighthouseGraphqlJwtAuth\Events\UserLoggedOut;
use Wimil\LighthouseGraphqlJwtAuth\Exceptions\AuthenticationException;

class Logout
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args)
    {
        if (!auth()->guard('api')->check()) {
            throw new AuthenticationException('Not Authenticated', 'Not Authenticated');
        }

        $user = auth()->user();

        auth()->logout();

        event(new UserLoggedOut($user));

        return [
            'status'  => 'TOKEN_REVOKED',
            'message' => __('Your session has been terminated'),
        ];
    }
}
