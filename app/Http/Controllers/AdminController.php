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
use Auth;
use App\User;
use App\History;
use App\LeaveType;
use App\LeaveApplication;
use App\LeaveBalance;
use App\LeaveEarning;
use App\TakenLeave;
use App\ApprovalAuthority;
use App\LeaveEntitlement;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\Notifications\StatusUpdate;
use App\Notifications\ProrateUpdate;
use App\BroughtForwardLeave;
use App\BurntLeave;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search_name = $request->get('name');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $leave_type = $request->get('leave_type');
        $leave_status = $request->get('leave_status');

        $query = LeaveApplication::orderBy('els_leave_applications.id', 'DESC');

        if ($search_name) {
            $query->join('els_users', 'els_users.id', '=', 'els_leave_applications.user_id')
                  ->where('els_users.name', 'like', '%'.$search_name.'%')
                  ->select('els_leave_applications.*');
        }

        if ($date_from && $date_to) {
            $query->wherebetween('date_from', [$date_from, $date_to]);
            $query->orwherebetween('date_to', [$date_from, $date_to]);
        }

        if ($leave_type) {
            $query->where('leave_type_id', $leave_type);
        }

        if ($leave_status) {
            if ($leave_status == 'PENDING') {
                $query->where('status','like','PENDING_%');
            } else if ($leave_status == 'DENIED') {
                $query->where('status','like','DENIED_%');
            } else {
                $query->where('status', $leave_status);
            }
        }

        $leave_app = $query->sortable()->paginate(15);

        $count_approve = LeaveApplication::where('status','APPROVED')->count();

        $count_cancel = LeaveApplication::where('status','CANCELLED')->count();

        $count_pending = LeaveApplication::where('status','like','PENDING_%')->count();

        $count_reject = LeaveApplication::where('status','like','DENIED_%')->count();

        return view('admin/report')->with(compact('leave_app', 'count_approve', 'count_pending', 'count_reject', 'count_cancel', 'search_name', 'date_from', 'date_to', 'leave_type', 'leave_status',));
    }

    public function change_leave_status(Request $request)
    {
        $leave_id = $request->get('leave_id');
        $mode = $request->get('mode');
        $new_status = $request->get('new_status');
        $status_remarks = $request->get('status_remarks');
        // dd($leave_id);

        $leave_app = '';

        if ($leave_id) {
            if ($mode == 'isView') {
                $leave_app = LeaveApplication::where('id', $leave_id)->with('user', 'approver_one', 'approver_two', 'approver_three')->first();
            } else if ($mode == 'isEdit') {
                $leave_app = LeaveApplication::where('id', $leave_id)->first();

                $leave_bal = LeaveBalance::where('user_id', $leave_app->user_id)->where('leave_type_id', $leave_app->leave_type_id)->first();
                $leave_taken = TakenLeave::where('user_id', $leave_app->user_id)->where('leave_type_id', $leave_app->leave_type_id)->first();

                if ($new_status == 'REJECT' || $new_status == 'CANCEL') {
                    if ($new_status == 'REJECT') {
                        $leave_app->status = 'DENIED_3';
                    } else if ($new_status == 'CANCEL') {
                        $leave_app->status = 'CANCELLED';
                    }
                    // If existing status is approved.
                    if ( $leave_app->status == "APPROVED" ) { 

                        // For the rest of the leave type other than replacement leave.
                        if ( $leave_app->leave_type_id != '12' ) {
                            $leave_bal->no_of_days += $leave_app->total_days; // Add in balance.
                            $leave_taken->no_of_days -= $leave_app->total_days; // Substract in taken.
                        }
    
                        // If Replacement leave.
                        if ( $leave_app->leave_type_id == '12') { 
                            $replacement_earn = LeaveEarning::where('user_id', $leave_app->user_id)->where('leave_type_id', '12')->first();
                            $replacement_bal = LeaveBalance::where('user_id', $leave_app->user_id)->where('leave_type_id', '12')->first();
    
                            $replacement_earn->no_of_days -= $leave_app->total_days; // Subtract replacement leave earned.
                            $replacement_bal->no_of_days -= $leave_app->total_days; // Subtract replacement leave balance.
    
                            // Get the claim application related to this use replacement application.
                            $this_claim_apply = ReplacementRelation::where('leave_id', $leave_app->id)->first();
                            $claimApp = LeaveApplication::where('id', $this_claim_apply->claim_id)->first();
                            if($claimApp->status == "TAKEN"){
                                $claimApp->status = 'APPROVED';
                                $claimApp->save();
                            }
                            $this_claim_apply->delete();
                        }
    
                        // If Emergency leave.
                        if ($leave_app->leave_type_id == '6') {
                            $annual_bal = LeaveBalance::where('user_id', $leave_app->user_id)->where('leave_type_id', '1')->first();
                            $annual_bal->no_of_days += $leave_app->total_days;
                            $annual_bal->update();
                        }
                        // If Sick leave.
                        if ($leave_app->leave_type_id == '3') {
                            $hosp_bal = LeaveBalance::where('user_id', $leave_app->user_id)->where('leave_type_id', '4')->first();
                            $hosp_bal->no_of_days += $leave_app->total_days;
                            $hosp_bal->update();
                        }
                    }
                } else if ($new_status == 'APPROVE') {
                    $leave_app->status = 'APPROVED';
                    // For the rest of the leave type other than replacement leave.
                    if($leave_app->leave_type_id != '12'){
                        $leave_bal->no_of_days -= $leave_app->total_days; // Substract in balance.
                        $leave_taken->no_of_days += $leave_app->total_days; // Add in taken.
                    }

                    // If Replacement leave.
                    if ($leave_app->leave_type_id == '12') {
                        $replacement_earn = LeaveEarning::where('user_id', $leave_app->user_id)->where('leave_type_id', '12')->first();
                        $replacement_bal = LeaveBalance::where('user_id', $leave_app->user_id)->where('leave_type_id', '12')->first();

                        if ($leave_app->remarks == "Claim") {
                            $replacement_earn->no_of_days += $leave_app->total_days; // Add replacement leave earning.
                            $replacement_bal->no_of_days += $leave_app->total_days; // Add replacement leave balance.
                        }
                        else if ($leave_app->remarks == "Apply") {
                            $this_claim_apply = ReplacementRelation::where('leave_id', $leave_app->id)->first(); // Get the claim application related to this use replacement application.
                            $claimApp = LeaveApplication::where('id', $this_claim_apply->claim_id)->first();
                            $all_claim_apply = ReplacementRelation::where('claim_id', $this_claim_apply->claim_id)->get(); // Get related claim records.
                            $total_days = 0;

                            foreach($all_claim_apply as $aca) {
                                $leaveApp = LeaveApplication::where('id', $aca->leave_id)->first();
                                if($leaveApp->status != 'CANCELLED') {
                                    $total_days += $leaveApp->total_days;
                                }
                            }

                            //If the total days is fully used including this application, set the claim application status to TAKEN.
                            if ($total_days == $claimApp->total_days) {
                                $claimApp->status = "TAKEN";
                                $claimApp->save();
                            }
                            else if ($total_days > $claimApp->total_days) {
                                $leave_app->status = "CANCELLED";
                                $leave_app->save();
                            }
                            
                            $leave_taken->no_of_days += $leave_app->total_days; // Add replacement taken leave.
                            $replacement_bal->no_of_days -= $leave_app->total_days; // Minum from replacement balance
                        }

                        $replacement_earn->update();
                        $replacement_bal->update();
                    }
                    
                    // If Emergency leave.
                    if ($leave_app->leave_type_id == '6') {
                        $annual_bal = LeaveBalance::where('user_id', $leave_app->user_id)->where('leave_type_id', '1')->first();
                        $annual_bal->no_of_days -= $leave_app->total_days;
                        $annual_bal->update();
                    }

                    // If Sick leave.
                    if ($leave_app->leave_type_id == '3') {
                        $hosp_bal = LeaveBalance::where('user_id', $leave_app->user_id)->where('leave_type_id', '4')->first();
                        $hosp_bal->no_of_days -= $leave_app->total_days;
                        $hosp_bal->update();
                    }
                }

                $leave_app->update();
                $leave_bal->update();
                $leave_taken->update();

                $hist = new History;
                $hist->leave_application_id = $leave_id;
                $hist->user_id = auth()->user()->id;
                $hist->remarks = $status_remarks;

                if ( $new_status == "APPROVE" ) {
                    $hist->action = "Approved";
                    // $leave_app->user->notify(new StatusUpdate($leave_app)); // Email
                } else if ( $new_status == "REJECT" ) {
                    $hist->action = "Rejected";
                } else if ( $new_status == "CANCEL" ) {
                    $hist->action = "Cancelled";
                }

                $hist->save();
            }
        }
        
        return response()->json(['leave_app' => $leave_app]);
    }

    public function view_history(Request $request)
    {
        $leave_id = $request->get('leave_id');

        $histories = History::where('leave_application_id', $leave_id)->with('editor')->get();

        return response()->json(['histories' => $histories]);
    }

    public function autocomplete(Request $request)
    {
        $user_name = $request->get('name');

        $user_list = User::where('name','like','%'.$user_name.'%')->get();

        return response()->json($user_list);
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'import_file'  => 'required|mimes:xls,xlsx'
           ]);

        Excel::import(new Import(), request()->file('import_file'));

        return back()->with('success', 'Data imported successfully.');
    }

    public function export(Request $request) 
    {
        $search_name = $request->get('excel_name');
        $date_from = $request->get('excel_date_from');
        $date_to = $request->get('excel_date_to');
        $leave_type = $request->get('excel_leave_type');
        $leave_status = $request->get('excel_leave_status');

        $query = LeaveApplication::orderBy('els_leave_applications.id', 'DESC');

        if ($search_name) {
            $query->join('els_users', 'els_users.id', '=', 'els_leave_applications.user_id')
                  ->where('els_users.name', 'like', '%'.$search_name.'%')
                  ->select('els_leave_applications.*');
        }

        if ($date_from && $date_to) {
            $query->wherebetween('date_from', [$date_from, $date_to]);
            $query->orwherebetween('date_to', [$date_from, $date_to]);
        }

        if ($leave_type) {
            $query->where('leave_type_id', $leave_type);
        }

        if ($leave_status) {
            if ($leave_status == 'PENDING') {
                $query->where('status','like','PENDING_%');
            } else if ($leave_status == 'DENIED') {
                $query->where('status','like','DENIED_%');
            } else {
                $query->where('status', $leave_status);
            }
        }

        $leave_app = $query->get();

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
        $count = 1;

        foreach($leave_app as $la) {
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->setCellValue('A' . $rows, $count++);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->setCellValue('B' . $rows, $la->user->name);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->setCellValue('C' . $rows, $la->total_days);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->setCellValue('D' . $rows, $la->leaveType->name);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->setCellValue('E' . $rows, $la->date_from);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->setCellValue('F' . $rows, $la->date_to);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->setCellValue('G' . $rows, $la->date_resume);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->setCellValue('H' . $rows, $la->reason);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            $sheet->setCellValue('I' . $rows, $la->status);
            $sheet->getColumnDimension('J')->setAutoSize(true);
            $sheet->setCellValue('J' . $rows, \Carbon\Carbon::parse($la->created_at)->isoFormat('Y-MM-DD'));
            $rows++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Leave_Applications_All.xlsx"');
        $writer->save("php://output");
    }

    public function export_leave_balance()
    {
        $annual = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->select('users.id as user_id', 'users.*', 'leave_balances.*')
        ->where('leave_balances.leave_type_id', '=', '1' )->get();

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
        $sheet->setCellValue('P1', 'Total Annual Ent');
        $sheet->getStyle('Q')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('Q1', 'Total Annual Earn');
        $sheet->getStyle('R')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('R1', 'Join Date');
        $sheet->getStyle('S')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('S1', 'Total Brought Forw');
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
            if ( $annual ) {
                $sheet->getColumnDimension('P')->setAutoSize(true);
                $sheet->getColumnDimension('Q')->setAutoSize(true);
                $sheet->getColumnDimension('R')->setAutoSize(true);

                $this_emp_type = $annual[$d]->emp_type_id;
                $this_user_id = $annual[$d]->user_id;

                $total_ent = LeaveEntitlement::where('emp_type_id', $this_emp_type)
                ->where('leave_type_id', '1')->first();

                $total_earn = LeaveEarning::where('user_id', $this_user_id)
                ->where('leave_type_id', '1')->first();

                // dd($total_earn);

                $join_date = User::where('id', $this_user_id)->first();

                $sheet->setCellValue('P' . $rows, $total_ent->no_of_days);
                $sheet->setCellValue('Q' . $rows, $total_earn->no_of_days);
                $sheet->setCellValue('R' . $rows, $join_date->join_date);
            }
            if ( $brought_forw ) {
                $sheet->getColumnDimension('S')->setAutoSize(true);
                $sheet->setCellValue('S' . $rows, $brought_forw[$d]->no_of_days);
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
                    $ann_taken_first_half = LeaveApplication::where('user_id',$user->id)->where('status','Approved')->where('leave_type_id',1)->whereBetween('created_at' ,['2021-01-01', '2021-06-30'])->get();
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

    public function sso_login(Request $request){

        // $response = Http::post('https://wspace.io/api/other/validate-token', [
        //     'token' => $token,
        // ]);
            $endpoint = "https://wspace.io/api/other/validate-token";
            $client = new \GuzzleHttp\Client(['http_errors' => false]);
            $token = $request->token;

            $response = $client->request('POST', $endpoint, [
                'form_params' => [
                    'token' => $token
                ]
            ]);

            // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

            $statusCode = $response->getStatusCode();
            $content = $response->getBody();

            // or when your server returns json
            $content = json_decode($response->getBody(), true);
            if(array_key_exists('error', $content)){
                return redirect('/');
            }
            else{
                Auth::loginUsingId($content['data']['id']);
                return redirect('home');
            }
    }
}
