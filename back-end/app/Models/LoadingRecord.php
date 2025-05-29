<?php

namespace App\Models;

use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoadingRecord extends Model
{
    use SoftDeletes;

    protected $table = 'loading_records';

    protected $fillable = [
        'truck_id',
        'location_id',
        'company_id',
        'driver_id',
        'empty_weight',
        'loaded_weight',
        'status',
        'entry_time',
        'exit_time',
        'driver_star',
        'entry_date'
    ];

    protected $dates = ['exit_time', 'created_at', 'updated_at', 'deleted_at'];

    // روابط
    public function truck(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Truck::class)->withTrashed();
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'loading_record_locations')->withTrashed();
    }

    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    // Accessors
    public function getCreatedAtAttribute($value)
    {
        return (new Verta($value))->format('Y/m/d');
    }

    public function getUpdatedAtAttribute($value)
    {
        return (new Verta($value))->format('Y/m/d H:i:s');
    }

    public function getExitTimeAttribute($value)
    {
        return (new Verta($value))->format('H:i');
    }

    public function getEntryTimeAttribute($value)
    {
        return (new Verta($value))->format('H:i');
    }

    public function getEntryDateAttribute($value)
    {
        return (new Verta($value))->format('Y/m/d');
    }

    public function getTotalDurationAttribute()
    {
        if ($this->entry_time && $this->exit_time) {
            $entryTime = Carbon::createFromFormat('H:i:s', $this->entry_time);
            $exitTime = Carbon::createFromFormat('H:i:s', $this->exit_time);
            return $entryTime->diff($exitTime)->format('%H:%I:%S');
        }
        return null; // اگر خروج ثبت نشده باشد
    }

    // اکسسور برای وزن خالص
    public function getNetWeightAttribute()
    {
        if ($this->loaded_weight !== null && $this->empty_weight !== null) {
            return $this->loaded_weight - $this->empty_weight;
        }
        return null; // اگر وزن بار یا وزن خالی ثبت نشده باشد
    }
}
