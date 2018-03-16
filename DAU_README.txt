#################################
verify php.ini
#################################
always_populate_raw_post_data = -1

#################################
composer install
#################################

#################################
npm install
#################################

#################################
Update .env
#################################

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=glazyb
DB_USERNAME=glazyb
DB_PASSWORD=???

# Socialite Providers
FACEBOOK_CLIENT_ID=???
FACEBOOK_CLIENT_SECRET=???
FACEBOOK_REDIRECT=http://localhost:8888/login/facebook

GOOGLE_CLIENT_ID=???.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=???
GOOGLE_REDIRECT=http://localhost:8888/login/google

#################################
php artisan key:generate
#################################

#################################
php artisan migrate
#################################

#################################
php artisan db:seed
#################################

#################################
npm run dev
#################################

#################################
test login:
admin@admin.com
1234
#################################

#################################
Migrations
#################################
2016_11_12_000050_add_columns_to_users_table.php
2016_11_12_000100_create_orton_cones_table.php
2016_11_12_000200_create_surface_types_table.php
2016_11_12_000300_create_transparency_types_table.php
2016_11_12_000400_create_atmospheres_table.php
2016_11_12_000500_create_material_types_table.php
2016_11_12_000600_create_materials_table.php
2016_11_12_000650_create_material_hazards_table.php
2016_11_12_000700_create_material_analyses_table.php
2016_11_12_000800_create_material_atmospheres_table.php
2016_11_12_000900_create_material_materials_table.php
2016_11_12_001000_create_material_images_table.php
2016_11_12_001100_create_material_collections_tables.php
2016_11_12_001200_create_user_profiles_table.php
2016_11_12_001300_create_material_reviews_table.php


#################################
Models
#################################
Models/Glazy

#################################
Seeds
#################################

Glazy folder
GlazyTableSeeder.php

#################################
Update DatabaseSeeder.php
#################################

$this->call(GlazyTableSeeder::class);

#################################
composer dump-autoload
#################################

#################################
php artisan migrate:refresh --seed
#################################

#################################
execute database/seeds/Glazy/ImportOldGlazy.sql
../../Library/bin/mysql -u root -p laravel_glazy
password root
source database/seeds/Glazy/ImportOldGlazy.sql
#################################

update materials set is_archived = TRUE where created_by_user_id = 1


#################################
cp app/console/commands (or entire app/console dir)
#################################

#################################
register commands in app/console/Kernel.php
#################################
    protected $commands = [
        // DAU
        Commands\UpdateRecipeAnalyses::class
    ];

#################################
php artisan command:updaterecipeanalyses
#################################

#################################
update materials
set base_composite_hash = null
where true
php artisan command:updatematerialhashes
#################################

#################################
cp app/Api over
#################################

#################################
cp app/Http/Controllers/Glazy
#################################

#################################
cp Middleware/CreateGuestAuthUser.php
#################################

#################################
cp Middleware/SetApiAuth.php
#################################


#################################
edit Http/Controllers/Controller
#################################
use App\Models\Glazy\Material\Collection;
use App\Models\Glazy\Material\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;


    public function __construct()
    {

        // TODO: DAU refactor
        $this->middleware(function ($request, $next) {

            $current_user = null;

            if (Auth::check()) {
                $current_user = [];
                $auth_user = Auth::user();
                $current_user['id'] = $auth_user->id;
                $current_user['name'] = $auth_user->name;

                $profile = UserProfile::where('user_id', $auth_user->id)->first();
                if ($profile) {
                    $current_user['username'] = $profile->username;
                }

//                $collections = Collection::select('id', 'name')->where('created_by_user_id', $auth_user->id)->get();

                $collections = Collection::where('created_by_user_id', $auth_user->id)->with('thumbnail')->get();
                $cols = [];
                foreach($collections as $collection) {
                    $col = [];
                    $col['id'] = $collection->id;
                    $col['name'] = $collection->name;
                    if ($collection->thumbnail) {
                        $col['thumbnail_url'] =
                            '/storage/uploads/recipes/'
                            .substr(''.$collection->thumbnail->material_id, -2, 2)
                            .'/s_'.$collection->thumbnail->filename;
                        $col['thumbnail'] = $collection->thumbnail->filename;
                    }
                    $cols[] = $col;
                }

                $current_user['collections'] = $cols;
            }

            View::share(['current_user' => $current_user]);

            return $next($request);
        });

    }


#################################
cp app/Policies
#################################


#################################
RouteServiceProvider.php : mapApiRoutes
#################################
        Route::prefix('api')
            ->middleware('api')
            ->namespace('App\Api\V1\Controllers')
            ->group(base_path('routes/api.php'));



#################################
cp packages dir
#################################

