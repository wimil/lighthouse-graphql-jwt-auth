<?php

namespace Wimil\LighthouseGraphqlJwtAuth;

use Wimil\LighthouseGraphqlJwtAuth\Notifications\VerifyEmail;

trait MustVerifyEmailGraphQL
{
    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }
}