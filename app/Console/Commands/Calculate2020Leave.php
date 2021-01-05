<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\LeaveEarning;
use App\LeaveBalance;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Calculate2020Leave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:2020';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '(NOT IN USE) To calculate 2020 prorated leave entitlements only.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Not in use anymore !
        $employees = User::get();

        $emps = [];
        
        foreach($employees as $emp)
        {
            $currentYear = '2020';

            $after36months = Carbon::parse($emp->join_date)->addMonths(36)->isoFormat('Y-MM-DD'); // Return String
            $after60months = Carbon::parse($emp->join_date)->addMonths(60)->isoFormat('Y-MM-DD'); // Return String

            $is3rdYear = substr($after36months, 0, 4); // Year
            $is5thYear = substr($after60months, 0, 4); // Year

            $prorateEnt = 0;
            $entAfter = 0;

            if ($is3rdYear == $currentYear) {
                $prorateEnt = 16;       
                $month = $after36months;
            } else if ($is5thYear == $currentYear) {
                $prorateEnt = 18;
                $month = $after60months;
            }

            $defaultEnt = 14; // Default entitlement for all staff is 14 days.

            if ($prorateEnt > 0 && $emp->id != 3 && $emp->id != 8 && $emp->id != 25 && $emp->id != 26) {
                $annMonth = substr($month, 5, 2); // Month
                $entBefore = ((intval($annMonth) - 1) / 12) * $defaultEnt; // To calculate days entitled before prorated months.
                $entBefore = round($entBefore);
                $entAfter = ((12 - (intval($annMonth) - 1)) / 12) * $prorateEnt; // To calculate days entitled for the prorated months.
                $entAfter = ceil($entAfter);
                
                // dd($annMonth, $entBefore, $entAfter);
                $tempEarn = 0;
                $tempBal = 0;
                
                $leaveEarn = LeaveEarning::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                if ($leaveEarn) {
                    $tempEarn = $leaveEarn->no_of_days;
                    $leaveEarn->no_of_days = ($tempEarn - $defaultEnt) + $entBefore + $entAfter; 
                    $leaveEarn->update();
                }
                
                $leaveBal = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                if ($leaveBal && $leaveEarn) {
                    $tempBal = $leaveBal->no_of_days;
                    $leaveBal->no_of_days = $leaveBal->no_of_days + ($leaveEarn->no_of_days) - $tempEarn;
                    $leaveBal->update();
                }

                $staff = (object) ['Name' => $emp->name, 'Before' => $tempEarn, 'After' => $leaveEarn->no_of_days];
                array_push($emps, $staff);
            }
        }

        print_r($staff);
    }
}

// $spreadsheet = new Spreadsheet();
// $sheet = $spreadsheet->getActiveSheet();
// $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $sheet->setCellValue('A1', 'ID');
// $sheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $sheet->setCellValue('B1', 'Name');
// $sheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $sheet->setCellValue('C1', 'Join Date');
// $sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $sheet->setCellValue('D1', 'Earned');
// $sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $sheet->setCellValue('E1', 'Balance');
// $sheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $sheet->setCellValue('F1', 'New Earning');
// $sheet->getStyle('G')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $sheet->setCellValue('G1', 'New Balance');

// $sheet->getColumnDimension('A')->setAutoSize(true);
// $sheet->setCellValue('A' . $rows, $emp->id);
// $sheet->getColumnDimension('B')->setAutoSize(true);
// $sheet->setCellValue('B' . $rows, $emp->name);
// $sheet->getColumnDimension('C')->setAutoSize(true);
// $sheet->setCellValue('C' . $rows, $emp->join_date);
// $sheet->getColumnDimension('D')->setAutoSize(true);
// $sheet->setCellValue('D' . $rows, $tempEarn);
// $sheet->getColumnDimension('E')->setAutoSize(true);
// $sheet->setCellValue('E' . $rows, $tempBal);
// $sheet->getColumnDimension('F')->setAutoSize(true);
// $sheet->setCellValue('F' . $rows, $leaveEarn->no_of_days);
// $sheet->getColumnDimension('G')->setAutoSize(true);
// $sheet->setCellValue('G' . $rows, $leaveBal->no_of_days);
// $rows++;

// $writer = new Xlsx($spreadsheet);
// $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
// header('Content-Type: application/vnd.ms-excel');
// header('Content-Disposition: attachment; filename="Leave_Applications_All.xlsx"');
// $writer->save("php://output");