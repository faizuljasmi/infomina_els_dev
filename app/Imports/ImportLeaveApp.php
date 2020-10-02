<?php

namespace App\Imports;

use Carbon\Carbon;
use App\User;
use App\ApprovalAuthority;
use App\LeaveType;
use App\LeaveApplication;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportLeaveApp implements ToModel, WithHeadingRow
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
        //dd($user_id);

            //TODO LEAVETYPE
            $ltype = LeaveType::orderBy('id','ASC')->where('name', $row['leave_type'])->first();
            if($ltype == null){
                return null;
            }
            $leave_type_id = $ltype->id;

            //TODO APPROVAL AUTHORITY
            $appAuth = ApprovalAuthority::orderBy('id','ASC')->where('user_id', $user_id)->first();
            if($appAuth == null){
                return null;
            }
            $app1 = $appAuth->authority_1_id;
            $app2 = $appAuth->authority_2_id;
            $app3 = $appAuth->authority_3_id;
       
            $lapp = new LeaveApplication();
            //Get from users table
            $lapp->user_id = $user_id;
            //See leave name and get id from leave_type table
            $lapp->leave_type_id = $leave_type_id;
            //See user_id and get approval authorities from approval_authorities
            $lapp->approver_id_1 = $app1;
            $lapp->approver_id_2 = $app2;
            $lapp->approver_id_3 = $app3;
            $datefrom = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(@$row['date_from']));// Change format, cast.
            $lapp->date_from = $datefrom;
            $dateto = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(@$row['date_to']));// Change format, cast.
            $lapp->date_to = $dateto;
            $dateres = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(@$row['date_resume']));// Change format, cast.
            $lapp->date_resume = $dateres;
            $lapp->total_days = @$row['total_days'];
            $lapp->reason = @$row['reason'];
            $lapp->emergency_contact_name = @$row['emergency_contact'];
            $lapp->emergency_contact_no = @$row['emergency_contact_no'];
            $lapp->relief_personnel_id = @$row['relief_personnel_id'];
            $lapp->apply_for = @$row['apply_for'];
            $lapp->status = @$row['status'];
            return $lapp;
    }
}

