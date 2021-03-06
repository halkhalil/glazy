<?php

namespace App\Api\V1\Controllers\Glazy;

use App\Api\V1\Repositories\UserProfileRepository;
use App\Api\V1\Transformers\User\UserTransformer;
use App\Models\UserProfile;
use App\User;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;
use App\Api\V1\Serializers\GlazySerializer;

use Illuminate\Support\Facades\Log;

use Auth;

class UserProfileController extends ApiBaseController
{

	protected $userProfileRepository;

    public function __construct(
        UserProfileRepository $userProfileRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->userProfileRepository = $userProfileRepository;
    }

    public function show($id)
    {
        $userProfile = $this->userProfileRepository->getWithDetails($id);

        if (!Auth::guard()->user() || !Auth::guard()->user()->can('view', $userProfile)) {
            return $this->respondUnauthorized('This is private.');
        }

        if (!$userProfile)
        {
            return $this->respondNotFound('Item does not exist');
        }

        $resource = new FractalItem($userProfile, new UserProfileTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function update(Request $request)
    {
        if (!Auth::guard()->user()) {
            return $this->respondUnauthorized('You must login to do this.');
        }

        $user = Auth::guard('api')->user();
        $current_user_id = $user->id;

        $data = $request->all();

        if(array_key_exists('username', $data) && $data['username']) {
            if (is_numeric($data['username'])) {
                return $this->respondWithError('Username cannot be numeric.');
            }
            // TODO: Check if username already exists in DB
            $sameUsernames = UserProfile::where('username', '=', $data['username'])
                ->where('user_id', '<>', $current_user_id)
                ->count();
            if ($sameUsernames) {
                return $this->respondWithError('Sorry, the username "'.$data['username'].'" is already taken.');
            }
        }

        $userProfile = $user->profile;

        if (! $userProfile) {
            // If a user has never entered user profile data, they might not have a user profile
            $userProfile = $this->userProfileRepository->create($user, $data);
        }
        else {
            $userProfile = $this->userProfileRepository->update($userProfile, $data);
        }

        if (array_key_exists('name', $data) && $data['name'] && $data['name'] !== $user->name) {
            // User's name has also been changed
            $user->name = $data['name'];
            $user->save();
        }

        $resource = new FractalItem($user, new UserTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function createAvatar(Request $request)
    {
        $data = $request->all();
        
        if (!Auth::guard()->user()) {
            return $this->respondUnauthorized('You must login to do this.');
        }

        $user = Auth::guard('api')->user();
        $userProfile = $user->profile;

        if (! $userProfile) {
            // If a user has never entered user profile data, they might not have a user profile
            $userProfile = $this->userProfileRepository->create($user, []);
        }

        $userProfile = $this->userProfileRepository->createAvatar($userProfile, $data);

        $resource = new FractalItem($user, new UserTransformer());

        return $this->manager->createData($resource)->toArray();
    }

}
