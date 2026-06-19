<?php

namespace App\Models;

use App\Models\Reference\AmmunitionCasing;
use App\Models\Reference\AmmunitionCondition;
use App\Models\Reference\BulletType;
use App\Models\Reference\PrimerType;
use App\Models\Reference\Purpose;
use App\Models\Reference\ShellLength;
use App\Models\Reference\ShellType;
use App\Models\Reference\ShotMaterial;
use App\Scopes\UserScope;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string $manufacturer
 * @property string $label
 * @property int|null $weight
 * @property int $inventory
 * @property int|null $reorder_min
 * @property int|null $purpose_id
 * @property int $caliber_id
 * @property int|null $bullet_type_id
 * @property int|null $ammunition_casing_id
 * @property int|null $ammunition_condition_id
 * @property int|null $primer_type_id
 * @property int|null $shot_material_id
 * @property int|null $shell_length_id
 * @property int|null $shell_type_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read AmmunitionCasing|null $ammunitionCasing
 * @property-read AmmunitionCondition|null $ammunitionCondition
 * @property-read BulletType|null $bulletType
 * @property-read Caliber $caliber
 * @property-read PrimerType|null $primerType
 * @property-read Purpose|null $purpose
 * @property-read ShellLength|null $shellLength
 * @property-read ShellType|null $shellType
 * @property-read ShotMaterial|null $shotMaterial
 * @property-read Collection<int, Inventory> $inventories
 * @property-read Collection<int, Picture> $pictures
 * @property-read Collection<int, TrainingSession> $shoots
 */
class Ammunition extends Model
{
    use BelongsToUser, HasFactory, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.ammunition';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manufacturer',
        'label',
        'weight',
        'reorder_min',
        'purpose_id',
        'caliber_id',
        'shell_length_id',
        'shell_type_id',
        'shot_material_id',
        'ammunition_casing_id',
        'ammunition_condition_id',
        'bullet_type_id',
        'primer_type_id',
        'user_id',
    ];

    /**
     * @return BelongsTo<AmmunitionCasing, self>
     */
    public function ammunitionCasing(): BelongsTo
    {
        return $this->belongsTo(AmmunitionCasing::class);
    }

    /**
     * @return BelongsTo<AmmunitionCondition, self>
     */
    public function ammunitionCondition(): BelongsTo
    {
        return $this->belongsTo(AmmunitionCondition::class);
    }

    /**
     * @return BelongsTo<BulletType, self>
     */
    public function bulletType(): BelongsTo
    {
        return $this->belongsTo(BulletType::class);
    }

    /**
     * @return BelongsTo<Caliber, self>
     */
    public function caliber(): BelongsTo
    {
        return $this->belongsTo(Caliber::class);
    }

    /**
     * @return MorphToMany<Picture, self>
     */
    public function pictures(): MorphToMany
    {
        return $this->morphToMany(Picture::class, 'pictureable');
    }

    /**
     * @return BelongsTo<PrimerType, self>
     */
    public function primerType(): BelongsTo
    {
        return $this->belongsTo(PrimerType::class);
    }

    /**
     * @return BelongsTo<Purpose, self>
     */
    public function purpose(): BelongsTo
    {
        return $this->belongsTo(Purpose::class);
    }

    /**
     * @return BelongsTo<ShellLength, self>
     */
    public function shellLength(): BelongsTo
    {
        return $this->belongsTo(ShellLength::class);
    }

    /**
     * @return BelongsTo<ShellType, self>
     */
    public function shellType(): BelongsTo
    {
        return $this->belongsTo(ShellType::class);
    }

    /**
     * @return BelongsTo<ShotMaterial, self>
     */
    public function shotMaterial(): BelongsTo
    {
        return $this->belongsTo(ShotMaterial::class);
    }

    /**
     * @return HasMany<Inventory, self>
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForCaliber(Builder $query, Caliber $caliber): Builder
    {
        return $query->where('caliber_id', '=', $caliber->id);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForPurpose(Builder $query, Purpose $purpose): Builder
    {
        return $query->where('purpose_id', '=', $purpose->id);
    }

    /**
     * @return HasMany<TrainingSession, self>
     */
    public function shoots(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function recalculateInventory(): void
    {
        $this->inventory = $this->inventories()
            ->withoutGlobalScope(UserScope::class)
            ->sum('rounds');

        $this->save();
    }

    public function getLabel(bool $extended = false): string
    {
        $label = $this->manufacturer.' '.$this->name;
        if ($extended) {
            $label .= ', '.$this->weight.'gr '.$this->caliber->label;
        }

        return $label;
    }
}
