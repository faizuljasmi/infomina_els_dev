<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalanderRemark extends Model
{
    protected $table = 'els_calander_remarks';

    protected $fillable = [
        'remark_date_from', 'remark_date_from', 'remark_text', 'remark_by'
    ];

    //One leave app has one canceller
public function remarker(){
    return $this->hasOne(User::class,'id','remark_by');
}
}

