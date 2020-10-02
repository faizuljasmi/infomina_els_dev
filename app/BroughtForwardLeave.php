<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BroughtForwardLeave extends Model
{
    protected $fillable = [
        'no_of-days',
    ];

    //One  brought forward belongs to one user
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    //One leave earning belongs to one leave type
    public function leave_type(){
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
     }

    public function leave_earning(){
        return $this->belongsTo(LeaveEarning::class,'leave_type_id','user_id');
    }
}
