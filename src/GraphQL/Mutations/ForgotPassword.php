<?php

namespace Wimil\LighthouseGraphqlJwtAuth\GraphQL\Mutations;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Wimil\LighthouseGraphqlJwtAuth\Events\ForgotPasswordRequested;

class ForgotPassword
{
    use SendsPasswordResetEmails;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function resolve($_, array $args)
    {
        $response = $this->broker()->sendResetLink(['email' => $args['email']]);
        if ($response == Password::RESET_LINK_SENT) {
            event(new ForgotPasswordRequested($args['email']));

            return [
                'status'  => 'EMAIL_SENT',
                'message' => __($response),
            ];
        }

        return [
            'status'  => 'EMAIL_NOT_SENT',
            'message' => __($response),
        ];
    }
}
