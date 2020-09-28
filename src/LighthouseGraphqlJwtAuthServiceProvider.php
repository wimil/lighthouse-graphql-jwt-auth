<?php

namespace Wimil\LighthouseGraphqlJwtAuth;

use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Events\BuildSchemaString;
use Wimil\LighthouseGraphqlJwtAuth\Contracts\AuthModelFactory as AuthModelFactoryContract;
use Wimil\LighthouseGraphqlJwtAuth\Factories\AuthModelFactory;

class LighthouseGraphqlJwtAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AuthModelFactoryContract::class, AuthModelFactory::class);

        $this->publishThings();

        app('events')->listen(
            BuildSchemaString::class,
            function (): string {
                if (config('lighthouse-graphql-jwt.schema')) {
                    return file_get_contents(config('lighthouse-graphql-jwt.schema'));
                }

                return file_get_contents(__DIR__ . '/../graphql/auth.graphql');
            }
        );
    }

    protected function publishThings()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lighthouse-graphql-jwt.php', 'lighthouse-graphql-jwt');

        $this->publishes([
            __DIR__ . '/../config/lighthouse-graphql-jwt.php' => config_path('lighthouse-graphql-jwt.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../graphql/auth.graphql' => base_path('graphql/auth.graphql'),
        ], 'schema');
    }
}
