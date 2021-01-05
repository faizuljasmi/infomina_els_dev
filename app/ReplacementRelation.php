<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReplacementRelation extends Model
{
    protected $table = 'els_replacement_claim_apply_relations';

    public function claim(){
        return $this->belongsTo(LeaveApplication::class,'claim_id');
    }

    public function application(){
        return $this->belongsTo(LeaveApplication::class,'leave_id');
    }
}
