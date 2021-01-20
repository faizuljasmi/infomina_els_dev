<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'els_histories';

    //One history belongs to one leave application
    public function application(){
        return $this->belongsTo(LeaveApplication::class, 'leave_application_id');
    }

    public function editor(){
        return $this->belongsTo(User::class,'user_id');
    }
}
