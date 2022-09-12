<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use BelongsToUser, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.training';

    protected $dates = ['trip_date', 'created_at', 'updated_at', 'deleted_at'];

    public function range() {
        return $this->belongsTo(Range::class);
    }

    public function shoots() {
        return $this->hasMany(TrainingSession::class);
    }

    public function targets() {
        return $this->hasMany(Target::class);
    }

    public function summary() {
        $firearms = [];
        foreach ( $this->shoots()->get() as $shoot ) {
            if ( isset($firearms[$shoot->firearm->label]) ) {
                $firearms[$shoot->firearm->label] = $firearms[$shoot->firearm->label] + $shoot->rounds;
            } else {
                $firearms[$shoot->firearm->label] = $shoot->rounds;
            }
        }

        return collect($firearms);
    }
}
