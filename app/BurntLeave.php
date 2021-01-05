<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BurntLeave extends Model
{
    protected $table = 'els_burnt_leaves';

    protected $fillable = [
        'no_of-days',
    ];

    //One burnt leave belongs to one user
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    //One burnt leave belongs to one leave type
    public function leave_type(){
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
     }
}
