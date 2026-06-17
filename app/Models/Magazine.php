<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string|null $label
 * @property string $manufacturer
 * @property string|null $model_name
 * @property int $capacity
 * @property string|null $serial_number
 * @property string|null $id_marking
 * @property string $status
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Picture> $pictures
 * @property-read Collection<int, Caliber> $calibers
 * @property-read Collection<int, Firearm> $firearms
 * @property-read Collection<int, Note> $notes
 */
class Magazine extends Model
{
    use BelongsToUser, HasFactory, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.magazines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'manufacturer',
        'model_name',
        'capacity',
        'serial_number',
        'id_marking',
        'status',
        'user_id',
    ];

    /**
     * @return MorphToMany<Picture, self>
     */
    public function pictures(): MorphToMany
    {
        return $this->morphToMany(Picture::class, 'pictureable');
    }

    /**
     * @return BelongsToMany<Caliber, self>
     */
    public function calibers(): BelongsToMany
    {
        return $this->belongsToMany(Caliber::class, 'cms.caliber_magazine');
    }

    /**
     * @return BelongsToMany<Firearm, self>
     */
    public function firearms(): BelongsToMany
    {
        return $this->belongsToMany(Firearm::class, 'cms.firearm_magazine');
    }

    /**
     * @return MorphMany<Note, self>
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    /**
     * @return MorphMany<AccessoryEvent, self>
     */
    public function events(): MorphMany
    {
        return $this->morphMany(AccessoryEvent::class, 'accessoryable');
    }
}
