<?php

namespace App\Models;

use Auth0\Laravel\Contract\Model\Stateful\User as StatefulUser;
use App\Traits\HasNotes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model implements StatefulUser, AuthenticatableUser
{
    use Notifiable, HasNotes, Authenticatable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'sub'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['remember_token'];

    public function calibers()
    {
        return $this->hasMany(Caliber::class);
    }

}
