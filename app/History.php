<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'els_histories';

    //One history belongs to one leave application
    public function application(){
        return $this->belongsTo(LeaveApplication::class);
    }
}
