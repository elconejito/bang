<?php

namespace App\Models;

use App\Models\Reference\LocationType;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * Class Location.
 *
 * @package namespace App\Models
 * @property int $id
 *
 */
class Location extends Model 
{
    use BelongsToUser, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'description',
        'location_type_id',
        'user_id',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(LocationType::class);
    }

}
