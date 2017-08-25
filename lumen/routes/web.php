<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$app->get('/', function () {
    return 'ePayco';
});

$app->get('/v1/efecty/consult/{referencia}',
        'EfectyGanaController@consultar_efecty');

$app->post('/generate_pago',
        'BotonController@generate_pago');

$app->post('/generar_usuario',
        'BotonController@generar_usuario');

// Request an access token
$app->post('/oauth/access_token', ['middleware' => 'login', function ()  use ($app){
    return response()->json($app->make('oauth2-server.authorizer')->issueAccessToken());
}]);
