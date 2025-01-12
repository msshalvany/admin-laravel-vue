<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadingRecordLocation extends Model
{
    protected $table = 'loading_record_locations'; // نام جدول
    protected $fillable = ['loading_record_id', 'location_id']; // ستون‌های قابل پر کردن
}
