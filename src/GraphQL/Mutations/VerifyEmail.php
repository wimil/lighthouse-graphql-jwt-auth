<?php

namespace Wimil\LighthouseGraphqlJwtAuth\GraphQL\Mutations;

use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Wimil\LighthouseGraphqlJwtAuth\Exceptions\ValidationException;
use Wimil\LighthouseGraphqlJwtAuth\Traits\AuthHelpers;

class VerifyEmail
{
    use AuthHelpers;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args)
    {
        $decodedToken = json_decode(base64_decode($args['token']));
        $expiration = decrypt($decodedToken->expiration);
        $email = decrypt($decodedToken->hash);

        if (Carbon::parse($expiration) < now()) {
            throw new ValidationException([
                'token' => __('The token is invalid'),
            ], 'Validation Error');
        }

        $model = app(config('auth.providers.users.model'));

        try {
            $user = $model->where('email', $email)->firstOrFail();
            $user->markEmailAsVerified();

            event(new Verified($user));

            $token = auth()->login($user);

            return $this->respondWithToken($token);
        } catch (ModelNotFoundException $e) {
            throw new ValidationException([
                'token' => __('The token is invalid'),
            ], 'Validation Error');
        }
    }
}
