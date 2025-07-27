<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use SoftDeletes , HasFactory;

    protected $fillable = [
        'company_id',
        'plate_right',
        'plate_char',
        'plate_left',
        'plate_city',
        'plate_full',
        'plate_type',
        'color',
        'type',
        'weight'
    ];

    const PLATE_TYPE_PRIVATE       = 1; // شخصی و گذر موقت
    const PLATE_TYPE_PUBLIC        = 2; // عمومی
    const PLATE_TYPE_GOVERNMENTAL = 3; // دولتی و تشریفاتی
    const PLATE_TYPE_MILITARY      = 4; // نظامی (پلیس و سپاه)
    const PLATE_TYPE_DIPLOMATIC    = 5; // دیپلماتیک
    const PLATE_TYPE_ARMY          = 6; // ارتش

    public static $plateTypes = [
        self::PLATE_TYPE_PRIVATE       => 'شخصی',
        self::PLATE_TYPE_PUBLIC        => 'عمومی',
        self::PLATE_TYPE_GOVERNMENTAL => 'دولتی',
        self::PLATE_TYPE_MILITARY      => 'نظامی',
        self::PLATE_TYPE_DIPLOMATIC    => 'دیپلماتیک',
        self::PLATE_TYPE_ARMY          => 'ارتش',
    ];



    const TRUCK_TYPE_OTHER = 0;
    const TRUCK_TYPE_TRUCK = 1;
    const TRUCK_TYPE_TRAILER = 2;
    const TRUCK_TYPE_SMALL_TRUCK = 3; // کامیونت
    const TRUCK_TYPE_KHAVAR = 4;       // خاور
    const TRUCK_TYPE_VAN = 5;          // وانت

    public static $truckTypes = [
        self::TRUCK_TYPE_OTHER => 'غیره',
        self::TRUCK_TYPE_TRUCK => 'کامیون',
        self::TRUCK_TYPE_TRAILER => 'تریلی',
        self::TRUCK_TYPE_SMALL_TRUCK => 'کامیونت',
        self::TRUCK_TYPE_KHAVAR => 'خاور',
        self::TRUCK_TYPE_VAN => 'وانت',
    ];


    public function driver(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Driver::class);
    }

    protected $appends = ['type_label', 'plate_type_label'];

    public function getTypeLabelAttribute(): string
    {
        return self::$truckTypes[$this->type] ?? 'نامشخص';
    }

    public function getPlateTypeLabelAttribute(): string
    {
        return self::$plateTypes[$this->plate_type] ?? 'نامشخص';
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::get(fn() => self::$truckTypes[$this->type] ?? 'نامشخص');
    }

    protected function plateTypeLabel(): Attribute
    {
        return Attribute::get(fn() => self::$plateTypes[$this->plate_type] ?? 'نامشخص');
    }


    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

}
