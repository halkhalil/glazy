<?php

use Dingo\Api\Routing\Router;

// DAU header('Access-Control-Allow-Origin: *');
// header( 'Access-Control-Allow-Headers: Authorization, Content-Type' );

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api) {

        $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');

        // DAU
        // $api->get('login/{provider}',   'App\\Api\\V1\\Controllers\\LoginController@redirectToProvider');
        $api->post('login/{provider}/callback', 'App\\Api\\V1\\Controllers\\LoginController@loginSocial');
        $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');
        // $app->post('login/credentials', 'App\\Api\\V1\\Controllers\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');

        $api->post('logout', 'App\\Api\\V1\\Controllers\\LogoutController@logout');
        $api->post('refresh', 'App\\Api\\V1\\Controllers\\RefreshController@refresh');
        $api->get('user', 'App\\Api\\V1\\Controllers\\UserController@user');
    });

    $api->group(['prefix' => 'recipes'], function(Router $api) {
        $api->get('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@show')->name('show');

        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->patch('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@update')->name('update');
            $api->delete('{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@destroy')->name('destroy');
            $api->get('/{id}/copy', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@copy')->name('copy');
            $api->get('/{id}/publish', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@publish')->name('publish');
            $api->get('/{id}/unpublish', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@unpublish')->name('unpublish');
        });
    });

    $api->group(['prefix' => 'materials'], function(Router $api) {
        $api->get('/editRecipeMaterialList/{id?}', 'App\\Api\\V1\\Controllers\\Glazy\\PrimitiveMaterialController@editRecipeMaterialList')->name('editRecipeMaterialList');
    });

    $api->group(['prefix' => 'usermaterials'], function(Router $api) {
        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->get('/', 'App\\Api\\V1\\Controllers\\Glazy\\UserMaterialController@index')->name('index');
            $api->get('/editList/{id?}', 'App\\Api\\V1\\Controllers\\Glazy\\UserMaterialController@editMaterialList')->name('editList');
        });
    });

    $api->group(['prefix' => 'search'], function(Router $api) {
        $api->get('/', 'App\\Api\\V1\\Controllers\\Glazy\\SearchController@index')->name('index');
        $api->get('/nearestSiAl', 'App\\Api\\V1\\Controllers\\Glazy\\SearchController@nearestSiAl')->name('nearestSiAl');
        $api->get('/similarBaseComponents/{material_id}', 'App\\Api\\V1\\Controllers\\Glazy\\SearchController@similarBaseComponents')->name('similarBaseComponents');
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to protected resources granted! You are seeing this text as you provided the token correctly.'
            ]);
        });

        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);
    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });
});
