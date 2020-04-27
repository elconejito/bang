<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\Purchase
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Purchase query()
 */
	class Purchase extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TrainingSession
 *
 * @property int $id
 * @property int $trip_id
 * @property int $rounds
 * @property int $firearm_id
 * @property int $ammunition_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property-read \App\Models\Ammunition $ammunition
 * @property-read \App\Models\Firearm $firearm
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Picture[] $pictures
 * @property-read int|null $pictures_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Target[] $targets
 * @property-read int|null $targets_count
 * @property-read \App\Models\Training $trip
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession whereAmmunitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession whereFirearmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession whereRounds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession whereTripId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TrainingSession whereUserId($value)
 */
	class TrainingSession extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Caliber
 *
 * @property int $id
 * @property string $label
 * @property string $short_label
 * @property int $caliber_type_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ammunition[] $ammunition
 * @property-read int|null $ammunition_count
 * @property-read \App\Models\CaliberType $caliberType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Firearm[] $firearms
 * @property-read int|null $firearms_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber purposes()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber whereCaliberTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber whereShortLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Caliber whereUserId($value)
 */
	class Caliber extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Note
 *
 * @property int $id
 * @property int $user_id
 * @property string $note
 * @property int $noteable_id
 * @property string $noteable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $noteable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note whereNoteableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note whereNoteableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Note whereUserId($value)
 */
	class Note extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Inventory
 *
 * @property int $id
 * @property int $boxes
 * @property int $rounds_per_box
 * @property int $rounds
 * @property float $cost_per_box
 * @property float $cost
 * @property int $order_id
 * @property int $ammunition_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property-read \App\Models\Ammunition $bullet
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \App\Models\Order $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Picture[] $pictures
 * @property-read int|null $pictures_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereAmmunitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereBoxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereCostPerBox($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereRounds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereRoundsPerBox($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inventory whereUserId($value)
 */
	class Inventory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $rounds
 * @property int $store_id
 * @property \Illuminate\Support\Carbon $order_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property float $total_cost
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Inventory[] $inventories
 * @property-read int|null $inventories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \App\Models\Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereRounds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTotalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUserId($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Store
 *
 * @property int $id
 * @property string $label
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereUserId($value)
 */
	class Store extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Ammunition
 *
 * @property int $id
 * @property string $manufacturer
 * @property string $name
 * @property int $weight
 * @property int $inventory
 * @property int $purpose_id
 * @property int $caliber_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property-read \App\Models\Caliber $caliber
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Inventory[] $inventories
 * @property-read int|null $inventories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Picture[] $pictures
 * @property-read int|null $pictures_count
 * @property-read \App\Models\Purpose $purpose
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TrainingSession[] $shoots
 * @property-read int|null $shoots_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition forCaliber(\App\Models\Caliber $caliber)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition forPurpose(\App\Models\Purpose $purpose)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition whereCaliberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition whereInventory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition wherePurposeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ammunition whereWeight($value)
 */
	class Ammunition extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Caliber[] $calibers
 * @property-read int|null $calibers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Firearm
 *
 * @property int $id
 * @property string $label
 * @property string $manufacturer
 * @property string $model
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Caliber[] $calibers
 * @property-read int|null $calibers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Picture[] $pictures
 * @property-read int|null $pictures_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Target[] $targets
 * @property-read int|null $targets_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Firearm whereUserId($value)
 */
	class Firearm extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Target
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @property-read \App\Models\Ammunition|null $bullet
 * @property-read \App\Models\Firearm|null $firearm
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \App\Models\Picture $picture
 * @property-read \App\Models\TrainingSession|null $shoot
 * @property-read \App\Models\Training|null $trip
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereBulletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereFirearmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereGroupSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target wherePictureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereShootId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereTripId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Target whereUserId($value)
 */
	class Target extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Purpose
 *
 * @property int $id
 * @property string $label
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ammunition[] $bullets
 * @property-read int|null $bullets_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Purpose newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Purpose newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Purpose query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Purpose whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Purpose whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Purpose whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Purpose whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Purpose whereUserId($value)
 */
	class Purpose extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Training
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \App\Models\Range $range
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TrainingSession[] $shoots
 * @property-read int|null $shoots_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Target[] $targets
 * @property-read int|null $targets_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Training newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Training newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Training query()
 */
	class Training extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Magazine
 *
 * @property int $id
 * @property string $label
 * @property string $manufacturer
 * @property string $model_name
 * @property int $capacity
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $serial_number
 * @property string|null $id_marking
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Caliber[] $calibers
 * @property-read int|null $calibers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Picture[] $pictures
 * @property-read int|null $pictures_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereIdMarking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereModelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Magazine whereUserId($value)
 */
	class Magazine extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Range
 *
 * @property int $id
 * @property string $label
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TrainingSession[] $shoots
 * @property-read int|null $shoots_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Range newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Range newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Range query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Range whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Range whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Range whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Range whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Range whereUserId($value)
 */
	class Range extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CaliberType
 *
 * @property int $id
 * @property string $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CaliberType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CaliberType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CaliberType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CaliberType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CaliberType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CaliberType whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CaliberType whereUpdatedAt($value)
 */
	class CaliberType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Picture
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $filename
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ammunition[] $bullets
 * @property-read int|null $bullets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Firearm[] $firearms
 * @property-read int|null $firearms_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Inventory[] $inventories
 * @property-read int|null $inventories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Magazine[] $magazines
 * @property-read int|null $magazines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Range[] $ranges
 * @property-read int|null $ranges_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TrainingSession[] $shoots
 * @property-read int|null $shoots_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Store[] $stores
 * @property-read int|null $stores_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Target[] $targets
 * @property-read int|null $targets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Training[] $trips
 * @property-read int|null $trips_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Picture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Picture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Picture query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Picture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Picture whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Picture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Picture whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Picture whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Picture whereUserId($value)
 */
	class Picture extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Cartridge
 *
 * @property int $id
 * @property string $caliber
 * @property string $label
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property int $cartridge_type_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ammunition[] $bullets
 * @property-read int|null $bullets_count
 * @property-read \App\Models\CaliberType $cartridgeType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Firearm[] $firearms
 * @property-read int|null $firearms_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read int|null $notes_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge purposes()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge whereCaliber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge whereCartridgeTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cartridge whereUserId($value)
 */
	class Cartridge extends \Eloquent {}
}

