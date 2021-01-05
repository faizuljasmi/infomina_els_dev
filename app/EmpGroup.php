<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpGroup extends Model
{
    protected $table = 'els_emp_groups';

    protected $fillable = [
        'name', 'group_leader_id',

    ];

    //One employee group has many users
    public function users(){
       return $this->hasMany(User::class,'id');
    }

    public function leader(){
        return $this->hasOne(User::class,'id','group_leader_id');
    }
}
