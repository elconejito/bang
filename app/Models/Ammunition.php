<?php

namespace App\Models;

use App\Models\Reference\AmmunitionCasing;
use App\Models\Reference\AmmunitionCondition;
use App\Models\Reference\BulletType;
use App\Models\Reference\PrimerType;
use App\Models\Reference\ShellLength;
use App\Models\Reference\ShellType;
use App\Models\Reference\ShotMaterial;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use App\Models\Reference\Purpose;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Traits\TransformableTrait;

class Ammunition extends Model
{
    use BelongsToUser, HasNotes, TransformableTrait;

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

    public function ammunitionCasing() {
        return $this->belongsTo(AmmunitionCasing::class);
    }

    public function ammunitionCondition() {
        return $this->belongsTo(AmmunitionCondition::class);
    }

    public function bulletType() {
        return $this->belongsTo(BulletType::class);
    }

    /**
     * Each bullet belongs to a Cartridge type
     *
     * @return BelongsTo
     */
    public function caliber() {
        return $this->belongsTo(Caliber::class);
    }

    public function pictures() {
        return $this->morphToMany(Picture::class, 'pictureable');
    }

    public function primerType() {
        return $this->belongsTo(PrimerType::class);
    }

    public function purpose() {
        return $this->belongsTo(Purpose::class);
    }

    public function shellLength() {
        return $this->belongsTo(ShellLength::class);
    }

    public function shellType() {
        return $this->belongsTo(ShellType::class);
    }

    public function shotMaterial() {
        return $this->belongsTo(ShotMaterial::class);
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    /**
     * @param Builder $query
     * @param Caliber $caliber
     *
     * @return Builder
     */
    public function scopeForCaliber(Builder $query, Caliber $caliber)
    {
        return $query->where('caliber_id', '=', $caliber->id);
    }

    /**
     * @param Builder $query
     * @param Purpose $purpose
     *
     * @return Builder
     */
    public function scopeForPurpose(Builder $query, Purpose $purpose)
    {
        return $query->where('purpose_id', '=', $purpose->id);
    }

    public function shoots() {
        return $this->hasMany(TrainingSession::class);
    }

    public function inventory() {
        $added = $this->inventories()->sum('rounds');
        $fired = $this->shoots()->sum('rounds');

        $this->inventory = $added - $fired;

        $this->save();
    }

    /**
     * @param bool $extended
     *
     * @return string
     */
    public function getLabel($extended = false) {
        $label = $this->manufacturer . " " . $this->name;
        if ( $extended ) {
            $label .= ", " . $this->weight . "gr " . $this->caliber->label;
        }
        return $label;
    }
}
