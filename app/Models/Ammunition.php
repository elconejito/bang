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
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Ammunition extends Model
{
    use BelongsToUser, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.ammunition';

    protected $fillable = [
        'manufacturer',
        'label',
        'weight',
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

    public function ammunitionCasing(): BelongsTo
    {
        return $this->belongsTo(AmmunitionCasing::class);
    }

    public function ammunitionCondition(): BelongsTo
    {
        return $this->belongsTo(AmmunitionCondition::class);
    }

    public function bulletType(): BelongsTo
    {
        return $this->belongsTo(BulletType::class);
    }

    public function caliber(): BelongsTo
    {
        return $this->belongsTo(Caliber::class);
    }

    public function pictures(): MorphToMany
    {
        return $this->morphToMany(Picture::class, 'pictureable');
    }

    public function primerType(): BelongsTo
    {
        return $this->belongsTo(PrimerType::class);
    }

    public function purpose(): BelongsTo
    {
        return $this->belongsTo(Purpose::class);
    }

    public function shellLength(): BelongsTo
    {
        return $this->belongsTo(ShellLength::class);
    }

    public function shellType(): BelongsTo
    {
        return $this->belongsTo(ShellType::class);
    }

    public function shotMaterial(): BelongsTo
    {
        return $this->belongsTo(ShotMaterial::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function scopeForCaliber(Builder $query, Caliber $caliber): Builder
    {
        return $query->where('caliber_id', '=', $caliber->id);
    }

    public function scopeForPurpose(Builder $query, Purpose $purpose): Builder
    {
        return $query->where('purpose_id', '=', $purpose->id);
    }

    public function shoots(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function inventory(): void
    {
        $added = $this->inventories()->sum('rounds');
        $fired = $this->shoots()->sum('rounds');

        $this->inventory = $added - $fired;

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
