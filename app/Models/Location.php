<?php

namespace App\Models;

use App\Models\Reference\LocationType;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $label
 * @property string|null $description
 * @property int|null $location_type_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read LocationType|null $type
 */
class Location extends Model
{
    use BelongsToUser, HasFactory, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'description',
        'location_type_id',
        'user_id',
    ];

    /**
     * @return BelongsTo<LocationType, self>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LocationType::class);
    }
}
