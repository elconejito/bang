<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ammunition extends Model
{
    use BelongsToUser, HasNotes;

    protected $fillable = [
        'manufacturer',
        'name',
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

    /**
     * Each bullet belongs to a Cartridge type
     *
     * @return BelongsTo
     */
    public function caliber() {
        return $this->belongsTo(Caliber::class);
    }

    public function purpose() {
        return $this->belongsTo(Purpose::class);
    }

    public function pictures() {
        return $this->morphToMany(Picture::class, 'pictureable');
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
