# Customization

This package is pretty customizable, you can extend the mutations or change them completely.

## Customizing the schema

The first thing you may want to customize is the schema, for that you can first publish the default schema bu running the following command

```php
php artisan vendor:publish --provider="Wimil\LighthouseGraphqlJwtAuth\Providers\LighthouseGraphQLPassportServiceProvider"
```

This will publish the schema, and a migration needed for the socialite integration.

Then update the configuration file to point the schema file to the load exported file instead of the one provided by the package.

```php
/*
|--------------------------------------------------------------------------
| GraphQL schema
|--------------------------------------------------------------------------
|
| File path of the GraphQL schema to be used, defaults to null so it uses
| the default location
|
*/
'schema' => base_path('graphql/auth.graphql')
```
From there you can customize the schema to fit your needs.

### Example

Say you need to not ust receive the email and password for registration but also you require the phone number. The first thing you need to make sure is that your user model and table has the necessary field. Once your database and model are set up update the schema so the input field for registration is updated with the phone number.

```graphql
input RegisterInput {
    name: String! @rules(apply: ["required", "string"])
    email: String! @rules(apply: ["required", "email"])
    password: String! @rules(apply: ["required", "confirmed", "min:8"])
    password_confirmation: String!
    phone_number: String! @rules(apply: ["required", "string"])
}
```

This is it! you can now request the phone number as part of registration.

## Customizing the resolvers

```graphql
login(input: LoginInput @spread): AuthPayload! @field(resolver: "App\\GraphQL\\Mutations\\Login")
```
You should now create a resolver ```App\\GraphQL\\Mutations\\Login``` that is used in the login mutation.