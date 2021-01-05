<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveEarning extends Model
{
    protected $table = 'els_leave_earnings';

    protected $fillable = [
        'no_of_days',
    ];

    //One leave earning belongs to one user
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    //One leave earning belongs to one leave type
    public function leave_type(){
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
     }

    public function brought_forward(){
        return $this->hasOne(BroughtForwardLeave::class,'leave_type_id', 'user_id');
    }
}
