<?php

/**
 * @author Faizul Jasmi
 * @email faizul.jasmi@infomina.com.my
 * @create date 2020-01-07 09:03:50
 * @modify date 2020-01-07 09:03:50
 * @desc [description]
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\NewApplication;
use App\User;
use App\EmpType;
use App\LeaveType;
use App\LeaveApplication;
use App\LeaveEntitlement;
use App\LeaveEarning;
use App\BroughtForwardLeave;
use App\BurntLeave;
use App\LeaveBalance;
use App\TakenLeave;
use App\Holiday;
use App\CalanderRemark;
use Carbon\Carbon;
use DateTime;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show application dashboard
     */

    public function index()
    {

        //Get current user who is logged in
        $user = auth()->user();
        $emptype = $user->emp_types;
        $empTypes = EmpType::orderBy('id', 'ASC')->get();
        $leaveTypes = LeaveType::orderBy('id', 'ASC')->get();
        $leaveEnts = LeaveEntitlement::orderBy('leave_type_id', 'ASC')->where('emp_type_id', '=', $user->emp_type_id)->get();
        //dd($leaveEnts);
        $leaveEarns = LeaveEarning::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        //$leaveApps = $user->leave_applications;
        $broughtFwd = BroughtForwardLeave::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        //dd($user->taken_leaves()->where('leave_type_id',12)->get('no_of_days'));
        $leaveTak = TakenLeave::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

        $ann_taken_first_half = LeaveApplication::where('user_id',$user->id)->where('status','Approved')->where('leave_type_id',1)->whereBetween('created_at' ,['2021-01-01', '2021-06-30'])->get();
        $total_ann_taken_first_half = 0;
        foreach($ann_taken_first_half as $ann){
            $total_ann_taken_first_half += $ann->total_days;
        }

        $leaveApps = LeaveApplication::orderBy('created_at', 'DESC')->where('user_id', '=', $user->id)->paginate(3);
        $pendLeaves = LeaveApplication::where(function ($query) use ($user) {
            $query->where('status', 'PENDING_1')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'PENDING_2')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'PENDING_3')
                ->where('user_id', $user->id);
        })->sortable(['created_at'])->paginate(5, ['*'], 'pending');

        $leaveHist = LeaveApplication::where(function ($query) use ($user) {
            $query->where('status', 'APPROVED')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'CANCELLED')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'DENIED_1')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'DENIED_2')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'DENIED_3')
                ->where('user_id', $user->id);
        })->sortable(['date_from'])->paginate(5, ['*'], 'history');

        //Get all holidays dates\
        $state_hols = $user->state_holidays;
        $natioanl_hols = $user->national_holidays;

        $holidays = $state_hols->merge($natioanl_hols)->sortBy('date_from');
        $holsPaginated = $holidays->groupBy(function ($val) {
            return Carbon::parse($val->date_from)->format('F');
        });

        $all_dates = array();
        foreach ($holidays as $hols) {
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $all_dates[] = $dates;
                $startDate->addDay();
            }
        }


        //Get all leave applications date
        $applied_dates = array();
        $approved_dates = array();
        foreach ($leaveApps as $la) {
            $startDate = new Carbon($la->date_from);
            $endDate = new Carbon($la->date_to);
            if ($la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3') {
                while ($startDate->lte($endDate)) {
                    $dates = str_replace("-", "", $startDate->toDateString());
                    $applied_dates[] = $dates;
                    $startDate->addDay();
                }
            }
            if ($la->status == 'APPROVED') {
                while ($startDate->lte($endDate)) {
                    $dates = str_replace("-", "", $startDate->toDateString());
                    $approved_dates[] = $dates;
                    $startDate->addDay();
                }
            }
        }
        //dd($approved_dates);
        $burntLeave = BurntLeave::where('user_id',$user->id)->where('leave_type_id',1)->first();
        $burntReplacement = BurntLeave::where('user_id',$user->id)->where('leave_type_id',12)->first();
        //dd($burntLeave);
        return view('home')->with(compact('user', 'emptype', 'empTypes', 'leaveTypes', 'leaveApps', 'leaveEnts', 'leaveEarns', 'broughtFwd', 'leaveBal', 'leaveTak', 'all_dates', 'applied_dates', 'approved_dates', 'holidays', 'holsPaginated', 'pendLeaves', 'leaveHist','burntLeave','total_ann_taken_first_half','burntReplacement'));
    }

    /**
     * Show admin dashboard
     */

    public function admin()
    {
        //Get current user who is logged in
        $user = auth()->user();

        $emptype = $user->emp_types;
        $empTypes = EmpType::orderBy('id', 'ASC')->get();
        $leaveTypes = LeaveType::orderBy('id', 'ASC')->get();

        if($user->user_type == 'Admin'){
            //Mantop ni. Only get leave applications that are currently waiting for THIS authority to approve, yang lain tak tarik.
            $leaveApps = LeaveApplication::where(function ($query) use ($user) {
                $query->where('status', 'PENDING_1');
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'PENDING_2');
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'PENDING_3');
            })->sortable(['created_at'])->paginate(5, ['*'], 'pending');
        }
        else{
            //Mantop ni. Only get leave applications that are currently waiting for THIS authority to approve, yang lain tak tarik.
            $leaveApps = LeaveApplication::where(function ($query) use ($user) {
                $query->where('status', 'PENDING_1')
                    ->where('approver_id_1', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'PENDING_2')
                    ->where('approver_id_2', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'PENDING_3')
                    ->where('approver_id_3', $user->id);
            })->sortable(['created_at'])->paginate(5, ['*'], 'pending');
        }


        $allLeaveApps = LeaveApplication::orderBy('date_from', 'ASC')->get();

        $leaveHist = LeaveApplication::where(function ($query) use ($user) {
            $query->where('status', 'APPROVED')
                ->where('approver_id_1', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'CANCELLED')
                ->where('approver_id_1', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'APPROVED')
                ->where('approver_id_2', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'CANCELLED')
                ->where('approver_id_2', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'APPROVED')
                ->where('approver_id_3', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'CANCELLED')
                ->where('approver_id_3', $user->id);
        })->sortable(['date_from' => 'desc'])->paginate(5, ['*'], 'history');

        if($user->user_type == "Management" || $user->user_type == "Admin"){

            $allLeaveHist = LeaveApplication::where(function ($query){
                $query->where('status', 'APPROVED');
            })->orWhere(function ($query) {
                $query->where('status', 'PENDING_1');
            })->orWhere(function ($query) {
                $query->where('status', 'PENDING_2');
            })->orWhere(function ($query) {
                $query->where('status', 'PENDING_3');
            })->get();
        }
        if($user->user_type == "Authority" ){
            $allLeaveHist = LeaveApplication::where(function ($query) use ($user) {
                $query->where('status', 'APPROVED')
                    ->where('approver_id_1', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'APPROVED')
                    ->where('approver_id_2', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'APPROVED')
                    ->where('approver_id_3', $user->id);
            })->get();
        }


        $holidays = Holiday::all();
        $all_dates = array();
        foreach ($holidays as $hols) {

            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $all_dates[] = $dates;
                $startDate->addDay();
            }
        }

        //Get all leave applications date
        $applied_dates = array();
        $approved_dates = array();
        $events = array();
        foreach ($leaveApps as $la) {

            $startDate = new Carbon($la->date_from);
            $endDate = new Carbon($la->date_to);

            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $applied_dates[] = $dates;
                $startDate->addDay();
            }
        }
        foreach ($leaveHist as $lh) {

            $startDate = new Carbon($lh->date_from);
            $endDate = new Carbon($lh->date_to);

            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $approved_dates[] = $dates;
                $startDate->addDay();
            }
        }
        $color = "";
        foreach($allLeaveHist as $lh){
            if($lh->status == "APPROVED"){
                $color = "mediumseagreen";
            }
            else{
                $color = "gold";
            }
            $dateTo = new DateTime($lh->date_to);
            $dateTo->modify('+1 day');
            $eventDetails = array(
                'title' => $lh->user->name."\n".$lh->leaveType->name." (".$lh->apply_for.") "."\n".Carbon::parse($lh->date_from)->isoFormat('D/MM')."-".Carbon::parse($lh->date_to)->isoFormat('D/MM'),
                'start' => $lh->date_from,
                'end' => $dateTo->format('Y-m-d'),
                'color' => $color
            );
            array_push($events , $eventDetails);
        }

        //Get leave applications of same group
        $groupLeaveApps = collect([]);
        foreach ($allLeaveApps as $la) {

            $groupIndex = ["_", "_two_", "_three_", "_four_", "_five_"];
            $isUserLaGroupSameUserGroup = false;
            foreach ($groupIndex as $gI_1) {
                foreach ($groupIndex as $gI_2) {
                    $gLa = $la->user["emp_group" . $gI_1 . "id"];
                    $gUser = $user["emp_group" . $gI_2 . "id"];

                    if ($gUser != "" && $gUser != null && $gLa != "" && $gLa != null) {
                        if ($gLa == $gUser) {
                            $isUserLaGroupSameUserGroup = true;
                            break;
                        }
                    }
                }
            }
            if (
                $isUserLaGroupSameUserGroup && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')
                && ($la->user_id != $user->id)
            ) {
                $groupLeaveApps->add($la);
            }
        }

        //Group user's group applications by months. Starting from the start of the week until end of year.
        $groupLeaveApps = $groupLeaveApps->whereBetween('date_from', array(now()->startOfMonth()->format('Y-m-d'), now()->endOfYear()->format('Y-m-d')))->groupBy(function ($val) {
            return Carbon::parse($val->date_from)->format('F');
        });

        // Remarks on calander
        $allremarks = CalanderRemark::orderBy('id', 'ASC')->get();

        foreach($allremarks as $ar){
            $eventDetails = array(
                'title' => "Remarks by ".$ar->remarker->name." :\n".$ar->remark_text,
                'start' => $ar->remark_date_from,
                'description' => "LOL",
                'end' => $ar->remark_date_to,
                'color' => "lightblue"
            );
            array_push($events , $eventDetails);
        }

        return view('admin')->with(compact('user', 'emptype', 'empTypes', 'leaveTypes', 'leaveApps', 'groupLeaveApps', 'leaveHist', 'all_dates', 'applied_dates', 'approved_dates', 'holidays', 'events','allremarks'));
    }

    public function search(Request $request)
    {
        //Get current user who is logged in
        $user = auth()->user();
        $search = $request->get('search');
        $emptype = $user->emp_types;
        $empTypes = EmpType::orderBy('id', 'ASC')->get();
        $leaveTypes = LeaveType::orderBy('id', 'ASC')->get();
        if($user->user_type == 'Admin'){
            //Mantop ni. Only get leave applications that are currently waiting for THIS authority to approve, yang lain tak tarik.
            $leaveApps = LeaveApplication::where(function ($query) use ($user) {
                $query->where('status', 'PENDING_1');
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'PENDING_2');
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'PENDING_3');
            })->sortable(['created_at'])->paginate(5, ['*'], 'pending');
        }
        else{
            //Mantop ni. Only get leave applications that are currently waiting for THIS authority to approve, yang lain tak tarik.
            $leaveApps = LeaveApplication::where(function ($query) use ($user) {
                $query->where('status', 'PENDING_1')
                    ->where('approver_id_1', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'PENDING_2')
                    ->where('approver_id_2', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'PENDING_3')
                    ->where('approver_id_3', $user->id);
            })->sortable(['created_at'])->paginate(5, ['*'], 'pending');
        }

        $allLeaveApps = LeaveApplication::orderBy('date_from', 'ASC')->get();

        $leaveHist = LeaveApplication::where(function ($query) use ($user, $search) {
            $query->where('status', 'APPROVED')
                ->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->where('approver_id_1', $user->id);
        })->orWhere(function ($query) use ($user, $search) {
            $query->where('status', 'CANCELLED')
                ->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->where('approver_id_1', $user->id);
        })->orWhere(function ($query) use ($user, $search) {
            $query->where('status', 'APPROVED')
                ->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->where('approver_id_2', $user->id);
        })->orWhere(function ($query) use ($user, $search) {
            $query->where('status', 'CANCELLED')
                ->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->where('approver_id_2', $user->id);
        })->orWhere(function ($query) use ($user, $search) {
            $query->where('status', 'APPROVED')
                ->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->where('approver_id_3', $user->id);
        })->orWhere(function ($query) use ($user, $search) {
            $query->where('status', 'CANCELLED')
                ->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->where('approver_id_3', $user->id);
        })->sortable(['updated_at'])->paginate(5, ['*'], 'history');

        if($user->user_type == "Management" || $user->user_type == "Admin"){

            $allLeaveHist = LeaveApplication::where(function ($query){
                $query->where('status', 'APPROVED');
            })->orWhere(function ($query) {
                $query->where('status', 'PENDING_1');
            })->orWhere(function ($query) {
                $query->where('status', 'PENDING_2');
            })->orWhere(function ($query) {
                $query->where('status', 'PENDING_3');
            })->get();
        }
        if($user->user_type == "Authority" ){
            $allLeaveHist = LeaveApplication::where(function ($query) use ($user) {
                $query->where('status', 'APPROVED')
                    ->where('approver_id_1', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'APPROVED')
                    ->where('approver_id_2', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('status', 'APPROVED')
                    ->where('approver_id_3', $user->id);
            })->get();
        }

        $holidays = Holiday::all();
        $all_dates = array();
        foreach ($holidays as $hols) {

            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $all_dates[] = $dates;
                $startDate->addDay();
            }
        }

        //Get all leave applications date
        $applied_dates = array();
        $approved_dates = array();
        $events = array();
        foreach ($leaveApps as $la) {

            $startDate = new Carbon($la->date_from);
            $endDate = new Carbon($la->date_to);

            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $applied_dates[] = $dates;
                $startDate->addDay();
            }
        }
        foreach ($leaveHist as $lh) {

            $startDate = new Carbon($lh->date_from);
            $endDate = new Carbon($lh->date_to);

            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $approved_dates[] = $dates;
                $startDate->addDay();
            }
        }
        $color = "";
        foreach($allLeaveHist as $lh){
            if($lh->status == "APPROVED"){
                $color = "mediumseagreen";
            }
            else{
                $color = "gold";
            }
            $eventDetails = array(
                'title' => $lh->user->name."\n".$lh->leaveType->name." (".$lh->apply_for.") "."\n".Carbon::parse($lh->date_from)->isoFormat('D/MM')."-".Carbon::parse($lh->date_to)->isoFormat('D/MM'),
                'start' => $lh->date_from,
                'description' => "LOL",
                'end' => $lh->date_to,
                'color' => $color
            );
            array_push($events , $eventDetails);
        }

        //Get leave applications of same group
        $groupLeaveApps = collect([]);
        foreach ($allLeaveApps as $la) {

            $groupIndex = ["_", "_two_", "_three_", "_four_", "_five_"];
            $isUserLaGroupSameUserGroup = false;
            foreach ($groupIndex as $gI_1) {
                foreach ($groupIndex as $gI_2) {
                    $gLa = $la->user["emp_group" . $gI_1 . "id"];
                    $gUser = $user["emp_group" . $gI_2 . "id"];

                    if ($gUser != "" && $gUser != null && $gLa != "" && $gLa != null) {
                        if ($gLa == $gUser) {
                            $isUserLaGroupSameUserGroup = true;
                            break;
                        }
                    }
                }
            }
            if (
                $isUserLaGroupSameUserGroup && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')
                && ($la->user_id != $user->id)
            ) {
                $groupLeaveApps->add($la);
            }
            else if($user->user_type == "Management" && ($la->status == 'APPROVED')
            && ($la->user_id != $user->id)){
                $eventDetails = array(
                    'title' => $la->user->name."\n".$la->leaveType->name."\n".Carbon::parse($la->date_from)->isoFormat('D/MM')."-".Carbon::parse($la->date_to)->isoFormat('D/MM'),
                    'start' => $la->date_from,
                    'description' => "LOL",
                    'end' => $la->date_to,
                    'color' => "mediumseagreen"
                );
                array_push($events , $eventDetails);
            }
        }

        //Group user's group applications by months. Starting from the start of the week until end of year.
        $groupLeaveApps = $groupLeaveApps->whereBetween('date_from', array(now()->startOfMonth()->format('Y-m-d'), now()->endOfYear()->format('Y-m-d')))->groupBy(function ($val) {
            return Carbon::parse($val->date_from)->format('F');
        });

        return view('admin')->with(compact('user', 'emptype', 'empTypes', 'leaveTypes', 'leaveApps', 'groupLeaveApps', 'leaveHist', 'all_dates', 'applied_dates', 'approved_dates', 'holidays', 'events'));
    }

    public function store_remarks(Request $request)
    {
        $remark_date_from = $request->get('remark_date_from');
        $remark_date_to = $request->get('remark_date_to');
        $remark_text = $request->get('remark_text');

        if ( $remark_date_from != null && $remark_date_to != null ){
            $remark = new CalanderRemark;
            $remark->remark_date_from = $remark_date_from;
            $remark->remark_date_to = $remark_date_to;
            $remark->remark_text = $remark_text;
            $remark->remark_by = auth()->user()->id;
            $remark->save();
        }

        return back();
    }

    function load_remarks(Request $request)
    {
        $delete_id = $request->input('id');
        $remarks_delete = CalanderRemark::where('id', $delete_id)->delete();

        if($request->ajax())
        {
            $output = '';
            $query = $request->get('query');
            if($query != '') {
                $data = CalanderRemark::orderBy('id', 'DESC')
                    ->join('users', 'users.id', '=', 'calander_remarks.remark_by')
                    ->where('remark_date_from', 'like', '%'.$query.'%')
                    ->orWhere('remark_date_to', 'like', '%'.$query.'%')
                    ->orWhere('remark_text', 'like', '%'.$query.'%')
                    ->orWhere('remark_by', 'like', '%'.$query.'%')
                    ->select('users.name as remark_by_name', 'calander_remarks.*')
                    ->get();
            } else {
                $data = CalanderRemark::orderBy('id', 'DESC')
                ->join('users', 'users.id', '=', 'calander_remarks.remark_by')
                ->select('users.name as remark_by_name', 'calander_remarks.*')
                ->get();
            }
            $total_row = $data->count();
            if($total_row > 0) {
                foreach($data as $row) {
                    $output .= '
                    <tr>
                    <th><input type="checkbox" name="remark_checkbox[]" class="remark_checkbox mx-auto" value="'.$row->id.'"></th>
                    <td>'.$row->id.'</td>
                    <td>'.$row->remark_date_from.'</td>
                    <td>'.$row->remark_date_to.'</td>
                    <td>'.$row->remark_text.'</td>
                    <td>'.$row->remark_by_name.'</td>
                    </tr>
                    ';
                }
            } else {
                $output = '
                <tr>
                    <td align="center" colspan="6">No Data Found</td>
                </tr>
                ';
            }

        return response()->json(['table_data' => $output]);
        }
    }

    public function delete_remarks(Request $request)
    {
        $delete_id = $request->input('id');
        $remarks_delete = CalanderRemark::wherein('id', $delete_id)->delete();

        return $delete_id;
    }


}
