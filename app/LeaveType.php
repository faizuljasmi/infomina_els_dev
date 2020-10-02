<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
    ];

    //One leave type has many leave entitlements
    public function entitlements(){
        return $this->hasMany(LeaveEntitlement::class);
    }

    //One leave type has many leave earnings 
    public function earnings(){
       return $this->hasMany(LeaveEarning::class);
    }
}
