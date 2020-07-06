<?php

namespace App\Repositories;

use App\Models\User;
use Auth0\Login\Auth0JWTUser;
use Auth0\Login\Auth0User;
use Auth0\Login\Repository\Auth0UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryAuth0 extends Auth0UserRepository
{
    /**
     * Get an existing user or create a new one
     *
     * @param array $profile - Auth0 profile
     *
     * @return User
     */
    protected function upsertUser( $profile ) {
        Log::debug(__CLASS__.':'.__LINE__, [$profile]);
        return User::firstOrCreate(['sub' => $profile['sub']], [
            'email' => $profile['email'] ?? '',
            'name' => $profile['name'] ?? '',
        ]);
    }

    /**
     * Authenticate a user with a decoded ID Token
     *
     * @param array $decodedJwt
     *
     * @return Auth0JWTUser
     */
    public function getUserByDecodedJWT(array $decodedJwt) : Authenticatable
    {
        Log::debug(__CLASS__.':'.__LINE__, [$decodedJwt]);
        $user = $this->upsertUser( (array) $decodedJwt );
        return new Auth0JWTUser( $user->getAttributes() );
    }

    /**
     * Get a User from the database using Auth0 profile information
     *
     * @param array $userinfo
     *
     * @return Auth0User
     */
    public function getUserByUserInfo(array $userinfo) : Authenticatable
    {
        Log::debug(__CLASS__.':'.__LINE__, [$userinfo]);
        $user = $this->upsertUser( $userinfo['profile'] );
        return new Auth0User( $user->getAttributes(), $userinfo['accessToken'] );
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        Log::debug(__CLASS__.':'.__LINE__);
        return User::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        Log::debug(__CLASS__.':'.__LINE__);
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
