<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoadingRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'truck_id', 'empty_weight', 'loaded_weight', 'total_weight',
        'customer_weight', 'load_percentage', 'entry_time', 'exit_time',
        'loading_start_time', 'loading_end_time', 'location_id', 'driver_satisfaction'
    ];

    // رابطه با کامیون (یک بارگیری مربوط به یک کامیون)
    public function truck(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    // رابطه با مکان (یک بارگیری مربوط به یک مکان)
    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    // رابطه با راننده (یک بارگیری از طریق کامیون، به راننده مرتبط است)
    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class, 'truck_id');
    }
}
