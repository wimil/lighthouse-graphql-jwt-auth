<?php

namespace Wimil\LighthouseGraphqlJwtAuth\GraphQL\Mutations;

use Wimil\LighthouseGraphqlJwtAuth\Traits\AuthHelpers;
use Wimil\LighthouseGraphqlJwtAuth\Events\UserLoggedIn;
use Wimil\LighthouseGraphqlJwtAuth\Exceptions\AuthenticationException;

class Login
{
    use AuthHelpers;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args)
    {
        $credentials = [
            'email' => $args['email'],
            'password' => $args['password']
        ];

        if (!$token = auth()->attempt($credentials)) {
            throw new AuthenticationException("Unauthorized", "Incorrect email or password.");
        }

        event(new UserLoggedIn(auth()->user()));

        return $this->respondWithToken($token);
    }
}
