<?php

/**
 * @author Faizul Jasmi
 * @email faizul.jasmi@infomina.com.my
 * @create date 2020-01-07 09:02:58
 * @modify date 2020-01-07 09:02:58
 * @desc [description]
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\Notifications\NewApplication;
use App\Notifications\StatusUpdate;
use App\Notifications\CancelApplication;
use App\LeaveType;
use App\User;
use App\ApprovalAuthority;
use App\LeaveApplication;
use App\LeaveEntitlement;
use App\LeaveEarning;
use App\LeaveBalance;
use App\TakenLeave;
use App\Holiday;
use App\EmpGroup;
use App\History;
use App\ReplacementRelation;
use Carbon\Carbon;

class LeaveApplicationController extends Controller
{
    //Create New Application
    public function create()
    {

        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id', 'ASC')->get()->except('leave_type_id', '=', '12');

        //Get THIS user id
        $user = auth()->user();
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = collect([]);

        $group1 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_id)->first();
        if (isset($group1)) {
            $groupMates1 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_id)->where('status', 'Active')->orWhere('emp_group_two_id', $user->emp_group_id)
            ->orWhere('emp_group_three_id', $user->emp_group_id)->orWhere('emp_group_four_id', $user->emp_group_id)->orWhere('emp_group_five_id', $user->emp_group_id)->get()->except($user->id)->except($group1->group_leader_id);
            $groupMates = $groupMates->merge($groupMates1);
        }

        $group2 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_two_id)->first();
        if (isset($group2)) {
            $groupMates2 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_two_id)->where('status', 'Active')->orWhere('emp_group_two_id', $user->emp_group_two_id)
                ->orWhere('emp_group_three_id', $user->emp_group_two_id)->orWhere('emp_group_four_id', $user->emp_group_two_id)->orWhere('emp_group_five_id', $user->emp_group_two_id)->get()->except($user->id)->except($group2->group_leader_id);
            $groupMates = $groupMates->merge($groupMates2);
        }

        $group3 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_three_id)->first();
        if (isset($group3)) {
            $groupMates3 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_three_id)->where('status', 'Active')->orWhere('emp_group_two_id', $user->emp_group_three_id)
                ->orWhere('emp_group_three_id', $user->emp_group_three_id)->orWhere('emp_group_four_id', $user->emp_group_three_id)->orWhere('emp_group_five_id', $user->emp_group_three_id)->get()->except($user->id)->except($group3->group_leader_id);
            $groupMates = $groupMates->merge($groupMates3);
        }

        $group4 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_four_id)->first();
        if (isset($group4)) {
            $groupMates4 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_four_id)->where('status', 'Active')->orWhere('emp_group_two_id', $user->emp_group_four_id)
                ->orWhere('emp_group_three_id', $user->emp_group_four_id)->orWhere('emp_group_four_id', $user->emp_group_four_id)->orWhere('emp_group_five_id', $user->emp_group_four_id)->get()->except($user->id)->except($group4->group_leader_id);
            $groupMates = $groupMates->merge($groupMates4);
        }

        $group5 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_five_id)->first();
        if (isset($group5)) {
            $groupMates5 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_five_id)->where('status', 'Active')->orWhere('emp_group_two_id', $user->emp_group_five_id)
                ->orWhere('emp_group_three_id', $user->emp_group_five_id)->orWhere('emp_group_four_id', $user->emp_group_five_id)->orWhere('emp_group_five_id', $user->emp_group_five_id)->get()->except($user->id)->except($group5->group_leader_id);
            $groupMates = $groupMates->merge($groupMates5);
        }
        $groupMates = $groupMates->unique()->values()->all();
	//dd($groupMates->unique()->values()->all());

        //Get approval authorities of THIS user
        $leaveAuth = $user->approval_authority;
        if ($leaveAuth == null) {
            return redirect('home')->with('error', 'Your approval authorities have not been set yet by the HR Admin. Please contact the HR Admin.');
        }

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();


        //Get all leave applications
        $leaveApps = LeaveApplication::orderBy('date_from', 'ASC')->get();


        //Get leave applications of same group
        $groupLeaveApps = collect([]);
        foreach ($leaveApps as $la) {
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
            if ($isUserLaGroupSameUserGroup && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')
            && ($la->user_id != $user->id)) {
                $groupLeaveApps->add($la);
            }
        }
        //Get my applications
        $myApps = collect([]);
        foreach ($leaveApps as $la) {
            if ($la->user->id == $user->id && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')) {
                $myApps->add($la);
            }
        }

        //Group user's applications by months. Starting from the start of current month until end of year
        $myApps = $myApps->whereBetween('date_from',array(now()->startOfMonth()->format('Y-m-d'),now()->endOfYear()->format('Y-m-d')))->groupBy(function($val) {
            return Carbon::parse($val->date_from)->format('F');
      });

        //Group user's group applications by months. Starting from the start of the week until end of year.
        $groupLeaveApps = $groupLeaveApps->whereBetween('date_from',array(now()->startOfWeek()->format('Y-m-d'),now()->endOfYear()->format('Y-m-d')))->groupBy(function($val) {
            return Carbon::parse($val->date_from)->format('F');
      });


      $state_hols = $user->state_holidays;
      $natioanl_hols = $user->national_holidays;

      $holidays = $state_hols->merge($natioanl_hols)->sortBy('date_from');
      $holsPaginated = $holidays->groupBy(function ($val) {
          return Carbon::parse($val->date_from)->format('F');
      });

      //dd($holsPaginated);
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
        $myApplication = array();
        foreach ($leaveApps as $la) {
            //Get the user applied and approved application
            if ($la->user->id == $user->id && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')) {
                $stardDate = new Carbon($la->date_from);
                $endDate = new Carbon($la->date_to);

                while ($stardDate->lte($endDate)) {
                    $dates = str_replace("-", "", $stardDate->toDateString());
                    $myApplication[] = $dates;
                    $stardDate->addDay();
                }
            }
            if ($la->user->emp_group_id == $user->emp_group_id) {
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
        }

        $all_rep_claims = LeaveApplication::orderBy('date_from', 'ASC')->where('user_id',$user->id)->where('leave_type_id', 12)->where('remarks','Claim')->where('status','APPROVED')->get();

        return view('leaveapp.create')->with(compact('user', 'leaveType', 'groupMates', 'leaveAuth', 'leaveBal', 'all_dates', 'applied_dates', 'approved_dates', 'myApplication', 'holidays', 'groupLeaveApps', 'holsPaginated', 'myApps', 'all_rep_claims'));
    }


    //Store Application
    public function store(Request $request)
    {

        $request->flash();
        //dd($request->emergency_contact_no);
        //Get user id
        $user = auth()->user();
        //Check Balance
        $leaveBal = LeaveBalance::where(function ($query) use ($request, $user) {
            $query->where('leave_type_id', '=', $request->leave_type_id)
                ->where('user_id', '=', $user->id);
        })->first();

        //If insufficient balance
        if ($leaveBal == null || $request->total_days > $leaveBal->no_of_days && $request->leave_type_id != '12') {
            return redirect()->to('/leave/apply')->with('error', 'Your have insufficient leave balance. Please contact HR for more info.');
        }

        //Check leave authority

        $appCheck = LeaveApplication::where('user_id', $user->id)->get();
        //Get all leave applications date
        $applied_dates = array();
        $approved_dates = array();
        foreach ($appCheck as $la) {
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

        $leaveApp = new LeaveApplication;
        //get user id, leave type id
        $leaveApp->user_id = $user->id;
        $leaveApp->leave_type_id = $request->leave_type_id;
        //status set pending 1
        //get all authorities id

        //If it is replacement leave claim
        if ($request->leave_type_id == '12' && $request->replacement_action == "Claim") {

            //If there is no second approver, move the last approver to the 2nd one
            if ($request->approver_id_2 == null) {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_3;
                $leaveApp->approver_id_3 = null;
            } else {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_2;
                $leaveApp->approver_id_3 = $request->approver_id_3;
            }
                $leaveApp->remarks = "Claim";
        } else {
            $leaveApp->approver_id_1 = $request->approver_id_1;
            $leaveApp->approver_id_2 = $request->approver_id_2;
            $leaveApp->approver_id_3 = $request->approver_id_3;
            if($request->leave_type_id == "12"){
                $leaveApp->remarks = "Apply";
            }
        }



        //get date from
        $leaveApp->date_from = $request->date_from;
        //get date to
        $leaveApp->date_to = $request->date_to;
        //get date resume
        $leaveApp->date_resume = $request->date_resume;
        //get total days
        $leaveApp->total_days = $request->total_days;
        //get apply for
        $leaveApp->apply_for = $request->apply_for;
        //get reason
        $leaveApp->reason = $request->reason;
        //get relief personel id
        $leaveApp->relief_personnel_id = $request->relief_personnel_id;
        //get emergency contact
        $leaveApp->emergency_contact_name = $request->emergency_contact_name;
        $leaveApp->emergency_contact_no = $request->emergency_contact_no;


        //Attachment validation
        $validator = Validator::make(
            $request->all(),
            ['attachment' => 'required_if:leave_type_id,3|required_if:leave_type_id,7|required_if:leave_type_id,4|required_if:leave_type_id,8|required_if:leave_type_id,9|mimes:jpeg,png,jpg,pdf|max:2048']
        );

        // if validation fails
        if ($validator->fails()) {
            return redirect()->to('/leave/apply')->with('error', 'Your file attachment is invalid. Application is not submitted');
        }
        //If validation passes and has a file. Not necessary to check but just to be safe
        if ($request->hasFile('attachment')) {
            $att = $request->file('attachment');
            $uploaded_file = $att->store('public');
            //Pecahkan
            $paths = explode('/', $uploaded_file);
            $filename = $paths[1];
            //dd($uploaded_file);
            //Save attachment filenam into leave application table
            $leaveApp->attachment = $filename;
        }


        $leaveApp->save();
        if($leaveApp->leave_type_id == "12" &&  $leaveApp->remarks == "Apply"){

            $claim_apply = ReplacementRelation::where('claim_id',$request->claim_id)->get();
            if(!$claim_apply->isEmpty()){
                $total_days = 0;
                foreach($claim_apply as $ca){
                    $rep_apply = LeaveApplication::where('id',$ca->leave_id)->first();
                    if($rep_apply->status == 'PENDING_1' || $rep_apply->status == 'PENDING_2' || $rep_apply->status == 'PENDING_3'||$rep_apply->status == 'APPROVED'){
                        $total_days += $rep_apply->total_days;
                    }
                    $td = $total_days + $leaveApp->total_days;
                    if($td > $ca->claim_total_days){
                        $leaveApp->delete();
                        return redirect()->to('/leave/apply')->with('error', 'You have fully used the chosen replacement claim. Choose another claim.');
                    }
                }
            }
                $claim_apply = new ReplacementRelation;
                //Set Claim ID
                $claim_apply->claim_id = $request->claim_id;
                //Get Claim application total days and set
                $claimApp = LeaveApplication::where('id',$request->claim_id)->first();
                $claim_apply->claim_total_days =  $claimApp->total_days;
                //Set Leave ID
                $claim_apply->leave_id = $leaveApp->id;
                //Set Leave Total days
                $claim_apply->leave_total_days =  $leaveApp->total_days;
                $claim_apply->save();
        }
        //Send email notification
        //Notification::route('mail', $leaveApp->approver_one->email)->notify(new NewApplication($leaveApp));

        $leaveApp->approver_one->notify(new NewApplication($leaveApp));
        //$this->mobile_notification($leaveApp,"authority_1");

        //STORE
        return redirect()->to('/home')->with('message', 'Leave application submitted succesfully');
    }

    public function edit(LeaveApplication $leaveApplication)
    {
        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id', 'ASC')->get();

        //Get THIS user id
        $user = $leaveApplication->user;
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = collect([]);

        $group1 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_id)->first();
        if (isset($group1)) {
            $groupMates1 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_id)->orWhere('emp_group_two_id', $user->emp_group_id)
            ->orWhere('emp_group_three_id', $user->emp_group_id)->orWhere('emp_group_four_id', $user->emp_group_id)->orWhere('emp_group_five_id', $user->emp_group_id)->get()->except($user->id)->except($group1->group_leader_id);
            $groupMates = $groupMates->merge($groupMates1);
        }

        $group2 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_two_id)->first();
        if (isset($group2)) {
            $groupMates2 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_two_id)->orWhere('emp_group_two_id', $user->emp_group_two_id)
                ->orWhere('emp_group_three_id', $user->emp_group_two_id)->orWhere('emp_group_four_id', $user->emp_group_two_id)->orWhere('emp_group_five_id', $user->emp_group_two_id)->get()->except($user->id)->except($group2->group_leader_id);
            $groupMates = $groupMates->merge($groupMates2);
        }

        $group3 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_three_id)->first();
        if (isset($group3)) {
            $groupMates3 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_three_id)->orWhere('emp_group_two_id', $user->emp_group_three_id)
                ->orWhere('emp_group_three_id', $user->emp_group_three_id)->orWhere('emp_group_four_id', $user->emp_group_three_id)->orWhere('emp_group_five_id', $user->emp_group_three_id)->get()->except($user->id)->except($group3->group_leader_id);
            $groupMates = $groupMates->merge($groupMates3);
        }

        $group4 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_four_id)->first();
        if (isset($group4)) {
            $groupMates4 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_four_id)->orWhere('emp_group_two_id', $user->emp_group_four_id)
                ->orWhere('emp_group_three_id', $user->emp_group_four_id)->orWhere('emp_group_four_id', $user->emp_group_four_id)->orWhere('emp_group_five_id', $user->emp_group_four_id)->get()->except($user->id)->except($group4->group_leader_id);
            $groupMates = $groupMates->merge($groupMates4);
        }

        $group5 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_five_id)->first();
        if (isset($group5)) {
            $groupMates5 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_five_id)->orWhere('emp_group_two_id', $user->emp_group_five_id)
                ->orWhere('emp_group_three_id', $user->emp_group_five_id)->orWhere('emp_group_four_id', $user->emp_group_five_id)->orWhere('emp_group_five_id', $user->emp_group_five_id)->get()->except($user->id)->except($group5->group_leader_id);
            $groupMates = $groupMates->merge($groupMates5);
        }
        $groupMates = $groupMates->unique()->values()->all();

        //Get approval authorities of THIS user
        $leaveAuth = $user->approval_authority;
        //Get all authorities
        $userAuth = User::orderBy('id', 'ASC')->where('id', '!=', '1')->where('user_type', 'Authority')->get()->except($user->id);
        //Get approval authorities for this user
        //Change id to CYNTHIA'S ID
        $leaveAuthReplacement = User::orderBy('id', 'ASC')->where('id', '!=', '4')->get()->except($user->id);


        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

        //Get leave applications from same group
        $leaveApps = LeaveApplication::orderBy('date_from', 'ASC')->get()->except($leaveApplication->id);

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
        $myApplication = array();
        foreach ($leaveApps as $la) {
             //Get the user applied and approved application
             if ($la->user->id == $user->id && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')) {
                $stardDate = new Carbon($la->date_from);
                $endDate = new Carbon($la->date_to);

                while ($stardDate->lte($endDate)) {
                    $dates = str_replace("-", "", $stardDate->toDateString());
                    $myApplication[] = $dates;
                    $stardDate->addDay();
                }
            }
            if ($la->user->emp_group_id == $user->emp_group_id) {
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
        }

        //dd($leaveApplication->approver_id_1);
        return view('leaveapp.edit')->with(compact('leaveApplication', 'user', 'leaveType', 'groupMates', 'userAuth', 'leaveAuth', 'leaveBal', 'all_dates', 'applied_dates', 'approved_dates', 'leaveAuthReplacement','myApplication'));
    }

    public function update(Request $request, LeaveApplication $leaveApplication)
    {
        //dd($request->emergency_contact_no);
        //Get user id
        $user = $leaveApplication->user;
        //Check Balance
        $leaveBal = LeaveBalance::where(function ($query) use ($request, $user) {
            $query->where('leave_type_id', '=', $request->leave_type_id)
                ->where('user_id', '=', $user->id);
        })->first();

        $leaveTaken = TakenLeave::where(function ($query) use ($request, $user) {
            $query->where('leave_type_id', '=', $request->leave_type_id)
                ->where('user_id', '=', $user->id);
        })->first();
        //dd($leaveBal->no_of_days);
        if ($request->total_days > $leaveBal->no_of_days && $request->leave_type_id != '12') {
            return redirect()->to('/leave/apply')->with('error', 'Your have insufficient leave balance. Please contact HR for more info.');
        }

        $leaveApp = $leaveApplication;
        //get user id, leave type id
        $leaveApp->user_id = $user->id;
        $leaveApp->leave_type_id = $request->leave_type_id;
        //status set pending 1
        //get all authorities id
        //If it is replacement leave claim
           //If it is replacement leave claim
           if ($request->leave_type_id == '12' && $request->replacement_action == "Claim") {

            //If there is no second approver, move the last approver to the 2nd one
            if ($request->approver_id_2 == null) {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_3;
                $leaveApp->approver_id_3 = null;
            } else {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_2;
                $leaveApp->approver_id_3 = $request->approver_id_3;
            }
                $leaveApp->remarks = "Claim";
        } else {
            $leaveApp->approver_id_1 = $request->approver_id_1;
            $leaveApp->approver_id_2 = $request->approver_id_2;
            $leaveApp->approver_id_3 = $request->approver_id_3;
            if($request->leave_type_id == "12"){
                $leaveApp->remarks = "Apply";
            }
        }


        //get date from
        $leaveApp->date_from = $request->date_from;
        //get date to
        $leaveApp->date_to = $request->date_to;
        //get date resume
        $leaveApp->date_resume = $request->date_resume;

        //get initial total days
        $initial_days = $leaveApp->total_days;
        //If the new set date is more than initial date
        if($initial_days < $request->total_days && $leaveApp->status == "APPROVED"){
            $day_dif = $request->total_days - $initial_days;
            //Minus the day diff to the balance
            $leaveBal->no_of_days -= $day_dif;
            $leaveBal->save();

            //Add the day diff to the taken leave
            $leaveTaken->no_of_days += $day_dif;
            $leaveTaken->save();
        }
        //Else if
        else if($initial_days > $request->total_days && $leaveApp->status == "APPROVED"){
            $day_dif = $initial_days - $request->total_days;

            //Add the day diff to the balance
            $leaveBal->no_of_days += $day_dif;
            $leaveBal->save();

            //Minus the day diff to taken leave
            $leaveTaken->no_of_days -= $day_dif;
            $leaveTaken->save();
        }
        $leaveApp->total_days = $request->total_days;
        //get reason
        $leaveApp->reason = $request->reason;
        //get relief personel id
        $leaveApp->relief_personnel_id = $request->relief_personnel_id;
        //get emergency contact
        $leaveApp->emergency_contact_name = $request->emergency_contact_name;
        $leaveApp->emergency_contact_no = $request->emergency_contact_no;

        //Attachment validation
        $validator = Validator::make(
            $request->all(),
            ['attachment' => 'required_if:leave_type_id,3|required_if:leave_type_id,7|mimes:jpeg,png,jpg,pdf|max:2048']
        );

        // if validation fails
        if ($validator->fails()) {
            return redirect()->to('/leave/apply')->with('error', 'Your file attachment format is invalid. Application is not submitted');
        }
        //If validation passes and has a file. Not necessary to check but just to be safe
        // if ($request->hasFile('attachment')) {
        //     $att = $request->file('attachment');
        //     $uploaded_file = $att->store('public');
        //     //Pecahkan
        //     $paths = explode('/', $uploaded_file);
        //     $filename = $paths[1];
        //     //dd($uploaded_file);
        //     //Save attachment filenam into leave application table
        //     $leaveApp->attachment = $filename;
        // }

        //Upload image
        if ($request->hasFile('attachment')) {
            $att = $request->file('attachment');
            $uploaded_file = $att->store('public');
            //Pecahkan
            $paths = explode('/', $uploaded_file);
            $filename = $paths[1];
            //dd($uploaded_file);
            //Save filename into Database
            $leaveApp->update(['attachment' => $filename]);
        }


        $leaveApp->save();
        //Send email notification
        //Notification::route('mail', $leaveApp->approver_one->email)->notify(new NewApplication($leaveApp));

        if($leaveApp->status == 'PENDING_1'){
            $leaveApp->approver_one->notify(new NewApplication($leaveApp));
        }

         //Record in activity history
         $hist = new History;
         $hist->leave_application_id = $leaveApplication->id;
         $hist->user_id = auth()->user()->id;
         $hist->action = "Edited";
         $hist->save();

        if(auth()->user()->user_type == "Admin" || auth()->user()->user_type == "Management"){
            return redirect()->to('/admin')->with('message', 'Leave application edited succesfully');
        }
        else{
            return redirect()->to('/home')->with('message', 'Leave application edited succesfully');
        }
    }

    public function approve(LeaveApplication $leaveApplication)
    {

        //Get current user id
        $user = auth()->user();
        //Get leave application authorities ID
        $la_1 = $leaveApplication->approver_id_1;
        $la_2 = $leaveApplication->approver_id_2;
        $la_3 = $leaveApplication->approver_id_3;

        $old_status = $leaveApplication->status;

        //If user id same as approver id 1
        if ($la_1 == $user->id) {
            //if no authority 2, terus change to approved
            if ($la_2 == null) {
                $leaveApplication->status = 'APPROVED';
            }
            //else update status to pending 2,
            else {
                $leaveApplication->status = 'PENDING_2';

                //Notify the second approver
                $leaveApplication->approver_two->notify(new NewApplication($leaveApplication));
            }
        }
        //if user id same as approved id 2
        else if ($la_2 == $user->id) {
            //if no authority 3, terus change to approved
            if ($la_3 == null) {
                $leaveApplication->status = 'APPROVED';
            }
            //else update status to pending 3
            else {
                $leaveApplication->status = 'PENDING_3';
                //Notify the third approver
                $leaveApplication->approver_three->notify(new NewApplication($leaveApplication));
            }
        }
        //If user id same as approved id 3, update status to approved
        else {
            $leaveApplication->status = 'APPROVED';
        }
        $leaveApplication->update();

        //If the application is approved
        if ($leaveApplication->status == 'APPROVED') {

            //If the approved leave is a Replacement leave, assign earned to Replacement, and add day balance to Annual
            if ($leaveApplication->leaveType->name == 'Replacement') {
                if($leaveApplication->remarks == "Claim"){
                    $lt = LeaveEarning::where(function ($query) use ($leaveApplication) {
                        $query->where('leave_type_id', $leaveApplication->leave_type_id)
                            ->where('user_id', $leaveApplication->user_id);
                    })->first();

                    $lt->no_of_days += $leaveApplication->total_days;

                    $lt->save();

                    //Add balance to replacement leave balance;
                    $lb = LeaveBalance::where(function ($query) use ($leaveApplication) {
                        $query->where('leave_type_id', $leaveApplication->leave_type_id)
                            ->where('user_id', $leaveApplication->user_id);
                    })->first();

                    $lb->no_of_days += $leaveApplication->total_days;
                    $lb->save();

                    //Record in activity history
                    $hist = new History;
                    $hist->leave_application_id = $leaveApplication->id;
                    $hist->user_id = $user->id;
                    $hist->action = " Claim Approved";
                    $hist->save();

                    // $leaveApplication->remarks = "Claim";
                    // $leaveApplication->save();

                    //Send status update email
                    $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                    return redirect()->to('/admin')->with('message', 'Replacement leave claim application status updated succesfully');
                }
                else if($leaveApplication->remarks == "Apply"){
                    //Get the claim application related to this use replacement application
                    $this_claim_apply = ReplacementRelation::where('leave_id',$leaveApplication->id)->first();
                    $claimApp = LeaveApplication::where('id', $this_claim_apply->claim_id)->first();
                    //Get related claim records
                    $all_claim_apply = ReplacementRelation::where('claim_id',$this_claim_apply->claim_id)->get();
                    $total_days = 0;
                    foreach($all_claim_apply as $aca){
                        $leaveApp = LeaveApplication::where('id',$aca->leave_id)->first();
                        if($leaveApp->status == 'PENDING_1'|| $leaveApp->status == 'PENDING_2'||$leaveApp->status == 'PENDING_3'||$leaveApp->status == 'APPROVED'){
                            $total_days += $leaveApp->total_days;
                        }
                    }
                    //If the total days is fully used including this application, set the claim application status to TAKEN,
                    if($total_days == $claimApp->total_days){
                        $claimApp->status = "TAKEN";
                        $claimApp->save();
                    }
                    elseif($total_days > $claimApp->total_days){
                        $leaveApplication->status = $old_status;
                        $leaveApplication->save();
                        return redirect()->to('/admin')->with('error', 'Employee does not have enough replacement leave balance. The leave has been cancelled.');
                    }
                    //If not just leave the status as it is

                    //Minus the user replacement leave balance based on the total days for this application
                    $ReplacementBal = LeaveBalance::where('leave_type_id','12')->where('user_id',$leaveApplication->user_id)->first();
                    $ReplacementBal->no_of_days -= $leaveApplication->total_days;
                    $ReplacementBal->save();

                    $ReplacementTaken = TakenLeave::where('leave_type_id','12')->where('user_id',$leaveApplication->user_id)->first();
                    $ReplacementTaken->no_of_days += $leaveApplication->total_days;
                    $ReplacementTaken->save();
                }
                $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                return redirect()->to('/admin')->with('message', 'Replacement leave application status updated succesfully');
            }

            //If the approved leave is a Sick leave, deduct the amount taken in both sick leave and hospitalization balance
            if ($leaveApplication->leaveType->name == 'Sick') {
                //Add in amount sick leave taken
                $lt = TakenLeave::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', $leaveApplication->leave_type_id)
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $lt->no_of_days += $leaveApplication->total_days;
                $lt->save();

                //Deduct balance in sick leave balance
                $sickBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', '3')
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $sickBalance->no_of_days -= $leaveApplication->total_days;
                $sickBalance->save();

                //Deduct balance in hosp leave balance
                $hospBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', '4')
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $hospBalance->no_of_days -= $leaveApplication->total_days;
                $hospBalance->save();

                //Record in activity history
                $hist = new History;
                $hist->leave_application_id = $leaveApplication->id;
                $hist->user_id = $user->id;
                $hist->action = "Approved";
                $hist->save();

                //Send status update email
                $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                return redirect()->to('/admin')->with('message', 'Sick leave application status updated succesfully');
            }

            //If the approved leave is an emergency leave, deduct the taken amount to Annual Leave
            if ($leaveApplication->leaveType->name == 'Emergency') {
                //Add in amount emergency leave taken
                $lt = TakenLeave::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', $leaveApplication->leave_type_id)
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $lt->no_of_days += $leaveApplication->total_days;
                $lt->save();

                //Deduct balance in emergency leave balance
                $emBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', '6')
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $emBalance->no_of_days -= $leaveApplication->total_days;
                $emBalance->save();

                //Deduct balance in annual leave
                $annBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', '1')
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                if($leaveApplication->total_days >  $annBalance->no_of_days){
                    $leaveApplication->status = $old_status;
                    $leaveApplication->update();
                    return redirect()->to('/admin')->with('error', 'Employee does not have enough leave balance');
                }
                $annBalance->no_of_days -= $leaveApplication->total_days;
                $annBalance->save();
                //dd($annBalance->no_of_days);

                //Record in activity history
                $hist = new History;
                $hist->leave_application_id = $leaveApplication->id;
                $hist->user_id = $user->id;
                $hist->action = "Approved";
                $hist->save();

                //Send status update email
                $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                return redirect()->to('/admin')->with('message', 'Emergency leave application status updated succesfully');
            }

            //Update leave taken table
            //Check for existing record
            $dupcheck = TakenLeave::where(function ($query) use ($leaveApplication) {
                $query->where('leave_type_id', $leaveApplication->leave_type_id)
                    ->where('user_id', $leaveApplication->user_id);
            })->first();

            //If does not exist, create new
            if ($dupcheck == null) {
                $tl = new TakenLeave;
                $tl->leave_type_id = $leaveApplication->leave_type_id;
                $tl->user_id = $leaveApplication->user_id;
                $tl->no_of_days = $leaveApplication->total_days;
                $tl->save();
            }
            //else update existing
            else {
                $dupcheck->no_of_days += $leaveApplication->total_days;
                $dupcheck->save();
            }

            //Update leave balance table
            //Check for existing record
            $dupcheck2 = LeaveBalance::where(function ($query) use ($leaveApplication) {
                $query->where('leave_type_id', $leaveApplication->leave_type_id)
                    ->where('user_id', $leaveApplication->user_id);
            })->first();

            //If does not exist, create new
            if ($dupcheck2 == null) {
                $lb = new LeaveBalance;
                $lb->leave_type_id = $leaveApplication->leave_type_id;
                $lb->user_id = $leaveApplication->user_id;
                $le = LeaveEarning::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', $leaveApplication->leave_type_id)
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $lb->no_of_days = $le->no_of_days - $leaveApplication->total_days;
                $lb->save();
            }
            //else update existing
            else {
                if($leaveApplication->total_days > $dupcheck2->no_of_days){
                    return redirect()->to('/admin')->with('error', 'Employee does not have enough leave balance');
                }
                else{
                    $dupcheck2->no_of_days -= $leaveApplication->total_days;
                    $dupcheck2->save();
                }
            }
        }

        //Record in activity history
        $hist = new History;
        $hist->leave_application_id = $leaveApplication->id;
        $hist->user_id = $user->id;
        $hist->action = "Approved";
        $hist->save();



        //Send status update email
        $leaveApplication->user->notify(new StatusUpdate($leaveApplication));

        return redirect()->to('/admin')->with('message', 'Leave application status updated succesfully');
    }

    public function deny(LeaveApplication $leaveApplication)
    {

        //Get current user id
        $user = auth()->user();
        //Get leave application authorities ID
        $la_1 = $leaveApplication->approver_id_1;
        $la_2 = $leaveApplication->approver_id_2;
        $la_3 = $leaveApplication->approver_id_3;

        //If user id same as approver id 1
        if ($la_1 == $user->id) {
            $leaveApplication->status = 'DENIED_1';
        }
        //if user id same as approved id 2
        else if ($la_2 == $user->id) {
            $leaveApplication->status = 'DENIED_2';
        }
        //If user id same as approved id 3,
        else {
            $leaveApplication->status = 'DENIED_3';
        }
        $leaveApplication->update();

         //Record in activity history
         $hist = new History;
         $hist->leave_application_id = $leaveApplication->id;
         $hist->user_id = $user->id;
         $hist->action = "Denied";
         $hist->save();

        //Send status update email
        $leaveApplication->user->notify(new StatusUpdate($leaveApplication));


        return redirect()->to('/admin')->with('message', 'Leave application status updated succesfully');
    }

    public function view(LeaveApplication $leaveApplication)
    {
        $leaveApp = $leaveApplication;
        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id', 'ASC')->get();
        //Get THIS user id
        $user = $leaveApp->user;
        $leaveAuth = $user->approval_authority;
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = User::orderBy('id', 'ASC')->get()->except($user->id);

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

        $applied_dates = array();
        $approved_dates = array();
        $startDate = new Carbon($leaveApp->date_from);
        $endDate = new Carbon($leaveApp->date_to);

        if ($leaveApp->status == 'APPROVED') {
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $approved_dates[] = $dates;
                $startDate->addDay();
            }
        } else {
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $applied_dates[] = $dates;
                $startDate->addDay();
            }
        }

        $holidays = Holiday::all();
        $hol_dates = array();
        foreach ($holidays as $hols) {
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $hol_dates[] = $dates;
                $startDate->addDay();
            }
        }

        return view('leaveapp.view')->with(compact('leaveApp', 'leaveType', 'user', 'leaveAuth', 'groupMates', 'leaveBal', 'applied_dates', 'approved_dates', 'hol_dates'));
    }

    public function cancel(LeaveApplication $leaveApplication, Request $request)
    {
        //If leave has been approved
        if($leaveApplication->status == "APPROVED"){
            //Get total days of leave application
            $days = $leaveApplication->total_days;
            //Get number of taken leave for the leave type
            $takenLeave = TakenLeave::where('user_id', $leaveApplication->user_id)->where('leave_type_id', $leaveApplication->leave_type_id)->first();
            //Get number of leave balance for the leave type
            $leaveBalance = LeaveBalance::where('user_id', $leaveApplication->user_id)->where('leave_type_id', $leaveApplication->leave_type_id)->first();

            if($leaveApplication->leave_type_id != "12"){
                //Subtract total days from taken leave
                $takenLeave->no_of_days -= $days;
                //Add total days into leave balance
                $leaveBalance->no_of_days += $days;
                $takenLeave->save();
                $leaveBalance->save();
            }

            //If leave application is Sick leave
            if($leaveApplication->leave_type_id == "3"){
                //Get leave balance for hospitalization
                $leaveBal2 = LeaveBalance::where('user_id', $leaveApplication->user_id)->where('leave_type_id', 4)->first();
                //Add the total days cancelle back to hospitalization balance
                $leaveBal2->no_of_days += $days;
                //Save
                $leaveBal2->save();
            }
            //If leave application is replacement leave
            if($leaveApplication->leave_type_id == "12"){
                //Get leave earning for replacement leave
                $leaveEarn = LeaveEarning::where('user_id', $leaveApplication->user_id)->where('leave_type_id', 12)->first();
                //Get annual leave balance
                $balanceLeave = LeaveBalance::where('user_id', $leaveApplication->user_id)->where('leave_type_id', 1)->first();
                //Subtract replacement leave earning
                $leaveEarn->no_of_days -= $days;
                //Subtract annual leave balance that has been added before
                $balanceLeave->no_of_days -= $days;
                //Save
                $leaveEarn->save();
                $balanceLeave->save();
            }
            //If leave application is emergency
            if($leaveApplication->leave_type_id == "6"){
                //Get leave annual leave balance
                $leaveBal2 = LeaveBalance::where('user_id', $leaveApplication->user_id)->where('leave_type_id', 1)->first();
                //Add the total days back into annual leave balance
                $leaveBal2->no_of_days += $days;
                //Save
                $leaveBal2->save();
            }
        }
        $leaveApplication->remarks = $request->remarks;
        $leaveApplication->remarker_id = auth()->user()->id;
        $prevStatus = $leaveApplication->status;
        $leaveApplication->status = "CANCELLED";
        $leaveApplication->save();

        if(($leaveApplication->remarker_id == $leaveApplication->approver_id_3)){
            if(($prevStatus == 'PENDING_2')||($prevStatus == 'PENDING_3') || ($prevStatus == 'APPROVED')){
                $leaveApplication->approver_two->notify(new CancelApplication($leaveApplication));
            }
        }



        $leaveApplication->approver_one->notify(new CancelApplication($leaveApplication));
        $when = now()->addMinutes(5);
        $leaveApplication->user->notify((new CancelApplication($leaveApplication))->delay($when));

        $user = auth()->user();
        if($user->user_type == "Employee"){
        return redirect()->to('/home')->with('message', 'Leave application cancelled succesfully');
        }
        else{
         return redirect()->to('/admin')->with('message', 'Leave application cancelled succesfully');
        }
    }

    public function applyFor(User $user){
        $user = $user;

        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id', 'ASC')->get()->except('leave_type_id', '=', '12');

        //Get employees who are in the same group (for relieve personnel).
        $groupMates = collect([]);

        $group1 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_id)->first();
        if (isset($group1)) {
            $groupMates1 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_id)->orWhere('emp_group_two_id', $user->emp_group_id)
            ->orWhere('emp_group_three_id', $user->emp_group_id)->orWhere('emp_group_four_id', $user->emp_group_id)->orWhere('emp_group_five_id', $user->emp_group_id)->get()->except($user->id)->except($group1->group_leader_id);
            $groupMates = $groupMates->merge($groupMates1);
        }

        $group2 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_two_id)->first();
        if (isset($group2)) {
            $groupMates2 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_two_id)->orWhere('emp_group_two_id', $user->emp_group_two_id)
                ->orWhere('emp_group_three_id', $user->emp_group_two_id)->orWhere('emp_group_four_id', $user->emp_group_two_id)->orWhere('emp_group_five_id', $user->emp_group_two_id)->get()->except($user->id)->except($group2->group_leader_id);
            $groupMates = $groupMates->merge($groupMates2);
        }

        $group3 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_three_id)->first();
        if (isset($group3)) {
            $groupMates3 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_three_id)->orWhere('emp_group_two_id', $user->emp_group_three_id)
                ->orWhere('emp_group_three_id', $user->emp_group_three_id)->orWhere('emp_group_four_id', $user->emp_group_three_id)->orWhere('emp_group_five_id', $user->emp_group_three_id)->get()->except($user->id)->except($group3->group_leader_id);
            $groupMates = $groupMates->merge($groupMates3);
        }

        $group4 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_four_id)->first();
        if (isset($group4)) {
            $groupMates4 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_four_id)->orWhere('emp_group_two_id', $user->emp_group_four_id)
                ->orWhere('emp_group_three_id', $user->emp_group_four_id)->orWhere('emp_group_four_id', $user->emp_group_four_id)->orWhere('emp_group_five_id', $user->emp_group_four_id)->get()->except($user->id)->except($group4->group_leader_id);
            $groupMates = $groupMates->merge($groupMates4);
        }

        $group5 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_five_id)->first();
        if (isset($group5)) {
            $groupMates5 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_five_id)->orWhere('emp_group_two_id', $user->emp_group_five_id)
                ->orWhere('emp_group_three_id', $user->emp_group_five_id)->orWhere('emp_group_four_id', $user->emp_group_five_id)->orWhere('emp_group_five_id', $user->emp_group_five_id)->get()->except($user->id)->except($group5->group_leader_id);
            $groupMates = $groupMates->merge($groupMates5);
        }
        $groupMates = $groupMates->unique()->values()->all();
        //dd($groupMates->unique()->values()->all());

        //Get approval authorities of THIS user
        $leaveAuth = $user->approval_authority;
        if ($leaveAuth == null) {
            return redirect('home')->with('error', 'Your approval authorities have not been set yet by the HR Admin. Please contact the HR Admin.');
        }

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

        //Get all leave applications
        $leaveApps = LeaveApplication::orderBy('date_from', 'ASC')->where('user_id',$user->id)->get();

        //Get my applications
        $myApps = collect([]);
        foreach ($leaveApps as $la) {
            if ($la->user->id == $user->id && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')) {
                $myApps->add($la);
            }
        }

        //Group user's applications by months. Starting from the start of current month until end of year
        $myApps = $myApps->whereBetween('date_from',array(now()->startOfMonth()->format('Y-m-d'),now()->endOfYear()->format('Y-m-d')))->groupBy(function($val) {
            return Carbon::parse($val->date_from)->format('F');
      });

      //Get all leave applications date
      $applied_dates = array();
      $approved_dates = array();
      $myApplication = array();
      foreach ($leaveApps as $la) {
          //Get the user applied and approved application
          if ($la->status == 'APPROVED') {
              $stardDate = new Carbon($la->date_from);
              $endDate = new Carbon($la->date_to);

              while ($stardDate->lte($endDate)) {
                  $dates = str_replace("-", "", $stardDate->toDateString());
                  $myApplication[] = $dates;
                  $approved_dates[] = $dates;
                  $stardDate->addDay();
              }
          }
          if($la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3'){
            $stardDate = new Carbon($la->date_from);
            $endDate = new Carbon($la->date_to);

            while ($stardDate->lte($endDate)) {
                $dates = str_replace("-", "", $stardDate->toDateString());
                $myApplication[] = $dates;
                $applied_dates = $dates;
                $stardDate->addDay();
            }
          }
      }

        return view('leaveapp.applyfor')->with(compact('user','leaveType','groupMates','leaveAuth','leaveBal','myApps','myApplication','applied_dates','approved_dates'));
    }

    public function submitApplyFor(Request $request, User $user){
        $request->flash();
        //dd($request->emergency_contact_no);
        //Get user id
        $user = $user;
        //Check Balance
        $leaveBal = LeaveBalance::where(function ($query) use ($request, $user) {
            $query->where('leave_type_id', '=', $request->leave_type_id)
                ->where('user_id', '=', $user->id);
        })->first();

        //If insufficient balance
        if ($leaveBal == null || $request->total_days > $leaveBal->no_of_days && $request->leave_type_id != '12') {
            return back()->with('error', 'Employee have insufficient leave balance. Please contact HR for more info.');
        }

        //Check leave authority

        $leaveApp = new LeaveApplication;
        //get user id, leave type id
        $leaveApp->user_id = $user->id;
        $leaveApp->leave_type_id = $request->leave_type_id;
        //status set pending 1
        //get all authorities id

        //If it is replacement leave claim
        if ($request->leave_type_id == '12') {

            //If there is no second approver, move the last approver to the 2nd one
            if ($request->approver_id_2 == null) {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_3;
                $leaveApp->approver_id_3 = null;
            } else {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_2;
                $leaveApp->approver_id_3 = $request->approver_id_3;
            }
        } else {
            $leaveApp->approver_id_1 = $request->approver_id_1;
            $leaveApp->approver_id_2 = $request->approver_id_2;
            $leaveApp->approver_id_3 = $request->approver_id_3;
        }



        //get date from
        $leaveApp->date_from = $request->date_from;
        //get date to
        $leaveApp->date_to = $request->date_to;
        //get date resume
        $leaveApp->date_resume = $request->date_resume;
        //get total days
        $leaveApp->total_days = $request->total_days;
        //get apply for
        $leaveApp->apply_for = $request->apply_for;
        //get reason
        $leaveApp->reason = $request->reason;
        //get relief personel id
        $leaveApp->relief_personnel_id = $request->relief_personnel_id;
        //get emergency contact
        $leaveApp->emergency_contact_name = $request->emergency_contact_name;
        $leaveApp->emergency_contact_no = $request->emergency_contact_no;


        //Attachment validation
        $validator = Validator::make(
            $request->all(),
            ['attachment' => 'required_if:leave_type_id,3|required_if:leave_type_id,7|required_if:leave_type_id,4|required_if:leave_type_id,8|required_if:leave_type_id,9|mimes:jpeg,png,jpg,pdf|max:2048']
        );

        // if validation fails
        if ($validator->fails()) {
            return back()->with('error', 'Your file attachment is invalid. Application is not submitted');
        }
        //If validation passes and has a file. Not necessary to check but just to be safe
        if ($request->hasFile('attachment')) {
            $att = $request->file('attachment');
            $uploaded_file = $att->store('public');
            //Pecahkan
            $paths = explode('/', $uploaded_file);
            $filename = $paths[1];
            //dd($uploaded_file);
            //Save attachment filenam into leave application table
            $leaveApp->attachment = $filename;
        }

        $leaveApp->status = "Approved";
        $leaveApp->save();

        //If the approved leave is a Replacement leave, assign earned to Replacement, and add day balance to Annual
        if ($leaveApp->leaveType->name == 'Replacement') {
            $lt = LeaveEarning::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', $leaveApp->leave_type_id)
                    ->where('user_id', $leaveApp->user_id);
            })->first();

            $lt->no_of_days += $leaveApp->total_days;

            $lt->save();

            //Add balance to annual;
            $lb = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '1')
                    ->where('user_id', $leaveApp->user_id);
            })->first();

            $lb->no_of_days += $leaveApp->total_days;
            $lb->save();

            //Send status update email
            return back()->with('message', 'Leave Record Added Succesfully');
        }

        //If the approved leave is a Sick leave, deduct the amount taken in both sick leave and hospitalization balance
        if ($leaveApp->leaveType->name == 'Sick') {
            //Add in amount sick leave taken
            $lt = TakenLeave::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', $leaveApp->leave_type_id)
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $lt->no_of_days += $leaveApp->total_days;
            $lt->save();

            //Deduct balance in sick leave balance
            $sickBalance = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '3')
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $sickBalance->no_of_days -= $leaveApp->total_days;
            $sickBalance->save();

            //Deduct balance in hosp leave balance
            $hospBalance = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '4')
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $hospBalance->no_of_days -= $leaveApp->total_days;
            $hospBalance->save();

            return back()->with('message', 'Sick leave application status updated succesfully');
        }

        //If the approved leave is an emergency leave, deduct the taken amount to Annual Leave
        if ($leaveApp->leaveType->name == 'Emergency') {
            //Add in amount emergency leave taken
            $lt = TakenLeave::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', $leaveApp->leave_type_id)
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $lt->no_of_days += $leaveApp->total_days;
            $lt->save();

            //Deduct balance in emergency leave balance
            $emBalance = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '6')
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $emBalance->no_of_days -= $leaveApp->total_days;
            $emBalance->save();

            //Deduct balance in annual leave
            $annBalance = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '1')
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $annBalance->no_of_days -= $leaveApp->total_days;
            $annBalance->save();
            //dd($annBalance->no_of_days);

            return back()->with('message', 'Emergency leave application status updated succesfully');
        }

        //Update leave taken table
        //Check for existing record
        $dupcheck = TakenLeave::where(function ($query) use ($leaveApp) {
            $query->where('leave_type_id', $leaveApp->leave_type_id)
                ->where('user_id', $leaveApp->user_id);
        })->first();

        //If does not exist, create new
        if ($dupcheck == null) {
            $tl = new TakenLeave;
            $tl->leave_type_id = $leaveApp->leave_type_id;
            $tl->user_id = $leaveApp->user_id;
            $tl->no_of_days = $leaveApp->total_days;
            $tl->save();
        }
        //else update existing
        else {
            $dupcheck->no_of_days += $leaveApp->total_days;
            $dupcheck->save();
        }

        //Update leave balance table
        //Check for existing record
        $dupcheck2 = LeaveBalance::where(function ($query) use ($leaveApp) {
            $query->where('leave_type_id', $leaveApp->leave_type_id)
                ->where('user_id', $leaveApp->user_id);
        })->first();

        //If does not exist, create new
        if ($dupcheck2 == null) {
            $lb = new LeaveBalance;
            $lb->leave_type_id = $leaveApp->leave_type_id;
            $lb->user_id = $leaveApp->user_id;
            $le = LeaveEarning::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', $leaveApp->leave_type_id)
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $lb->no_of_days = $le->no_of_days - $leaveApp->total_days;
            $lb->save();
        }
        //else update existing
        else {
            $dupcheck2->no_of_days -= $leaveApp->total_days;
            $dupcheck2->save();
        }

         //Record in activity history
         $hist = new History;
         $hist->leave_application_id = $leaveApp->id;
         $hist->user_id = auth()->user()->id;
         $hist->action = "Applied on Behalf";
         $hist->save();
        //Send email notification
        //Notification::route('mail', $leaveApp->approver_one->email)->notify(new NewApplication($leaveApp));

        //$leaveApp->approver_one->notify(new NewApplication($leaveApp));

        //STORE
        return back()->with('message', 'Leave record submitted succesfully');
    }

    public function list(Request $request){

        $user_id = $request->user_id;
        $user = User::where('id',$user_id)->first();
        if($user->name == $request->user_name){
            $leaveApps = LeaveApplication::select('id','user_id','leave_type_id','status','date_from','date_to','apply_for','date_resume','total_days','reason' ,'relief_personnel_id','attachment','updated_at')->where(function ($query) use ($user_id) {
                $query->where('status', 'PENDING_1')
                    ->where('approver_id_1', $user_id);
            })->orWhere(function ($query) use ($user_id) {
                $query->where('status', 'PENDING_2')
                    ->where('approver_id_2', $user_id);
            })->orWhere(function ($query) use ($user_id) {
                $query->where('status', 'PENDING_3')
                    ->where('approver_id_3', $user_id);
            })->with('user','relief_personnel','leaveType')->get();

            $leaveApps->makeVisible('attachment_url')->toArray();
            return response()->json($leaveApps);
        }
        return response()->json("Failed");
    }

    public function list_my_pending(Request $request){

        $user_id = $request->user_id;
        $user = User::where('id',$user_id)->first();
        if($user->name == $request->user_name){

                $leaves = LeaveApplication::where(function ($query) use ($user) {
                    $query->where('status', 'PENDING_1')
                        ->where('user_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('status', 'PENDING_2')
                        ->where('user_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('status', 'PENDING_3')
                        ->where('user_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('status', 'APPROVED')
                        ->where('user_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('status', 'CANCELLED')
                        ->where('user_id', $user->id);
                })->with('user','relief_personnel','leaveType')->get();


            $leaves->makeVisible('attachment_url')->toArray();
            return response()->json($leaves);
        }
        return response()->json("Failed");
    }

    public function pending_count(Request $request){

        $user_id = $request->user_id;
        $user = User::where('id',$user_id)->first();
        if($user->name == $request->user_name){
            $leaveApps = LeaveApplication::where(function ($query) use ($user_id) {
                $query->where('status', 'PENDING_1')
                    ->where('approver_id_1', $user_id);
            })->orWhere(function ($query) use ($user_id) {
                $query->where('status', 'PENDING_2')
                    ->where('approver_id_2', $user_id);
            })->orWhere(function ($query) use ($user_id) {
                $query->where('status', 'PENDING_3')
                    ->where('approver_id_3', $user_id);
            })->get();

            $total_pending = count($leaveApps);

            return response()->json(['total_pending' => $total_pending]);
        }
        return response()->json("Failed");
    }

    public function mobile_action(Request $request){

        $user_id = $request->user_id;
        $user = User::where('id',$user_id)->first();
        $leaveApplication = LeaveApplication::where('id',$request->leave_app_id)->first();

        if($user->name == $request->user_name){

            if($request->action == "Approve"){
                //Get leave application authorities ID
                $la_1 = $leaveApplication->approver_id_1;
                $la_2 = $leaveApplication->approver_id_2;
                $la_3 = $leaveApplication->approver_id_3;

                $old_status = $leaveApplication->status;

                //If user id same as approver id 1
                if ($la_1 == $user->id) {
                    //if no authority 2, terus change to approved
                    if ($la_2 == null) {
                        $leaveApplication->status = 'APPROVED';
                    }
                    //else update status to pending 2,
                    else {
                        $leaveApplication->status = 'PENDING_2';

                        //Notify the second approver
                        $leaveApplication->approver_two->notify(new NewApplication($leaveApplication));
                        $this->mobile_notification($leaveApplication, "authority_2");
                    }
                }
                //if user id same as approved id 2
                else if ($la_2 == $user->id) {
                    //if no authority 3, terus change to approved
                    if ($la_3 == null) {
                        $leaveApplication->status = 'APPROVED';
                    }
                    //else update status to pending 3
                    else {
                        $leaveApplication->status = 'PENDING_3';
                        //Notify the third approver
                        $leaveApplication->approver_three->notify(new NewApplication($leaveApplication));
                        $this->mobile_notification($leaveApplication, "authority_3");
                    }
                }
                //If user id same as approved id 3, update status to approved
                else {
                    $leaveApplication->status = 'APPROVED';
                }
                $leaveApplication->update();

                //If the application is approved
                if ($leaveApplication->status == 'APPROVED') {

                    //If the approved leave is a Replacement leave, assign earned to Replacement, and add day balance to Annual
                    if ($leaveApplication->leaveType->name == 'Replacement') {
                        $lt = LeaveEarning::where(function ($query) use ($leaveApplication) {
                            $query->where('leave_type_id', $leaveApplication->leave_type_id)
                                ->where('user_id', $leaveApplication->user_id);
                        })->first();

                        $lt->no_of_days += $leaveApplication->total_days;

                        $lt->save();

                        //Add balance to annual;
                        $lb = LeaveBalance::where(function ($query) use ($leaveApplication) {
                            $query->where('leave_type_id', '1')
                                ->where('user_id', $leaveApplication->user_id);
                        })->first();

                        $lb->no_of_days += $leaveApplication->total_days;
                        $lb->save();

                        //Record in activity history
                        $hist = new History;
                        $hist->leave_application_id = $leaveApplication->id;
                        $hist->user_id = $user->id;
                        $hist->action = "Approved";
                        $hist->save();

                        //Send status update email
                        $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                        $this->mobile_notification($leaveApplication, "employee");
                        return response()->json("Success");
                        // return redirect()->to('/admin')->with('message', 'Replacement leave application status updated succesfully');
                    }

                    //If the approved leave is a Sick leave, deduct the amount taken in both sick leave and hospitalization balance
                    if ($leaveApplication->leaveType->name == 'Sick') {
                        //Add in amount sick leave taken
                        $lt = TakenLeave::where(function ($query) use ($leaveApplication) {
                            $query->where('leave_type_id', $leaveApplication->leave_type_id)
                                ->where('user_id', $leaveApplication->user_id);
                        })->first();
                        $lt->no_of_days += $leaveApplication->total_days;
                        $lt->save();

                        //Deduct balance in sick leave balance
                        $sickBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                            $query->where('leave_type_id', '3')
                                ->where('user_id', $leaveApplication->user_id);
                        })->first();
                        $sickBalance->no_of_days -= $leaveApplication->total_days;
                        $sickBalance->save();

                        //Deduct balance in hosp leave balance
                        $hospBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                            $query->where('leave_type_id', '4')
                                ->where('user_id', $leaveApplication->user_id);
                        })->first();
                        $hospBalance->no_of_days -= $leaveApplication->total_days;
                        $hospBalance->save();

                        //Record in activity history
                        $hist = new History;
                        $hist->leave_application_id = $leaveApplication->id;
                        $hist->user_id = $user->id;
                        $hist->action = "Approved";
                        $hist->save();

                        //Send status update email
                        $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                        $this->mobile_notification($leaveApplication, "employee");
                        return response()->json("Success");
                        // return redirect()->to('/admin')->with('message', 'Sick leave application status updated succesfully');
                    }

                    //If the approved leave is an emergency leave, deduct the taken amount to Annual Leave
                    if ($leaveApplication->leaveType->name == 'Emergency') {
                        //Add in amount emergency leave taken
                        $lt = TakenLeave::where(function ($query) use ($leaveApplication) {
                            $query->where('leave_type_id', $leaveApplication->leave_type_id)
                                ->where('user_id', $leaveApplication->user_id);
                        })->first();
                        $lt->no_of_days += $leaveApplication->total_days;
                        $lt->save();

                        //Deduct balance in emergency leave balance
                        $emBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                            $query->where('leave_type_id', '6')
                                ->where('user_id', $leaveApplication->user_id);
                        })->first();
                        $emBalance->no_of_days -= $leaveApplication->total_days;
                        $emBalance->save();

                        //Deduct balance in annual leave
                        $annBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                            $query->where('leave_type_id', '1')
                                ->where('user_id', $leaveApplication->user_id);
                        })->first();
                        if($leaveApplication->total_days >  $annBalance->no_of_days){
                            $leaveApplication->status = $old_status;
                            $leaveApplication->update();
                            //  return redirect()->to('/admin')->with('error', 'Employee does not have enough leave balance');
                             return response()->json("Failed: Employee does not have enough leave balance");
                        }
                        $annBalance->no_of_days -= $leaveApplication->total_days;
                        $annBalance->save();
                        //dd($annBalance->no_of_days);

                        //Record in activity history
                        $hist = new History;
                        $hist->leave_application_id = $leaveApplication->id;
                        $hist->user_id = $user->id;
                        $hist->action = "Approved";
                        $hist->save();

                        //Send status update email
                        $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                        $this->mobile_notification($leaveApplication,"employee");
                        return response()->json("Success");
                        // return redirect()->to('/admin')->with('message', 'Emergency leave application status updated succesfully');
                    }

                    //Update leave taken table
                    //Check for existing record
                    $dupcheck = TakenLeave::where(function ($query) use ($leaveApplication) {
                        $query->where('leave_type_id', $leaveApplication->leave_type_id)
                            ->where('user_id', $leaveApplication->user_id);
                    })->first();

                    //If does not exist, create new
                    if ($dupcheck == null) {
                        $tl = new TakenLeave;
                        $tl->leave_type_id = $leaveApplication->leave_type_id;
                        $tl->user_id = $leaveApplication->user_id;
                        $tl->no_of_days = $leaveApplication->total_days;
                        $tl->save();
                    }
                    //else update existing
                    else {
                        $dupcheck->no_of_days += $leaveApplication->total_days;
                        $dupcheck->save();
                    }

                    //Update leave balance table
                    //Check for existing record
                    $dupcheck2 = LeaveBalance::where(function ($query) use ($leaveApplication) {
                        $query->where('leave_type_id', $leaveApplication->leave_type_id)
                            ->where('user_id', $leaveApplication->user_id);
                    })->first();

                    //If does not exist, create new
                    if ($dupcheck2 == null) {
                        $lb = new LeaveBalance;
                        $lb->leave_type_id = $leaveApplication->leave_type_id;
                        $lb->user_id = $leaveApplication->user_id;
                        $le = LeaveEarning::where(function ($query) use ($leaveApplication) {
                            $query->where('leave_type_id', $leaveApplication->leave_type_id)
                                ->where('user_id', $leaveApplication->user_id);
                        })->first();
                        $lb->no_of_days = $le->no_of_days - $leaveApplication->total_days;
                        $lb->save();
                    }
                    //else update existing
                    else {
                        if($leaveApplication->total_days > $dupcheck2->no_of_days){
                            // return redirect()->to('/admin')->with('error', 'Employee does not have enough leave balance');
                            return response()->json("Failed: Employee does not have enough leave balance");
                        }
                        else{
                            $dupcheck2->no_of_days -= $leaveApplication->total_days;
                            $dupcheck2->save();
                        }
                    }
                }

                //Record in activity history
                $hist = new History;
                $hist->leave_application_id = $leaveApplication->id;
                $hist->user_id = $user->id;
                $hist->action = $leaveApplication->status;
                $hist->save();



                //Send status update email
                $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                $this->mobile_notification($leaveApplication,"employee");
                return response()->json("Success");
            }
            else{

                //Get leave application authorities ID
                $la_1 = $leaveApplication->approver_id_1;
                $la_2 = $leaveApplication->approver_id_2;
                $la_3 = $leaveApplication->approver_id_3;

                //If user id same as approver id 1
                if ($la_1 == $user->id) {
                    $leaveApplication->status = 'DENIED_1';
                }
                //if user id same as approved id 2
                else if ($la_2 == $user->id) {
                    $leaveApplication->status = 'DENIED_2';
                }
                //If user id same as approved id 3,
                else {
                    $leaveApplication->status = 'DENIED_3';
                }
                $leaveApplication->remarks = $request->remarks;
                $leaveApplication->remarker_id = $request->user_id;
                $leaveApplication->update();

                //Record in activity history
                $hist = new History;
                $hist->leave_application_id = $leaveApplication->id;
                $hist->user_id = $user->id;
                $hist->action = "Denied";
                $hist->save();

                //Send status update email
                $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                return response()->json("Success");
                }
            }
        return response()->json("Failed");
    }


    public function mobile_notification(LeaveApplication $leaveApplication, $personnel){
        $endpoint = "https://wspace.io/api/push-notification/android";
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $leave_type = $leaveApplication->leaveType->name;
        $user_id = $leaveApplication->user_id;
        $title = "";
        $body = "";
        //dd($personnel);
        if($personnel == "employee"){
            $user_id = $leaveApplication->user_id;
            if($leaveApplication->status == "APPROVED"){
                $title = "Leave Application Approved";
                $body = "Your ".$leave_type." leave application has been approved.";
                if($leaveApplication->leave_type_id == "12"){
                    $title = 'Leave Claim Application Approved';
                    $body = 'Your replacement leave claim application has been approved';
                }
            }
            else if($leaveApplication->status == "PENDING_1" || $leaveApplication->status == "PENDING_2" || $leaveApplication->status == "PENDING_3"){
                $title = $leave_type." Leave Application Status Updated";

                if($leaveApplication->leave_type_id == "12"){
                    $title = 'Leave Claim Application Status Update';
                }
                if($leaveApplication->status == 'PENDING_1'){
                    $currAuth = $leaveApplication->approver_one->name;
                }
                else if($leaveApplication->status == 'PENDING_2'){
                    $currAuth = $leaveApplication->approver_two->name;
                }
                else if($leaveApplication->status == 'PENDING_3'){
                    $currAuth = $leaveApplication->approver_three->name;
                }
                $body = 'Waiting approval by '.$currAuth;
            }
            else if($leaveApplication->status == 'DENIED_1'|| $leaveApplication->status == 'DENIED_2'|| $leaveApplication->status == 'DENIED_3' ){

                $title = 'Leave Application Denied';

                if($leaveApplication->leave_type_id == "12"){
                    $title = 'Leave Claim Application Denied';
                }
                if($leaveApplication->status == 'DENIED_1'){
                    $currAuth = $leaveApplication->approver_one->name;
                }
                else if($leaveApplication->status == 'DENIED_2'){
                    $currAuth = $leaveApplication->approver_two->name;
                }
                else if($la->status == 'DENIED_3'){
                    $currAuth = $leaveApplication->approver_three->name;
                }
                $body = 'Denied by '.$currAuth;
            }
        }
        else{

            if($personnel == "authority_1"){
                    $user_id = $leaveApplication->approver_id_1;
                    $title = "Leave Application Waiting Approval";
                    $body = $leave_type." Leave Application by ".$leaveApplication->user->name." waiting for your approval.";

            }
            else if($personnel == "authority_2"){
                $user_id = $leaveApplication->approver_id_2;
                $title = "Leave Application Waiting Approval";
                $body = $leave_type." Leave Application by ".$leaveApplication->user->name." waiting for your approval.";

            }
            else if($personnel == "authority_3"){
                $user_id = $leaveApplication->approver_id_3;
                $title = "Leave Application Waiting Approval";
                $body = $leave_type." Leave Application by ".$leaveApplication->user->name." waiting for your approval.";
            }
        }


        $response = $client->request('POST', $endpoint, [
            'form_params' => [
                'users' => [$user_id],
                'title' => $title,
                'body' => $body
            ]
        ]);

        // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

        $statusCode = $response->getStatusCode();
        $content = $response->getBody();

        // or when your server returns json
        $content = json_decode($response->getBody(), true);
        return $statusCode;
    }

}
