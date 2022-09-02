<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use App\Models\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Loader Alias
        $loader = AliasLoader::getInstance();
        $loader->alias(\Laravel\Sanctum\NewAccessToken::class, \App\Models\Sanctum\NewAccessToken::class);

        Sanctum::authenticateAccessTokensUsing(
            static function (PersonalAccessToken $accessToken, bool $is_valid) {
                return $accessToken->expired_at ? $is_valid && !$accessToken->expired_at->isPast() : $is_valid;
            }
        );

        if($this->app->environment('production')) {
            if ( config('app.force_https_in_prod') == true ) {
                URL::forceScheme('https');
            }
        }
    }
}
