# Default schema

By default the schema is defined internally in the package, once published it will be saved in ```graphql/auth.graphql``` and it looks like this:

```graphql
type User {
    id: ID!
    name: String!
    email: String!
}

type AuthPayload {
    access_token: String
    expires_in: Int
    token_type: String
    user: User
}

type RefreshTokenPayload {
    access_token: String!
    expires_in: Int!
    token_type: String!
}

type LogoutResponse {
    status: String!
    message: String
}

type ForgotPasswordResponse {
    status: String!
    message: String
}

type RegisterResponse {
    tokens: AuthPayload
    status: RegisterStatuses!
}

type UpdatePasswordResponse {
    status: String!
    message: String
}

enum RegisterStatuses {
    MUST_VERIFY_EMAIL
    SUCCESS
}

input LoginInput {
    email: String! @rules(apply: ["required", "email"])
    password: String! @rules(apply: ["required"])
}

input ForgotPasswordInput {
    email: String! @rules(apply: ["required", "email"])
}

input ResetPasswordInput {
    email: String! @rules(apply: ["required", "email"])
    token: String! @rules(apply: ["required", "string"])
    password: String! @rules(apply: ["required", "confirmed", "min:8"])
    password_confirmation: String!
}

input RegisterInput {
    name: String! @rules(apply: ["required", "string"])
    email: String! @rules(apply: ["required", "email", "unique:users,email"])
    password: String! @rules(apply: ["required", "confirmed", "min:8"])
    password_confirmation: String!
}


input VerifyEmailInput {
    token: String!
}

input UpdatePassword {
    old_password: String!
    password: String! @rules(apply: ["required", "confirmed", "min:8"])
    password_confirmation: String!
}

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

In the configuration file you can now set the schema file to be used for the exported one like this:

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

This will allow you to change the schema and resolvers if needed.

