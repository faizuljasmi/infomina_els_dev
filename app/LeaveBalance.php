<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'no_of_days',
    ];

    //One leave balance belongs to one user
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    //One leave balance belongs to one leave type
    public function leave_type(){
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
     }
}
