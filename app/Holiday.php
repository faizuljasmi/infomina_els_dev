<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = '_holidays';

    protected $fillable = [
        'name','date_from','date_to','total_days',
    ];

    public function state(){
        return $this->belongsTo(State::class,'state_id');
    }

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

}
