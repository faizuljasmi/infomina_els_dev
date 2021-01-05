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

    public function staff_id(){
        return $this->single_metas()->where('meta','staff_id');
    }
}
