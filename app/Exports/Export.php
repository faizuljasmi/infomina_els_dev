<?php

namespace App\Exports;

use App\User;
use App\LeaveApplication;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
//use Maatwebsite\Excel\Concerns\WithMultipleSheets;
//use Maatwebsite\Excel\Concerns\WithTitle;


class Export implements FromCollection, WithHeadings
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return LeaveApplication::select('id', 'leave_type_id', 'date_from', 'date_to', 
        'date_resume', 'total_days', 'reason', 'status', 'emergency_contact')
        ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Leave Type',
            'Date From',
            'Date To',
            'Date Resume',
            'Total Days',
            'Reason',
            'Status',
            'Emergency Contact',
        ];
    }
}