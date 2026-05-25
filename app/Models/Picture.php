<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Picture extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.pictures';

    protected $fillable = [
        'name',
        'filename',
        'user_id',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    public function resize()
    {
        // dd(storage_path('app/public/images/' . $this->filename));
        $img = Image::make(storage_path('app/public/images/'.$this->filename));
        // save Large
        $img->fit(1920, 1440);
        $img->save(storage_path('app/public/images/large/'.$this->filename));

        // save Medium
        $img->fit(480, 360);
        $img->save(storage_path('app/public/images/medium/'.$this->filename));

        // Save Thumbnail
        $img->fit(220, 165);
        $img->save(storage_path('app/public/images/thumbnail/'.$this->filename));
    }

    public function getPath(string $size = 'thumbnail'): string
    {
        return 'storage/images/'.$size.'/'.$this->filename;
    }

    public function getUrl(string $size = 'thumbnail'): string
    {
        return Storage::url('images/'.$size.'/'.$this->filename);
    }

    public function bullets(): MorphToMany
    {
        return $this->morphedByMany(Ammunition::class, 'pictureable');
    }

    public function firearms(): MorphToMany
    {
        return $this->morphedByMany(Firearm::class, 'pictureable');
    }

    public function inventories(): MorphToMany
    {
        return $this->morphedByMany(Inventory::class, 'pictureable');
    }

    public function magazines(): MorphToMany
    {
        return $this->morphedByMany(Magazine::class, 'pictureable');
    }

    public function orders(): MorphToMany
    {
        return $this->morphedByMany(Order::class, 'pictureable');
    }

    public function ranges(): MorphToMany
    {
        return $this->morphedByMany(Range::class, 'pictureable');
    }

    public function shoots(): MorphToMany
    {
        return $this->morphedByMany(TrainingSession::class, 'pictureable');
    }

    public function stores(): MorphToMany
    {
        return $this->morphedByMany(Store::class, 'pictureable');
    }

    public function targets(): BelongsToMany
    {
        return $this->belongsToMany(Target::class);
    }

    public function trips(): MorphToMany
    {
        return $this->morphedByMany(Training::class, 'pictureable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
