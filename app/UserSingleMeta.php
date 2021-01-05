<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSingleMeta extends Model
{
    protected $table = '_users_single_metas';

    public function user(){
        return $this->belongsTo(WspaceUser::class);
    }

}
