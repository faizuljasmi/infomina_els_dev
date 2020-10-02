<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TakenLeave extends Model
{
    protected $fillable = [
        'no_of_days',
    ];

    //One taken leave belongs to one user
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    //One taken leave belongs to one leave type
    public function leave_type(){
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
     }
}
