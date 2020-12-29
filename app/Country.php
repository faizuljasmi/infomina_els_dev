<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function states(){
        return $this->hasMany(State::class);
    }

    public function branches(){
        return $this->hasMany(Branch::class);
    }

    public function holidays(){
        return $this->hasMany(Holiday::class);
    }

    public function country_wide_holidays(){
        return $this->holidays()->where('state_id',null);
    }
}
