<?php

namespace Wimil\LighthouseGraphqlJwtAuth\GraphQL\Mutations;

use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Hash;
use Wimil\LighthouseGraphqlJwtAuth\Events\PasswordUpdated;
use Wimil\LighthouseGraphqlJwtAuth\Exceptions\ValidationException;

class UpdatePassword
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {
        $user = auth()->user();

        if (!Hash::check($args['old_password'], $user->password)) {
            throw new ValidationException([
                'password' => __('Current password is incorrect'),
            ], 'Validation Exception');
        }
        $user->password = Hash::make($args['password']);
        $user->save();
        event(new PasswordUpdated($user));

        return [
            'status'  => 'PASSWORD_UPDATED',
            'message' => __('Your password has been updated'),
        ];
    }
}
