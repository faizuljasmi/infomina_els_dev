<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WspaceUser extends Model
{
    protected $table = '_users';

    public function els_user(){
        return $this->hasOne(User::class,'id','els_id');
    }

    public function single_metas(){
        return $this->hasMany(UserSingleMeta::class,'entity_id');
    }

    public function staffid(){
        return $this->single_metas()->where('meta','staff_id');
    }

    public function getStaffIdAttribute(){
        return $this->staffid()->first()->val;
    }

    public function jobtitle(){
        return $this->single_metas()->where('meta','job_title');
    }

    public function getJobTitleAttribute(){
        return $this->jobtitle()->first()->val;
    }

    public function joindate(){
        return $this->single_metas()->where('meta','join_date');
    }

    public function getJoinDateAttribute(){
        return $this->joindate()->first()->val;
    }

    public function dob(){
        return $this->single_metas()->where('meta','dob');
    }

    public function getDobAttribute(){
        return $this->dob()->first()->val;
    }

    public function maritalstatus(){
        return $this->single_metas()->where('meta','marital_status');
    }

    public function getMaritialStatusAttribute(){
        return $this->maritalstatus()->first()->val;
    }

    public function gender(){
        return $this->single_metas()->where('meta','gender');
    }

    public function getGenderAttribute(){
        return $this->gender()->first()->val;
    }

    public function emergencycontactname(){
        return $this->single_metas()->where('meta','emergency_contact_name_1');
    }

    public function getEmergencyContactNameAttribute(){
        return $this->emergencycontactname()->first()->val;
    }

    public function emergencycontactno(){
        return $this->single_metas()->where('meta','emergency_contact_no_1');
    }

    public function getEmergencyContactNoAttribute(){
        return $this->emergencycontactno()->first()->val;
    }
    
}
