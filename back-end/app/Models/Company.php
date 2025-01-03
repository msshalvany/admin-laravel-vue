<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'address', 'phone'];

    public function trucks(){
        return $this->hasMany(Truck::class);
    }
}
