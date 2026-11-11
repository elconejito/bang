<?php

namespace App\Models\Reference;

use App\Models\Ammunition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $label
 * @property string $abbreviation
 */
class BulletType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.bullet_types';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['label', 'abbreviation'];

    /**
     * Ammunition that uses this shared bullet type.
     *
     * @return HasMany<Ammunition, self>
     */
    public function ammunition(): HasMany
    {
        return $this->hasMany(Ammunition::class, 'bullet_type_id')->withoutGlobalScopes();
    }

    /**
     * Whether any user's ammunition references this shared bullet type.
     */
    public function isInUse(): bool
    {
        return $this->ammunition()->exists();
    }
}
