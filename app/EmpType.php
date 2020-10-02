<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\LeaveEntitlement;

class EmpType extends Model
{
    //
    protected $fillable = [
        'name',
        // 'ent_annual', 
        //  'ent_calamity' ,
        //  'ent_carryfwd', 
        //  'ent_compassionate' ,
        //  'ent_emergency', 
        //  'ent_hospitalization' ,
        //  'ent_marriage' ,
        //  'ent_maternity', 
        //  'ent_paternity', 
        //  'ent_sick' ,
        //  'ent_training', 
        //  'ent_unpaid' ,
    ];

    //One Employee type has many users
    public function users(){
        return $this->hasMany(User::class, 'id');
    }

    //One employee type has many entitlements
    public function entitlements(){
        return $this->hasMany(LeaveEntitlement::class);
    }
}
