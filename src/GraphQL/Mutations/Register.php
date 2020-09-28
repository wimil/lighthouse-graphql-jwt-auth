<?php

namespace Wimil\LighthouseGraphqlJwtAuth\GraphQL\Mutations;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Wimil\LighthouseGraphqlJwtAuth\Traits\AuthHelpers;

class Register
{
    use AuthHelpers;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args)
    {
        $model = $this->createAuthModel($args);

        $this->validateAuthModel($model);

        if ($model instanceof MustVerifyEmail) {
            $model->sendEmailVerificationNotification();

            event(new Registered($model));

            return [
                'tokens' => [],
                'status' => 'MUST_VERIFY_EMAIL',
            ];
        }

        $token = auth()->login($model);
        event(new Registered($model));
        return [
            'tokens' => $this->respondWithToken($token),
            'status' => 'SUCCESS'
        ];
    }

    private function validateAuthModel($model): void
    {
        $authModelClass = $this->getAuthModelFactory()->getClass();

        if ($model instanceof $authModelClass) {
            return;
        }

        throw new \RuntimeException("Auth model must be an instance of {$authModelClass}");
    }

    protected function createAuthModel(array $data): Model
    {
        $input = collect($data)->except('password_confirmation')->toArray();
        $input['password'] = Hash::make($input['password']);

        return $this->getAuthModelFactory()->create($input);
    }
}
