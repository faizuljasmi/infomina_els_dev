<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\EmpType;
use App\User;

class LeaveEntitlement extends Model
{
    protected $table = 'els_leave_entitlements';

    protected $fillable = [
        'no_of_days',
    ];

    //One leave ent belongs to one leave type
    public function leave_type(){
       return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    //One leave ent belongs to one emp type
    public function emp_type(){
        return $this->belongsTo(EmpType::class, 'emp_type_id');
    }

    // public function getEmpTypeAttribute(){
    //     return isset($this->emp_type) ? $this->emp_type->name : '';
    // }
}
