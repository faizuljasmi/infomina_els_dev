<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = ['emp_group_id','leave_type_id','rule_desc','rule_val'];

    public function emp_group(){
        return $this->belongsTo(EmpGroup::class);
    }

    public function leave_type(){
        return $this->belongsTo(LeaveType::class);
    }
}
