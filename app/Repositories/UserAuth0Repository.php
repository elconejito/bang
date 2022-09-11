<?php

namespace App\Repositories;

use App\Models\User;
use Auth0\Laravel\Contract\Auth\User\Repository as Auth0UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserAuth0Repository implements Auth0UserRepository
{
    public function fromSession(
        array $user
    ): ?Authenticatable {
        return new User([
            'id' => 'just_a_random_example|' . $user['sub'] ?? $user['user_id'] ?? null,
            'name' => $user['name'],
            'email' => $user['email']
        ]);
    }

    public function fromAccessToken(
        array $user
    ): ?Authenticatable {
        // Simliar to above. Used for stateless application types.
        return null;
    }
}
