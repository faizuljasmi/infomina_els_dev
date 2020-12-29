<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function state(){
        return $this->belongsTo(State::class,'state_id');
    }

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function employees(){
        return $this->hasMany(User::class);
    }

    public function active_employees(){
        return $this->employees()->where('status','Active');
    }
}
