<?php

namespace Netflex\Toolbox\Providers;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Netflex\Toolbox\Http\Controllers\StructureWebhookController;
use Netflex\Toolbox\Middleware\AddTrailingSlash;
use Netflex\Toolbox\Middleware\RemoveTrailingSlash;
use Netflex\Toolbox\Validations\RecaptchaV2;

class ToolboxProvider extends \Illuminate\Support\ServiceProvider
{

    public function register()
    {
        $this->registerTrailingSlashHelpers();


    }

    public function boot()
    {
        Route::post('/.well-known/netflex/structure-webhooks', [StructureWebhookController::class, 'process'])
            ->withoutMiddleware(VerifyCsrfToken::class);
        $this->bootRecaptcha();
        $this->bootOrderCommandConfigs();
    }


    public function registerTrailingSlashHelpers()
    {
        app()->bind('add-slash', AddTrailingSlash::class);
        app()->bind('remove-slash', RemoveTrailingSlash::class);
    }

    private function bootOrderCommandConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/indexers.php", "indexers");

        $this->publishes(
            [
                __DIR__ . "/../../config/indexers.php" => $this->app->configPath('indexers.php'),
            ],
            'toolbox-config',
        );

    }

    /**
     * @return void
     */
    public function bootRecaptcha(): void
    {
        Blade::componentNamespace('Netflex\\Toolbox\\Views\\Components', 'toolbox');
        View::addNamespace('toolbox', __DIR__ . "/../../views");
        Validator::extend('recaptcha-v2', RecaptchaV2::class);

        $this->mergeConfigFrom(__DIR__ . "/../../config/recaptcha-v2.php", "recaptcha-v2");

        $this->publishes(
            [
                __DIR__ . "/../../config/recaptcha-v2.php" => $this->app->configPath('recaptcha-v2.php'),
            ], 'toolbox-recaptcha-v2-config',
        );
    }

}
