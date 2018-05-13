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
use App\Models\MaterialComment;
use App\Policies\MaterialCommentPolicy;
use App\Models\UserProfile;
use App\Policies\UserProfilePolicy;
use App\Models\Collection;
use App\Policies\CollectionPolicy;
use App\Models\UserMaterial;
use App\Policies\UserMaterialPolicy;

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
        MaterialComment::class => MaterialCommentPolicy::class,
        UserProfile::class => UserProfilePolicy::class,
        Collection::class => CollectionPolicy::class,
        UserMaterial::class => UserMaterialPolicy::class,
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