#################################
composer.json
#################################
    "autoload": {
        "classmap": [
            "database",
            "packages/derekau/ceramicscalc"
        ],

require:
        "intervention/image": "^2.3",
        "ksubileau/color-thief-php": "^1.3",

require-dev:
	"lanin/laravel-api-debugger": "^0.2.0"

#################################
composer update
#################################

#################################
package.json
#################################
npm install -S bootstrap@4.0.0-alpha.6
npm install -S bootstrap-colorpicker
npm install -S @claviska/jquery-minicolors

  "dependencies": {
skipped????????    "vue-router": "^2.2.0"
  }


#################################
config/app.php
#################################
change name to Glazy
providers array:
        Intervention\Image\ImageServiceProvider::class,
        Lanin\ApiDebugger\DebuggerServiceProvider::class,
aliases:
        // DAU: Intervention
        'Image' => Intervention\Image\Facades\Image::class,
        'Debugger' => Lanin\ApiDebugger\DebuggerFacade::class,

#################################
ln -s ../storage/app/public storage
#################################

#################################
config/filesystems.php
#################################
        // DAU Glazy disks
        'tmp' => [
            'driver'     => 'local',
            'root'       => storage_path('app/tmp'),
        ],

        'materials' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public/uploads/recipes'),
            'visibility' => 'public',
        ],

        'collections' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public/uploads/collections'),
            'visibility' => 'public',
        ],

        'profiles' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public/uploads/profiles'),
            'visibility' => 'public',
        ],



#################################
cp public/img/glazy
#################################

#################################
cp resources/assets/js/ceramicscalc
#################################

#################################
composer require laravel/passport
#################################

#################################
config/app.php
#################################
        Laravel\Passport\PassportServiceProvider::class,

#################################
php artisan migrate
#################################

#################################
php artisan passport:install
#################################

#################################
App\Models\Access\User
#################################

use Laravel\Passport\HasApiTokens;

use:        HasApiTokens; // DAU

#################################
Providers/AuthServiceProvider
#################################
use Laravel\Passport\Passport;
use Carbon\Carbon;
use App\Models\Glazy\Material\Material;
use App\Policies\MaterialPolicy;
use App\Models\Glazy\Material\MaterialImage;
use App\Policies\MaterialImagePolicy;

    protected $policies = [
        //
        // DAU
        Material::class => MaterialPolicy::class,
        MaterialImage::class => MaterialImagePolicy::class,
    ];

in boot:
        // DAU
        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addDays(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

#################################
php artisan vendor:publish --tag=passport-components
#################################

#################################
cp resources/assets/js/components/glazy
#################################

#################################
cp resources/assets/js/components/glazy-bootstrap.js
#################################

#################################
update package.json
#################################
npm install -S highcharts
npm install -S vue-star-rating
npm install -S vue-multiselect@next
npm install -S daemonite-material@4.0.0-alpha.6

#################################
resources/assets/js/bootstrap.js
#################################
// DAU
require('./components/glazy-bootstrap');

#################################
Http/Kernel.php
#################################
        'web' => [
            \Laravel\Passport\Http\Middleware\CreateFreshApiToken::class,   // DAU
//            \App\Http\Middleware\CreateGuestAuthUser::class,    // DAU

        'api' => [
            \App\Http\Middleware\SetApiAuth::class, // DAU
//            \App\Http\Middleware\CreateGuestAuthUser::class,    // DAU
            'throttle:60,1',
            'bindings',
        ],


#################################
cp resources/assets/sass/glazy
#################################

#################################
resources/assets/sass/frontend/app.scss
#################################
@import url(https://fonts.googleapis.com/css?family=Roboto:100,300,300i,400,600);

// Bootstrap
//@import "../../../../node_modules/bootstrap-sass/assets/stylesheets/bootstrap";
// DAU
@import "node_modules/daemonite-material/assets/sass/material";
@import "node_modules/tether/src/css/tether";

// Glazy
@import "../glazy/app";

#################################
cp resources/views/glazy
#################################

#################################
cp routes/Api
cp routes/Glazy
#################################

#################################
routes/api.php
#################################
// DAU
Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
    require (__DIR__ . '/Api/Recipe.php');
    require (__DIR__ . '/Api/Material.php');
});

#################################
routes/web.php
#################################
// DAU
Route::group(['namespace' => 'Glazy', 'as' => 'glazy.'], function () {
    require (__DIR__ . '/Glazy/Recipe.php');
});

#################################
config/auth.php
#################################
        // DAU
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],

#################################
npm run dev
#################################

#################################
EDITED resources/views/glazy/layout/app.blade.php for MIX
#################################

#################################
REMOVE all hasOwnProperty checks in vue components
#################################

#################################
copy over resources/lang/en/glazy
#################################

#################################
add profile() to User
#################################
    // DAU
    public function profile()
    {
        return $this->hasOne('App\Models\Glazy\Material\UserProfile');
    }

#################################
change session timeout 720min
#################################

#################################
#################################

#################################
#################################

#################################
#################################

#################################
#################################
intervention/image suggests installing intervention/imagecache (Caching extension for the Intervention Image library)
ksubileau/color-thief-php suggests installing ext-gmagick (to use the Gmagick image adapter.)
phpseclib/phpseclib suggests installing ext-libsodium (SSH2/SFTP can make use of some algorithms provided by the libsodium-php extension.)
phpseclib/phpseclib suggests installing ext-gmp (Install the GMP (GNU Multiple Precision) extension in order to speed up arbitrary precision integer arithmetic operations.)
lcobucci/jwt suggests installing mdanter/ecc (Required to use Elliptic Curves based algorithms.)
league/oauth2-server suggests installing indigophp/hash-compat (Polyfill for hash_equals function for PHP 5.5)


#################################
INSTALL IMAGE MAGICK
https://laracasts.com/discuss/channels/general-discussion/vagrant-imagick
For now with php7 the following should work: sudo apt-get update && sudo apt-get install -y imagemagick php-imagick && sudo service php7.0-fpm restart