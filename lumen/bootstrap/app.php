<?php
require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}
class Application extends \Laravel\Lumen\Application
{
    /**
     * Get the path to the application configuration files.
     *
     * @param string $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app('path') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

class_alias('Illuminate\Support\Facades\Config', 'Config');

$app = new Application(
    realpath(__DIR__.'/../')
);

 $app->withFacades();

 $app->withEloquent();
/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

//$app = new Laravel\Lumen\Application(
//    realpath(__DIR__.'/../')
//);


/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/
$app->middleware([
    //    App\Http\Middleware\ExampleMiddleware::class
    \LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware::class

]);

$app->routeMiddleware([
    // 'auth' => App\Http\Middleware\Authenticate::class,
    'oauth' => \LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware::class,
    // 'oauth-user'=> \LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware::class,
    'authorize' => App\Http\Middleware\Authorize::class,
    'login' => \App\Http\Middleware\Login::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Way\Generators\GeneratorsServiceProvider::class);
$app->register(Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
$app->register(Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider::class);
$app->register(Appzcoder\LumenRoutesList\RoutesCommandServiceProvider::class);
$app->register(\LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider::class);
$app->register(\LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider::class); 
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
if (env('APP_ENV') != 'production' || env('APP_ENV') == 'local') {
    $app->register(MichaelB\LumenMake\LumenMakeServiceProvider::class);
}
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/
$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../routes/web.php';
});

return $app;
