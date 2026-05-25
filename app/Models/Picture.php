<?php

namespace App\Models;

use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * @property int $id
 * @property string $name
 * @property string $filename
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Ammunition> $bullets
 * @property-read Collection<int, Firearm> $firearms
 * @property-read Collection<int, Inventory> $inventories
 * @property-read Collection<int, Magazine> $magazines
 * @property-read Collection<int, Order> $orders
 * @property-read Collection<int, Range> $ranges
 * @property-read Collection<int, TrainingSession> $shoots
 * @property-read Collection<int, Store> $stores
 * @property-read Collection<int, Target> $targets
 * @property-read Collection<int, Note> $notes
 */
class Picture extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.pictures';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'filename',
        'user_id',
    ];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    /**
     * @return void
     */
    public function resize()
    {
        $img = Image::make(storage_path('app/public/images/'.$this->filename));

        $img->fit(1920, 1440);
        $img->save(storage_path('app/public/images/large/'.$this->filename));

        $img->fit(480, 360);
        $img->save(storage_path('app/public/images/medium/'.$this->filename));

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

    /**
     * @return MorphToMany<Ammunition, self>
     */
    public function bullets(): MorphToMany
    {
        return $this->morphedByMany(Ammunition::class, 'pictureable');
    }

    /**
     * @return MorphToMany<Firearm, self>
     */
    public function firearms(): MorphToMany
    {
        return $this->morphedByMany(Firearm::class, 'pictureable');
    }

    /**
     * @return MorphToMany<Inventory, self>
     */
    public function inventories(): MorphToMany
    {
        return $this->morphedByMany(Inventory::class, 'pictureable');
    }

    /**
     * @return MorphToMany<Magazine, self>
     */
    public function magazines(): MorphToMany
    {
        return $this->morphedByMany(Magazine::class, 'pictureable');
    }

    /**
     * @return MorphToMany<Order, self>
     */
    public function orders(): MorphToMany
    {
        return $this->morphedByMany(Order::class, 'pictureable');
    }

    /**
     * @return MorphToMany<Range, self>
     */
    public function ranges(): MorphToMany
    {
        return $this->morphedByMany(Range::class, 'pictureable');
    }

    /**
     * @return MorphToMany<TrainingSession, self>
     */
    public function shoots(): MorphToMany
    {
        return $this->morphedByMany(TrainingSession::class, 'pictureable');
    }

    /**
     * @return MorphToMany<Store, self>
     */
    public function stores(): MorphToMany
    {
        return $this->morphedByMany(Store::class, 'pictureable');
    }

    /**
     * @return BelongsToMany<Target, self>
     */
    public function targets(): BelongsToMany
    {
        return $this->belongsToMany(Target::class);
    }

    /**
     * @return MorphToMany<Training, self>
     */
    public function trips(): MorphToMany
    {
        return $this->morphedByMany(Training::class, 'pictureable');
    }

    /**
     * @return MorphMany<Note, self>
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
