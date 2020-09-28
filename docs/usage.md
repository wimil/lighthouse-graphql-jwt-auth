# Usage

the package will add 8 mutations to your GraphQL API

```graphql
extend type Mutation {
    login(input: LoginInput @spread): AuthPayload! @field(resolver: "Wimil\\LighthouseGraphqlJwtAuth\\GraphQL\\Mutations\\Login@resolve")
    refreshToken: RefreshTokenPayload! @field(resolver: "Wimil\\LighthouseGraphqlJwtAuth\\GraphQL\\Mutations\\RefreshToken@resolve")
    logout: LogoutResponse! @field(resolver: "Wimil\\LighthouseGraphqlJwtAuth\\GraphQL\\Mutations\\Logout@resolve")
    forgotPassword(input: ForgotPasswordInput! @spread): ForgotPasswordResponse! @field(resolver: "Wimil\\LighthouseGraphqlJwtAuth\\GraphQL\\Mutations\\ForgotPassword@resolve")
    resetPassword(input: ResetPasswordInput @spread): ForgotPasswordResponse! @field(resolver: "Wimil\\LighthouseGraphqlJwtAuth\\GraphQL\\Mutations\\ResetPassword@resolve")
    register(input: RegisterInput @spread): RegisterResponse! @field(resolver: "Wimil\\LighthouseGraphqlJwtAuth\\GraphQL\\Mutations\\Register@resolve")
    verifyEmail(input: VerifyEmailInput! @spread): AuthPayload! @field(resolver: "Wimil\\LighthouseGraphqlJwtAuth\\GraphQL\\Mutations\\VerifyEmail@resolve")
    updatePassword(input: UpdatePassword! @spread): UpdatePasswordResponse! @field(resolver: "Wimil\\LighthouseGraphqlJwtAuth\\GraphQL\\Mutations\\UpdatePassword@resolve") @guard(with: ["api"])
}
```
* **login:** Will allow your clients to log in by using email and password.
* **refreshToken:** Refresh a token, which invalidates the current one
* **logout:** Log the user out - which will invalidate the current token and unset the authenticated user.
* **forgotPassword:** Will allow your clients to request the forgot password email.
* **resetPassword:** Will allow your clients to update the forgotten password from the email received.
* **register:** Will allow your clients to register a new user using the default Laravel registration fields
* **verifyEmail:** Will allow your clients to verify the email after they receive a token in the email
* **updatePassword:** Will allow your clients to update the logged in user password - This requires the global **AuthenticateWithApiGuard** registered in the lighthouse config

## Using the email verification

If you want to use the email verification feature that comes with laravel, please follow the instruction in the laravel documentation to configure the model in [https://laravel.com/docs/8.x/verification](https://laravel.com/docs/8.x/verification), once that is done add the following traits

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Wimil\LighthouseGraphqlJwtAuth\MustVerifyEmailGraphQL;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;
    use MustVerifyEmailGraphQL;
}
```

This will add some methods for the email notification to be sent with a token. Use the token in the following mutation.

```graphql
mutation{
  verifyEmail(input:{
    token: "HERE_THE_TOKEN"
  }){
    access_token,
    user{
      id,
      name,
      email
    }
  }
}
```

If the token is valid the tokens will be issued.

> Is very important that you remove the **SendEmailVerificationNotification** listener from your **EventServiceProvider** or 2 emails will be sent.
>
> The token generated for this package to verify the email is different from the one created by default in Laravel due to implementation details. This means that this same token won't work for verifying the user's email with the laravel default views.

## Global Authenticate middleware

>This may not longer be required since Lighthouse introduced the same method in the code in the latest versions.

You can use the [guard](https://lighthouse-php.com/4.16/api-reference/directives.html#guard) to validate that the user is logged in, however this will not set the User property on the context, for this you will have to register the global middleware provided so you can have access to the user in the context object

Set the global middleware ```\Wimil\LighthouseGraphqlJwtAuth\Http\Middleware\AuthenticateWithApiGuard::class``` in the lighthouse php config

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Controls the HTTP route that your GraphQL server responds to.
    | You may set `route` => false, to disable the default route
    | registration and take full control.
    |
    */

    'route' => [
        /*
         * The URI the endpoint responds to, e.g. mydomain.com/graphql.
         */
        'uri' => 'graphql',

        /*
         * Lighthouse creates a named route for convenient URL generation and redirects.
         */
        'name' => 'graphql',

        /*
         *
         * Beware that middleware defined here runs before the GraphQL execution phase,
         * so you have to take extra care to return spec-compliant error responses.
         * To apply middleware on a field level, use the @middleware directive.
         */
        'middleware' => [
            \Nuwave\Lighthouse\Support\Http\Middleware\AcceptJson::class,
            \Wimil\LighthouseGraphqlJwtAuth\Http\Middleware\AuthenticateWithApiGuard::class
        ],
    ],
...
```

This will set the logged in user in the guard for the context object

```php
return $context->user(); // will return the logged in user.
```

## Events emitted by this package

**UserLoggedIn:** This event will be emitted when a new access token is created via the login mutation, this event receives the user model

```php
<?php

namespace Wimil\LighthouseGraphqlJwtAuth\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class UserLoggedIn.
 */
class UserLoggedIn
{
    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * UserLoggedIn constructor.
     *
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}


```

**UserLoggedOut:** This event will be emitted when the logout mutation was called and the token has been revoked, this event receives the user model

```php
<?php

namespace Wimil\LighthouseGraphqlJwtAuth\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class UserLoggedOut.
 */
class UserLoggedOut
{
    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * UserLoggedOut constructor.
     *
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}

```

**Illuminate\Auth\Events\Registered** This event will be emitted when the user is registered via the register mutation or using the socialite integration, this event receives the user model and is part of the Laravel Default Authentication system **UserRefreshedToken** This event will be emitted when the user refresh a token via de refresh token mutation, it received the user model.

```php
<?php

namespace Wimil\LighthouseGraphqlJwtAuth\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class UserRefreshedToken.
 */
class UserRefreshedToken
{
    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * UserRefreshedToken constructor.
     *
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}

```

**PasswordUpdated:** This event will be emmited from the ```updatePassword``` and ```updateForgottenPassword``` mutations after the user has set the new password. This event receives the user model as well.

```php
<?php

namespace Wimil\LighthouseGraphqlJwtAuth\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PasswordUpdated.
 */
class PasswordUpdated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var
     */
    public $user;

    /**
     * PasswordUpdated constructor.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}

```

**ForgotPasswordRequested** This event will be emitted when the user requests an email for forgotten password. In this case only the email is passed to the event.

```php
<?php

namespace Wimil\LighthouseGraphqlJwtAuth\Events;

/**
 * Class ForgotPasswordRequested.
 */
class ForgotPasswordRequested
{
    /**
     * @var string
     */
    public $email;

    /**
     * ForgotPasswordRequested constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }
}

```
