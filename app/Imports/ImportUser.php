<?php

namespace App\Imports;

use App\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportUser implements ToModel, WithValidation, WithHeadingRow
{
    Use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function rules(): array
    {
        return [
            'staff_id' => Rule::unique('users', 'staff_id'),
            'email' => Rule::unique('users', 'email'), // (Table Name, Field in DB)
        ];
    }

    public function customValidationMessages()
    {
        return [
            'staff_id.unique' => 'Staff Id already exist!',
            'email.unique' => 'Email already exist!',
        ];
    }

    public function model(array $row)
    {
        $user = new User();
        $user->name = $row['name'];
        if($row['name'] == null){  //Handle null.
            return null;
        }
        $user->staff_id = $row['staff_id'];
        $user->email = $row['email'];
        $user->password = $row['password'];
        $user->user_type = $row['user_type'];
        $user->emp_type_id = $row['emp_type_id'];
        $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(@$row['join_date']));// Change format, cast.
        $user->join_date = $date;
        $user->job_title = $row['job_title'];
        $user->gender = $row['gender'];
        $user->emp_group_id = $row['group_type_1'];
        $user->emp_group_two_id = $row['group_type_2'];
        $user->emp_group_three_id = $row['group_type_3'];
        $user->emp_group_four_id = $row['group_type_4'];
        $user->emp_group_five_id = $row['group_type_5'];
        return $user;
        
    }
}
