# Pre requisites

This package requires you to install [Jwt-auth](https://jwt-auth.readthedocs.io/en/develop/quick-start/) prior to use it


# Installation

To install run

```
composer require wimil/lighthouse-graphql-jwt-auth
```

ServiceProvider will be attached automatically

Run this command to publish the schema and configuration file

```
php artisan vendor:publish --provider="Wimil\LighthouseGraphqlJwtAuth\LighthouseGraphqlJwtAuthServiceProvider"
```

You are done with the installation!

