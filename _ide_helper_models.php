<?php
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\Bullet
 *
 * @property int $id
 * @property string $manufacturer
 * @property string $name
 * @property int $weight
 * @property int $inventory
 * @property int $purpose_id
 * @property int $cartridge_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Cartridge $cartridge
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Inventory[] $inventories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Picture[] $pictures
 * @property-read \App\Purpose $purpose
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Shoot[] $shoots
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bullet whereCartridgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bullet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bullet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bullet whereInventory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bullet whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bullet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bullet wherePurposeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bullet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bullet whereWeight($value)
 */
	class Bullet extends \Eloquent {}
}

namespace App{
/**
 * App\Cartridge
 *
 * @property int $id
 * @property string $size
 * @property string $label
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bullet[] $bullets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Firearm[] $firearms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cartridge purposes()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cartridge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cartridge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cartridge whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cartridge whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cartridge whereUpdatedAt($value)
 */
	class Cartridge extends \Eloquent {}
}

namespace App{
/**
 * App\Firearm
 *
 * @property int $id
 * @property string $label
 * @property string $manufacturer
 * @property string $model
 * @property int $cartridge_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Cartridge $cartridge
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Picture[] $pictures
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Target[] $targets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Firearm whereCartridgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Firearm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Firearm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Firearm whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Firearm whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Firearm whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Firearm whereUpdatedAt($value)
 */
	class Firearm extends \Eloquent {}
}

namespace App{
/**
 * App\Inventory
 *
 * @property int $id
 * @property int $boxes
 * @property int $rounds_per_box
 * @property int $rounds
 * @property float $cost_per_box
 * @property float $cost
 * @property int $order_id
 * @property int $bullet_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Bullet $bullet
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \App\Order $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Picture[] $pictures
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereBoxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereBulletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereCostPerBox($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereRounds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereRoundsPerBox($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inventory whereUpdatedAt($value)
 */
	class Inventory extends \Eloquent {}
}

namespace App{
/**
 * App\Magazine
 *
 * @property int $id
 * @property string $label
 * @property string $manufacturer
 * @property string $model_name
 * @property int $capacity
 * @property int $cartridge_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $serial_number
 * @property string|null $id_marking
 * @property-read \App\Cartridge $cartridge
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Picture[] $pictures
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereCartridgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereIdMarking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereModelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Magazine whereUpdatedAt($value)
 */
	class Magazine extends \Eloquent {}
}

namespace App{
/**
 * App\Note
 *
 * @property int $id
 * @property int $user_id
 * @property string $note
 * @property int $noteable_id
 * @property string $noteable_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $noteable
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereNoteableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereNoteableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereUserId($value)
 */
	class Note extends \Eloquent {}
}

namespace App{
/**
 * App\Order
 *
 * @property int $id
 * @property int $rounds
 * @property int $store_id
 * @property \Carbon\Carbon $order_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property float $total_cost
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Inventory[] $inventories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \App\Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereRounds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereTotalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereUpdatedAt($value)
 */
	class Order extends \Eloquent {}
}

namespace App{
/**
 * App\Picture
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $filename
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Target[] $targets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Picture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Picture whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Picture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Picture whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Picture whereUpdatedAt($value)
 */
	class Picture extends \Eloquent {}
}

namespace App{
/**
 * App\Purpose
 *
 * @property int $id
 * @property string $label
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bullet[] $bullets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Purpose whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Purpose whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Purpose whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Purpose whereUpdatedAt($value)
 */
	class Purpose extends \Eloquent {}
}

namespace App{
/**
 * App\Range
 *
 * @property int $id
 * @property string $label
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Shoot[] $shoots
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Range whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Range whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Range whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Range whereUpdatedAt($value)
 */
	class Range extends \Eloquent {}
}

namespace App{
/**
 * App\Shoot
 *
 * @property int $id
 * @property int $trip_id
 * @property int $rounds
 * @property int $firearm_id
 * @property int $bullet_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Bullet $bullet
 * @property-read \App\Firearm $firearm
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Picture[] $pictures
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Target[] $targets
 * @property-read \App\Trip $trip
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shoot whereBulletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shoot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shoot whereFirearmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shoot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shoot whereRounds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shoot whereTripId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shoot whereUpdatedAt($value)
 */
	class Shoot extends \Eloquent {}
}

namespace App{
/**
 * App\Store
 *
 * @property int $id
 * @property string $label
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Store whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Store whereUpdatedAt($value)
 */
	class Store extends \Eloquent {}
}

namespace App{
/**
 * App\Target
 *
 * @property int $id
 * @property string|null $label
 * @property float $distance
 * @property float $group_size
 * @property int $picture_id
 * @property int|null $bullet_id
 * @property int|null $firearm_id
 * @property int|null $shoot_id
 * @property int|null $trip_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Bullet|null $bullet
 * @property-read \App\Firearm|null $firearm
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \App\Picture $picture
 * @property-read \App\Shoot|null $shoot
 * @property-read \App\Trip|null $trip
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereBulletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereFirearmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereGroupSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target wherePictureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereShootId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereTripId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Target whereUpdatedAt($value)
 */
	class Target extends \Eloquent {}
}

namespace App{
/**
 * App\Trip
 *
 * @property int $id
 * @property \Carbon\Carbon $trip_date
 * @property int $range_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \App\Range $range
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Shoot[] $shoots
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Target[] $targets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Trip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Trip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Trip whereRangeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Trip whereTripDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Trip whereUpdatedAt($value)
 */
	class Trip extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

