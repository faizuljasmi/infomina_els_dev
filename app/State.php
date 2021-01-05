<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = '_states';

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function holidays(){
        return $this->hasMany(Holiday::class);
    }

    public function branches(){
        return $this->hasMany(Branch::class);
    }

    public function state_wide_holidays(){
        return $this->holidays()->where('state_id',$this->id);
    }
}
