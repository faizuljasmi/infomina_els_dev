<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovalAuthority extends Model
{
    protected $fillable = [
        'user_id','authority_1_id','authority_2_id','authority_3_id',
    ];

    //One set of approval authorities belongs to one user
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    //Relation with model user for 1st,2nd, and 3rd authority
    public function authority_one(){
        return $this->hasOne(User::class,'id','authority_1_id');
    }
    public function authority_two(){
        return $this->hasOne(User::class,'id','authority_2_id');
    }
    public function authority_three(){
        return $this->hasOne(User::class,'id','authority_3_id');
    }

    //Need Check. To get Authority One's Name
    // public function getAuthorityOneAttribute(){
    //     return isset($this->user) ? $this->user->name : '';
    // }
}
