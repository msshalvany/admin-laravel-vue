<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'address', 'phone'];

    public function trucks(){
        return $this->hasMany(Truck::class);
    }
}
