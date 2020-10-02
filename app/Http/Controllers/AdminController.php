<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\Import;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Datatables;
use DB;
use Input;
use App\User;
use App\History;
use App\LeaveType;
use App\LeaveApplication;
use App\LeaveBalance;
use App\LeaveEarning;
use App\TakenLeave;
use App\ApprovalAuthority;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\Notifications\StatusUpdate;
use App\BroughtForwardLeave;
use App\BurntLeave;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::sortable()
        ->join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name', 'leave_applications.id as leave_app_id', 'leave_applications.created_at as created')
        ->orderby('leave_applications.created_at', 'DESC')
        ->paginate(15);

        // dd($users);

        $count_approve = LeaveApplication::where('leave_applications.status','like','%APPROVED%')->count();

        $count_cancel = LeaveApplication::where('leave_applications.status','like','%CANCELLED%')->count();

        $count_pending = LeaveApplication::where('leave_applications.status','like','%PENDING_1%')
        ->orwhere('leave_applications.status','like','%PENDING_2%')
        ->orwhere('leave_applications.status','like','%PENDING_3%')
        ->count();

        $count_reject = LeaveApplication::where('leave_applications.status','like','%DENIED_1%')
        ->orwhere('leave_applications.status','like','%DENIED_2%')
        ->orwhere('leave_applications.status','like','%DENIED_3%')
        ->count();

        $count_all = LeaveApplication::count();

        $current_user = auth()->user()->id;

        $edited_by = User::where('users.id', $current_user)
        ->first();

        $approver_1 = ApprovalAuthority::join('users', 'users.id', '=', 'approval_authorities.authority_1_id')
        ->select('users.name')->get();

        // dd($approver_1);


        return view('admin/report')->with(compact('users', 'count_approve', 'count_pending', 'count_reject', 'count_cancel', 'count_all', 'edited_by', 'approver_1'));
    }

    public function change_status(Request $request)
    {
        $new_status = $request->get('change_status');
        $app_id = $request->get('status_app_id');
        $status_remarks = $request->get('status_remarks');

        $leave_app = LeaveApplication::where('id', $app_id)
        ->first();

        $leave_bal = LeaveBalance::where('user_id', $leave_app->user_id)
        ->where('leave_type_id', $leave_app->leave_type_id)
        ->first();

        $leave_earn = LeaveEarning::where('user_id', $leave_app->user_id)
        ->where('leave_type_id', '12')
        ->first();

        $hosp_balance = LeaveBalance::where('user_id', $leave_app->user_id)
        ->where('leave_type_id', '4')
        ->first();

        $annual_balance = LeaveBalance::where('user_id', $leave_app->user_id)
        ->where('leave_type_id', '1')
        ->first();

        $taken_leave = TakenLeave::where('user_id', $leave_app->user_id)
        ->where('leave_type_id', $leave_app->leave_type_id)
        ->first();

        if ( $new_status != "") {
            if ( $new_status == "APPROVE" ) {
                $leave_app->status = "4";

                if ( $leave_app->leave_type_id != '12' ) { // If leave type is not replacement leave
                    if($leave_app->total_days > $leave_bal->no_of_days){
                        return back()->with('error', 'Employee does not have enough leave balance');
                    }
                    $leave_bal->no_of_days -= $leave_app->total_days; // Deduct the days in leave balances
                    $taken_leave->no_of_days += $leave_app->total_days; // Add days in leaves taken
                } else {
                    $leave_earn->no_of_days += $leave_app->total_days; // Add days in leave earning
                    $annual_balance->no_of_days += $leave_app->total_days; // Also add days in annual leave balances
                }

                if ( $leave_app->leave_type_id == '3') { // If leave type is sick leave
                    $hosp_balance->no_of_days -= $leave_app->total_days; // Deduct also in hospitalization leaves
                }

                if ( $leave_app->leave_type_id == '6') { // If leave type is emergency leave
                    if($leave_app->total_days >  $annual_balance->no_of_days){
                        return back()->with('error', 'Employee does not have enough leave balance');
                    }
                    $annual_balance->no_of_days -= $leave_app->total_days; // Deduct also in annual leaves
                }

            } else if ( $new_status == "REJECT" ) {

                if ( $leave_app->status == "APPROVED" ) { // If existing status is approved

                    if ( $leave_app->leave_type_id != '12' ) { // If leave type is not replacement leave
                        $leave_bal->no_of_days += $leave_app->total_days; // Then add back the days to leave balances
                        $taken_leave->no_of_days -= $leave_app->total_days; // Deduct days in leaves taken
                    }

                    if ( $leave_app->leave_type_id == '3') { // If leave type is sick leave
                        $hosp_balance->no_of_days += $leave_app->total_days; // Add also in hospitalization leaves
                    }

                    if ( $leave_app->leave_type_id == '6') { // If leave type is emergency leave
                        $annual_balance->no_of_days += $leave_app->total_days; // Add also in annual leaves
                    }
                }
                $leave_app->status = "7";

            } else if ( $new_status == "CANCEL" ) {

                if ( $leave_app->status == "APPROVED" ) { // If existing status is approved

                    if ( $leave_app->leave_type_id != '12' ) { // If leave type is not replacement leave
                        $leave_bal->no_of_days += $leave_app->total_days; // Then add back the days to leave balances
                        $taken_leave->no_of_days -= $leave_app->total_days; // Add days in leaves taken
                    }

                    if ( $leave_app->leave_type_id == '3') { // If leave type is sick leave
                        $hosp_balance->no_of_days += $leave_app->total_days; // Add also in hospitalization leaves
                    }

                    if ( $leave_app->leave_type_id == '6') { // If leave type is emergency leave
                        $annual_balance->no_of_days += $leave_app->total_days; // Add also in annual leaves
                    }
                }
                $leave_app->status = "8";
            }

            $leave_app->save();
            $leave_earn->save();
            $leave_bal->save();
            $hosp_balance->save();
            $annual_balance->save();
            $taken_leave->save();

            $hist = new History;
            $hist->leave_application_id = $app_id;
            $hist->user_id = auth()->user()->id;
            $hist->remarks = $status_remarks;

            $user = User::where('id', $leave_app->user_id)->get();

            if ( $new_status == "APPROVE" ) {
                $hist->action = "Approved";
                $leave_app->user->notify(new StatusUpdate($leave_app)); // emails
            } else if ( $new_status == "REJECT" ) {
                $hist->action = "Rejected";
            } else if ( $new_status == "CANCEL" ) {
                $hist->action = "Cancelled";
            }

            $hist->save();
        }

        return back();
    }

    public function view_approver(Request $request)
    {
        $getdata = $request->json()->all();
        $app_id = $getdata[0];

        $approver_name = User::where('users.id', $app_id)->select('users.name')->first();

        return response()->json(['approver_name' => $approver_name]);
    }

    public function view_history(Request $request)
    {
        $getdata = $request->json()->all();
        $app_id = $getdata[0];

        $history = History::leftjoin('users', 'users.id', '=', 'histories.user_id')
        ->where('histories.leave_application_id', $app_id)
        ->select('histories.*', 'users.*', 'histories.created_at as created')
        ->get();

        return response()->json(['history' => $history]);
    }

    public function autocomplete(Request $request)
    {
        $search_name = $request->get('name');

        $result = User::where('users.name','like','%'.$search_name.'%')->get();

        return response()->json($result);
    }


    public function search(Request $request)
    {
        $search_name = $request->get('name');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $leave_type = $request->get('leave_type');
        $leave_status = $request->get('leave_status');

        $count_approve = LeaveApplication::where('leave_applications.status','like','%APPROVED%')->count();

        $count_cancel = LeaveApplication::where('leave_applications.status','like','%CANCELLED%')->count();

        $count_pending = LeaveApplication::where('leave_applications.status','like','%PENDING_1%')
        ->orwhere('leave_applications.status','like','%PENDING_2%')
        ->orwhere('leave_applications.status','like','%PENDING_3%')
        ->count();

        $count_reject = LeaveApplication::where('leave_applications.status','like','%DENIED_1%')
        ->orwhere('leave_applications.status','like','%DENIED_2%')
        ->orwhere('leave_applications.status','like','%DENIED_3%')
        ->count();

        $count_all = LeaveApplication::count();

        $current_user = auth()->user()->id;

        $edited_by = User::where('users.id', 'like', '%'.$current_user.'%')
        ->first();

        $query = User::sortable()
        ->join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->join('approval_authorities', 'approval_authorities.user_id', '=', 'users.id')
        ->select('users.*', 'leave_applications.*', 'approval_authorities.*', 'leave_types.name as leave_type_name', 'leave_applications.id as leave_app_id', 'leave_applications.created_at as created')
        ->orderby('leave_applications.created_at', 'DESC');

        if($request->get('name') != '') {
            $query->where('users.name','like','%'.$search_name.'%');
        }

        if($request->get('date_from') != '' && $request->get('date_to') != '') {
            $query->wherebetween('leave_applications.date_from', [$date_from, $date_to]);
            $query->orwherebetween('leave_applications.date_to', [$date_from, $date_to]);
        }

        if($request->get('leave_type') != '') {
            $query->where('leave_applications.leave_type_id', $leave_type);
        }

        if($request->get('leave_status') != '') {
            if($request->get('leave_status') == 'PENDING') {
                $query->where('leave_applications.status','like','%PENDING_1%');
                $query->orwhere('leave_applications.status','like','%PENDING_2%');
                $query->orwhere('leave_applications.status','like','%PENDING_3%');
            }
            if($request->get('leave_status') == 'DENIED') {
                $query->where('leave_applications.status','like','%DENIED_1%');
                $query->orwhere('leave_applications.status','like','%DENIED_2%');
                $query->orwhere('leave_applications.status','like','%DENIED_3%');
            }
            else {
                $query->where('leave_applications.status','like','%'.$leave_status.'%');
            }
        }

        $users = $query->paginate(15);

        return view('admin/report')->with(compact('users', 'search_name', 'date_from', 'date_to', 'leave_type', 'leave_status',
        'count_approve', 'count_pending', 'count_reject', 'count_cancel', 'count_all', 'edited_by'));
    }

    public function chart()
    {
        $count_all = LeaveApplication::count();

        $count_approve = LeaveApplication::where('leave_applications.status','like','%APPROVED%')->count();

        $count_cancel = LeaveApplication::where('leave_applications.status','like','%CANCELLED%')->count();

        $count_pending = LeaveApplication::where('leave_applications.status','like','%PENDING_1%')
        ->orwhere('leave_applications.status','like','%PENDING_2%')
        ->orwhere('leave_applications.status','like','%PENDING_3%')
        ->count();

        $count_reject = LeaveApplication::where('leave_applications.status','like','%DENIED_1%')
        ->orwhere('leave_applications.status','like','%DENIED_2%')
        ->orwhere('leave_applications.status','like','%DENIED_3%')
        ->count();

        return view('admin/chart')->with(compact('count_approve', 'count_pending', 'count_reject', 'count_cancel', 'count_all'));
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'import_file'  => 'required|mimes:xls,xlsx'
           ]);

        Excel::import(new Import(), request()->file('import_file'));

        return back()->with('success', 'Data imported successfully.');
    }

    public function export_all()
    {
        $users = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name', 'leave_applications.created_at as created')
        ->orderby('leave_applications.created_at', 'DESC')
        ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('A1', 'No.');
        $sheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('B1', 'Name');
        $sheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('C1', 'Day(s)');
        $sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('D1', 'Type');
        $sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E1', 'From Date');
        $sheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('F1', 'To Date');
        $sheet->getStyle('G')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('G1', 'Resume Date');
        $sheet->getStyle('H')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('H1', 'Reason');
        $sheet->getStyle('I')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('I1', 'Status');
        $sheet->getStyle('J')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('J1', 'Date Apply');
        $rows = 2;

        $countapp = count($users);
        $count = 1;

        for($d=0; $d<$countapp; $d++) {
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->setCellValue('A' . $rows, $count++);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->setCellValue('B' . $rows, $users[$d]->name);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->setCellValue('C' . $rows, $users[$d]->total_days);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->setCellValue('D' . $rows, $users[$d]->leave_type_name);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->setCellValue('E' . $rows, $users[$d]->date_from);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->setCellValue('F' . $rows, $users[$d]->date_to);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->setCellValue('G' . $rows, $users[$d]->date_resume);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->setCellValue('H' . $rows, $users[$d]->reason);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            $sheet->setCellValue('I' . $rows, $users[$d]->status);
            $sheet->getColumnDimension('J')->setAutoSize(true);
            $sheet->setCellValue('J' . $rows, \Carbon\Carbon::parse($users[$d]->created_at)->isoFormat('Y-MM-DD'));
            $rows++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Leave_Applications_All.xlsx"');
        $writer->save("php://output");

    }

    public function export_search(Request $request)
    {
        $search_name = $request->get('excel_name');
        $date_from = $request->get('excel_date_from');
        $date_to = $request->get('excel_date_to');
        $leave_type = $request->get('excel_leave_type');
        $leave_status = $request->get('excel_leave_status');

        $query = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name', 'leave_applications.created_at as created')
        ->orderby('leave_applications.created_at', 'DESC');

        if($request->get('excel_name') != '') {
            $query->where('users.name','like','%'.$search_name.'%');
        }

        if($request->get('excel_date_from') != '' && $request->get('excel_date_to') != '') {
            $query->wherebetween('leave_applications.date_from', [$date_from, $date_to]);
        }

        if($request->get('excel_leave_type') != '') {
            $query->where('leave_applications.leave_type_id','like','%'.$leave_type.'%');
        }

        if($request->get('excel_leave_status') != '') {
            if($request->get('leave_status') == 'PENDING') {
                $query->where('leave_applications.status','like','%PENDING_1%');
                $query->orwhere('leave_applications.status','like','%PENDING_2%');
                $query->orwhere('leave_applications.status','like','%PENDING_3%');
            }
            if($request->get('excel_leave_status') == 'DENIED') {
                $query->where('leave_applications.status','like','%DENIED_1%');
                $query->orwhere('leave_applications.status','like','%DENIED_2%');
                $query->orwhere('leave_applications.status','like','%DENIED_3%');
            }
            else {
                $query->where('leave_applications.status','like','%'.$leave_status.'%');
            }
        }

        $users = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('A1', 'No.');
        $sheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('B1', 'Name');
        $sheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('C1', 'Day(s)');
        $sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('D1', 'Type');
        $sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E1', 'From Date');
        $sheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('F1', 'To Date');
        $sheet->getStyle('G')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('G1', 'Resume Date');
        $sheet->getStyle('H')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('H1', 'Reason');
        $sheet->getStyle('I')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('I1', 'Status');
        $sheet->getStyle('J')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('J1', 'Date Apply');
        $rows = 2;

        $countapp = count($users);
        $count = 1;

        for($d=0; $d<$countapp; $d++) {
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->setCellValue('A' . $rows, $count++);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->setCellValue('B' . $rows, $users[$d]->name);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->setCellValue('C' . $rows, $users[$d]->total_days);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->setCellValue('D' . $rows, $users[$d]->leave_type_name);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->setCellValue('E' . $rows, $users[$d]->date_from);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->setCellValue('F' . $rows, $users[$d]->date_to);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->setCellValue('G' . $rows, $users[$d]->date_resume);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->setCellValue('H' . $rows, $users[$d]->reason);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            $sheet->setCellValue('I' . $rows, $users[$d]->status);
            $sheet->getColumnDimension('J')->setAutoSize(true);
            $sheet->setCellValue('J' . $rows, \Carbon\Carbon::parse($users[$d]->created_at)->isoFormat('Y-MM-DD'));
            $rows++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Leave_Applications.xlsx"');
        $writer->save("php://output");

    }

    public function export_leave_balance()
    {
        $annual = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '1' )->get();

        $annual_total = User::leftjoin('leave_entitlements', 'leave_entitlements.emp_type_id', '=', 'users.emp_type_id')
        ->where('leave_entitlements.leave_type_id', '=', '1' )->get();

        $brought_forw = User::leftjoin('brought_forward_leaves', 'brought_forward_leaves.user_id', '=', 'users.id')
        ->where('brought_forward_leaves.leave_type_id', '=', '1' )->get();

        $calamity = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '2' )->get();

        $sick = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '3' )->get();

        $hospitalization = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '4' )->get();

        $compassionate = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '5' )->get();

        $emergency = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '6' )->get();

        $marriage = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '7' )->get();

        $maternity = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '8' )->get();

        $paternity = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '9' )->get();

        $traning = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '10' )->get();

        $unpaid = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '11' )->get();

        $replacement = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '12' )->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('A1', 'No.');
        $sheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('B1', 'Name');
        $sheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('C1', 'Staff ID');
        $sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('D1', 'Annual');
        $sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E1', 'Calamity');
        $sheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('F1', 'Sick');
        $sheet->getStyle('G')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('G1', 'Hospitalization');
        $sheet->getStyle('H')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('H1', 'Compassionate');
        $sheet->getStyle('I')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('I1', 'Emergency');
        $sheet->getStyle('J')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('J1', 'Marriage');
        $sheet->getStyle('K')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('K1', 'Maternity');
        $sheet->getStyle('L')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('L1', 'Paternity');
        $sheet->getStyle('M')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('M1', 'Traning');
        $sheet->getStyle('N')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('N1', 'Unpaid');
        $sheet->getStyle('O')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('O1', 'Replacement');
        $sheet->getStyle('P')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('P1', 'Total Annaul Ent');
        $sheet->getStyle('Q')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('Q1', 'Total Brought Forw');
        $rows = 2;

        $countapp = count($annual);
        $count = 1;

        for($d=0; $d<$countapp; $d++) {
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->setCellValue('A' . $rows, $count++);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->setCellValue('B' . $rows, $annual[$d]->name);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->setCellValue('C' . $rows, $annual[$d]->staff_id);
            if ( $annual ) {
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->setCellValue('D' . $rows, $annual[$d]->no_of_days);
            }
            if ( $calamity ) {
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->setCellValue('E' . $rows, $calamity[$d]->no_of_days);
            }
            if ( $sick ) {
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->setCellValue('F' . $rows, $sick[$d]->no_of_days);
            }
            if ( $hospitalization ) {
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->setCellValue('G' . $rows, $hospitalization[$d]->no_of_days);
            }
            if ( $compassionate ) {
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->setCellValue('H' . $rows, $compassionate[$d]->no_of_days);
            }
            if ( $emergency ) {
                $sheet->getColumnDimension('I')->setAutoSize(true);
                $sheet->setCellValue('I' . $rows, $emergency[$d]->no_of_days);
            }
            if ( $marriage ) {
                $sheet->getColumnDimension('J')->setAutoSize(true);
                $sheet->setCellValue('J' . $rows, $marriage[$d]->no_of_days);
            }
            if ( $maternity ) {
                $sheet->getColumnDimension('K')->setAutoSize(true);
                $sheet->setCellValue('K' . $rows, $maternity[$d]->no_of_days);
            }
            if ( $paternity ) {
                $sheet->getColumnDimension('L')->setAutoSize(true);
                $sheet->setCellValue('L' . $rows, $paternity[$d]->no_of_days);
            }
            if ( $traning ) {
                $sheet->getColumnDimension('M')->setAutoSize(true);
                $sheet->setCellValue('M' . $rows, $traning[$d]->no_of_days);
            }
            if ( $unpaid ) {
                $sheet->getColumnDimension('N')->setAutoSize(true);
                $sheet->setCellValue('N' . $rows, $unpaid[$d]->no_of_days);
            }
            if ( $replacement ) {
                $sheet->getColumnDimension('O')->setAutoSize(true);
                $sheet->setCellValue('O' . $rows, $replacement[$d]->no_of_days);
            }
            if ( $annual_total ) {
                $sheet->getColumnDimension('P')->setAutoSize(true);
                $sheet->setCellValue('P' . $rows, $annual_total[$d]->no_of_days);
            }
            if ( $brought_forw ) {
                $sheet->getColumnDimension('Q')->setAutoSize(true);
                $sheet->setCellValue('Q' . $rows, $brought_forw[$d]->no_of_days);
            }
            $rows++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Leave_Balance.xlsx"');
        $writer->save("php://output");

    }

    public function deduct_burnt(){
        $users = User::where('status','Active')->get();
        $bfwd = BroughtForwardLeave::where('leave_type_id',1)->where('no_of_days','>',0)->get();

        foreach($users as $user){
            foreach($bfwd as $bf){
                if($user->id == $bf->user_id){
                    $ann_taken_first_half = LeaveApplication::where('user_id',$user->id)->where('status','Approved')->where('leave_type_id',1)->where('created_at' ,'<=', '2020-06-30')->get();
                    $cur_ann_leave_bal = LeaveBalance::where('user_id',$user->id)->where('leave_type_id',1)->first();
                    $total_days = 0;
                    foreach($ann_taken_first_half as $ann){
                        $total_days += $ann->total_days;
                    }
                    $bf_balance = $bf->no_of_days - $total_days;
                    if($bf_balance < 0){
                        $bf_balance = 0;
                    }
                    $new_ann_balance = $cur_ann_leave_bal->no_of_days - $bf_balance;
                    $cur_ann_leave_bal->no_of_days = $new_ann_balance;
                    $cur_ann_leave_bal->save();

                    $burnt = new BurntLeave;
                    $burnt->leave_type_id = 1;
                    $burnt->user_id = $user->id;
                    $burnt->no_of_days = $bf_balance;
                    $burnt->save();
                    //dd("Total Annual Taken ".$total_days." Brought Forward ".$bf->no_of_days." Annual Balance ".$user->leave_balances[0]->no_of_days);
                }
            }
        }
        dd("Done");
    }
}
