<?php

use Dingo\Api\Routing\Router;

// DAU header('Access-Control-Allow-Origin: *');
// header( 'Access-Control-Allow-Headers: Authorization, Content-Type' );

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api) {

        $api->post('register', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');

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

        $api->post('changePassword', 'App\\Api\\V1\\Controllers\\UserController@changePassword');

        $api->patch('profile', 'App\\Api\\V1\\Controllers\\Glazy\\UserProfileController@update');
        $api->post('createAvatar', 'App\\Api\\V1\\Controllers\\Glazy\\UserProfileController@createAvatar');

    });

    $api->group(['prefix' => 'recipes'], function(Router $api) {
        $api->get('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@show')->name('show');

        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->post('/', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@store')->name('store');
            $api->patch('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@update')->name('update');
            $api->delete('{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@destroy')->name('destroy');
            $api->get('/{id}/copy', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@copy')->name('copy');
            $api->get('/{id}/publish', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@publish')->name('publish');
            $api->get('/{id}/unpublish', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@unpublish')->name('unpublish');
            $api->get('/{id}/archive', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@archive')->name('archive');
            $api->get('/{materialId}/image/{imageId}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@thumbnail')->name('thumbnail');
            $api->get('/{id}/export/{exportType}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialController@export')->name('export');
        });
    });

    $api->group(['prefix' => 'materialmaterials'], function(Router $api) {
        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->patch('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialMaterialController@update')->name('update');
            $api->post('/', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialMaterialController@store')->name('store');
        });
    });

    $api->group(['prefix' => 'materials'], function(Router $api) {
        $api->get('/editRecipeMaterialList/{id?}', 'App\\Api\\V1\\Controllers\\Glazy\\PrimitiveMaterialController@editRecipeMaterialList')->name('editRecipeMaterialList');
    });

    $api->group(['prefix' => 'usermaterials'], function(Router $api) {
        $api->get('/editList/{id?}', 'App\\Api\\V1\\Controllers\\Glazy\\UserMaterialController@editMaterialList')->name('editList');
        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->get('/addMaterial/{materialId}', 'App\\Api\\V1\\Controllers\\Glazy\\UserMaterialController@addMaterial')->name('addMaterial');
            $api->get('/', 'App\\Api\\V1\\Controllers\\Glazy\\UserMaterialController@index')->name('index');
            $api->delete('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\UserMaterialController@destroy')->name('destroy');
        });
    });

    $api->group(['prefix' => 'materialimages'], function(Router $api) {
        $api->get('/userList/{user_id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialImageController@userList')->name('userList');
        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->post('/', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialImageController@store')->name('store');
            $api->patch('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialImageController@update')->name('update');
            $api->delete('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialImageController@destroy')->name('destroy');
        });
    });

    $api->group(['prefix' => 'materialreviews'], function(Router $api) {
        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->post('/', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialReviewController@store')->name('store');
            $api->patch('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialReviewController@update')->name('update');
            $api->delete('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialReviewController@destroy')->name('destroy');
        });
    });

    $api->group(['prefix' => 'materialcomments'], function(Router $api) {
        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->post('/', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialCommentController@store')->name('store');
            $api->patch('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialCommentController@update')->name('update');
            $api->delete('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\MaterialCommentController@destroy')->name('destroy');
        });
    });

    $api->group(['prefix' => 'collectionmaterials'], function(Router $api) {
        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->post('/', 'App\\Api\\V1\\Controllers\\Glazy\\CollectionMaterialController@store')->name('store');
            $api->get('/delete/{collection_id}/{material_id}', 'App\\Api\\V1\\Controllers\\Glazy\\CollectionMaterialController@destroy')->name('destroy');
        });
    });

    $api->group(['prefix' => 'collections'], function(Router $api) {
        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->delete('/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\CollectionController@destroy')->name('destroy');
        });
    });

    $api->group(['prefix' => 'search'], function(Router $api) {
        $api->get('/', 'App\\Api\\V1\\Controllers\\Glazy\\SearchController@index')->name('index');
        $api->get('/nearestXY', 'App\\Api\\V1\\Controllers\\Glazy\\SearchController@nearestXY')->name('nearestXY');
        $api->post('/similarMaterials/{id?}', 'App\\Api\\V1\\Controllers\\Glazy\\SearchController@similarMaterials')->name('similarMaterials');
        $api->post('/containsMaterials', 'App\\Api\\V1\\Controllers\\Glazy\\SearchController@containsMaterials')->name('containsMaterials');
        $api->get('/similarBaseComponents/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\SearchController@similarBaseComponents')->name('similarBaseComponents');
        $api->get('/similarUnityFormula/{id}', 'App\\Api\\V1\\Controllers\\Glazy\\SearchController@similarUnityFormula')->name('similarUnityFormula');
    });

    $api->group(['prefix' => 'notifications'], function(Router $api) {
        $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
            $api->get('/markAsRead', 'App\\Api\\V1\\Controllers\\Glazy\\NotificationController@markAsRead')->name('markAsRead');
        });
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
