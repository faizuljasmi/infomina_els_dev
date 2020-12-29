<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use App\Notifications\PasswordReset;


class User extends Authenticatable
{
    use Notifiable, Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'staff_id','branch_id', 'email', 'password', 'user_type', 'emp_type_id', 'emp_group_id', 'emp_group_two_id', 'emp_group_three_id', 'emp_group_four_id', 'emp_group_five_id', 'join_date', 'gender',
        'job_title', 'emergency_contact_name', 'emergency_contact_no',
    ];

    public $sortable = [
        'id',
        'staff_id',
        'name',
        'user_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }

    /**
     * Add a mutator to ensure hashed passwords
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    //One User has one employee type
    public function emp_types()
    {
        return $this->belongsTo(EmpType::class, 'emp_type_id');
    }

    //One User has one employee group
    public function emp_group()
    {
        return $this->belongsTo(EmpGroup::class, 'emp_group_id');
    }
    //One User has one employee group
    public function emp_group_two()
    {
        return $this->belongsTo(EmpGroup::class, 'emp_group_two_id');
    }
    //One User has one employee group
    public function emp_group_three()
    {
        return $this->belongsTo(EmpGroup::class, 'emp_group_three_id');
    }
    //One User has one employee group
    public function emp_group_four()
    {
        return $this->belongsTo(EmpGroup::class, 'emp_group_four_id');
    }
    //One User has one employee group
    public function emp_group_five()
    {
        return $this->belongsTo(EmpGroup::class, 'emp_group_five_id');
    }

    //One User can lead one group
    public function group_lead()
    {
        return $this->belongsTo(EmpGroup::class);
    }

    //One User has many leave applications
    public function leave_applications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function leave_earnings()
    {
        return $this->hasMany(LeaveEarning::class);
    }

    public function brought_forward_leaves()
    {
        return $this->hasMany(BroughtForwardLeave::class);
    }

    public function burnt_leaves()
    {
        return $this->hasMany(BurntLeave::class);
    }

    public function taken_leaves()
    {
        return $this->hasMany(TakenLeave::class);
    }

    public function leave_balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    //One user has one set of approval authority
    public function approval_authority()
    {
        return $this->hasOne(ApprovalAuthority::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function state_holidays(){
        return $this->branch->state->state_wide_holidays();
    }

    public function national_holidays(){
        return $this->branch->country->country_wide_holidays();
    }
}
