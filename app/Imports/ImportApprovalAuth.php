<?php

namespace App\Imports;

use App\User;
use App\ApprovalAuthority;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportApprovalAuth implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {  
        
        $user = User::orderBy('id','ASC')->where('name', $row['name'])->first();
        if($user == null){
            return null;
        }
        $user_id = $user->id;
        
        
        $appid1 = User::orderBy('id', 'ASC')->where("name", $row['approval_1'])->first();
        if($appid1 == null){
            $app1 = null;
        }
        else{
        $app1 = $appid1->id;
        }

        $appid2 = User::orderBy('id', 'ASC')->where("name", $row['approval_2'])->first();
        if($appid2 == null){
            $app2 = null;
        }
        else{
        $app2 = $appid2->id;
        }

        $appid3 = User::orderBy('id', 'ASC')->where("name", $row['approval_3'])->first();
        if($appid3 == null){
            $app3 = null;
        }
        else{
        $app3 = $appid3->id;
        }

        $apau = new ApprovalAuthority();
        $apau->user_id = $user_id;
        $apau->authority_1_id = $app1;
        $apau->authority_2_id = $app2;
        $apau->authority_3_id = $app3;
        return $apau;
    }
}
