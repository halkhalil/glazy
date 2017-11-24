<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Models\Material;
use App\Policies\MaterialPolicy;
use App\Models\MaterialImage;
use App\Policies\MaterialImagePolicy;
use App\Models\MaterialReview;
use App\Policies\MaterialReviewPolicy;
use App\Models\UserProfile;
use App\Policies\UserProfilePolicy;
use App\Models\Collection;
use App\Policies\CollectionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Material::class => MaterialPolicy::class,
        MaterialImage::class => MaterialImagePolicy::class,
        MaterialReview::class => MaterialReviewPolicy::class,
        UserProfile::class => UserProfilePolicy::class,
        Collection::class => CollectionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
