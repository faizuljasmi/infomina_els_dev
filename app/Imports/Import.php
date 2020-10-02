<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Import implements WithMultipleSheets 
{
    public function sheets(): array
    {
        return [
            'User' => new ImportUser(),
            'ApprovalAuth' => new ImportApprovalAuth(),
            'LeaveApplication' => new ImportLeaveApp(),
            'LeaveEarning' => new ImportLeaveEarn(),
        ];
    }
}