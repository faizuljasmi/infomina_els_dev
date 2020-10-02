<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class LeaveApplication extends Model
{
    //use Notifiable;
    use Sortable;

    //Declare Fillable
    protected $fillable = [
        'date_from','date_to', 'date_resume',
        'total_days', 'reason', 'relief_personnel_id',
        'status',
        'attachment','emergency_contact',
    ];

    public $sortable = [
        'id',
        'created_at',
        'updated_at',
        'date_from',
        'date_to',
        'total_days',
        'status'
    ];

    //One leave app has belongs to one user
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    //One leave app has many histories
    public function histories(){
        return $this->hasMany(History::class);
    }

    //One leave app has one leave type
    public function leaveType(){
        return $this->hasOne(LeaveType::class,'id','leave_type_id');
    }

    //One leave app has one 1st auth
    public function approver_one(){
        return $this->hasOne(User::class,'id','approver_id_1');
    }

    //One leave app has one 2nd auth
    public function approver_two(){
        return $this->hasOne(User::class, 'id','approver_id_2');
    }

    //One leave app has one 3rd auth
    public function approver_three(){
        return $this->hasOne(User::class,'id','approver_id_3');
    }

    //One leave app has one releif personnel
    public function relief_personnel(){
        return $this->hasOne(User::class,'id','relief_personnel_id');
    }

    //One leave app has one canceller
    public function remarker(){
        return $this->hasOne(User::class,'id','remarker_id');
    }

    public function getAttachmentUrlAttribute(){
        return $this->attributes['attachment'] ? url('/storage/'.$this->attributes['attachment']) : 'https://placehold.it/900x300';
      }

}
