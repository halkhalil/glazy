<?php namespace App\Models\Glazy\Material;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

class UserProfile extends Model
{
    const DB_USERNAME = 'username';
    const DB_TITLE = 'title';
    const DB_DESCRIPTION = 'description';
    const DB_URL = 'url';
    const DB_PINTEREST = 'pinterest';
    const DB_FACEBOOK = 'facebook';
    const DB_INSTAGRAM = 'instagram';

    protected $table = 'user_profiles';

    protected $fillable = [
        'title',
        'description',
        'location',
        'url',
        'pinterest',
        'facebook',
        'instagram',
    ];


    public function user()
    {
        return $this->hasOne('App\User');
    }

}