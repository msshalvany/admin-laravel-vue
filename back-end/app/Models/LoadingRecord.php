<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoadingRecord extends Model
{
    use SoftDeletes;

    protected $table = 'loading_records';
//
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
    ];

    protected $dates = ['exit_time', 'created_at', 'updated_at', 'deleted_at'];

    // روابط
    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
