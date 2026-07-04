<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $note
 * @property int $notable_id
 * @property string $notable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Model $notable
 */
class Note extends Model
{
    use BelongsToUser, HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'note',
    ];

    /**
     * @return MorphTo<Model, self>
     */
    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}
