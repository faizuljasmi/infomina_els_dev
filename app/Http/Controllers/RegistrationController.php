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
use App\User;
use App\EmpType;
use App\EmpGroup;
use App\LeaveApplication;
use App\LeaveType;
use App\LeaveEntitlement;
use App\LeaveEarning;
use App\LeaveBalance;
use App\TakenLeave;
use App\BroughtForwardLeave;
use App\BurntLeave;
use Redirect,Response,DB,Config;
use Datatables;


class RegistrationController extends Controller
{

    public function create()
    {
        $user = auth()->user();
        $activeUsers = User::where('status','Active')->sortable(['staff_id'])->paginate(15,['*'],'active');
        $inactiveUsers = User::where('status','Inactive')->sortable(['staff_id'])->paginate(15,['*'],'inactive');
        //dd($users);
        $empTypes = EmpType::orderBy('id', 'ASC')->get();
        //dd($empTypes);
        $empGroups = EmpGroup::orderBy('id', 'ASC')->get();
        return view('registration.create')->with(compact('user','activeUsers','inactiveUsers','empTypes', 'empGroups'));
    }

    public function store(Request $request)
    {

        $this->validate(request(), [
            'name' => ['required', 'string', 'max:255'],
            'staff_id' => ['required', 'string', 'min:4', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'join_date' => ['required'],
            'job_title' => ['required', 'string'],
        ]);
        $user = new User;
        $user->name = $request->name;
        $user->staff_id = $request->staff_id;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->user_type = $request->user_type;
        $user->join_date = $request->join_date;
        $user->gender = $request->gender;
        $user->emp_type_id = $request->emp_type_id;
        $user->emp_group_id = $request->emp_group_id;

        $user->job_title = $request->job_title;
        $user->save();
        // $user = User::create(request(['name','staff_id','email','password','user_type','join_date', 'gender', 'emp_type_id','emp_group_id','job_title']));

        return redirect()->route('user_view', ['user' => $user])->with('message', 'User created succesfully');
    }

    public function edit(User $user)
    {
        $user = $user;
        $user_insesh = auth()->user();
        $users = User::orderBy('id', 'ASC')->get()->except($user->id);
        $authUsers = User::orderBy('name','ASC')->where(function ($query) {
            $query->where('user_type', 'Admin')
                ->orWhere('user_type', 'Authority')
                ->orWhere('user_type', 'Management');
        })->get();
        $empType = $user->emp_types;
        $empTypes = EmpType::orderBy('id', 'ASC')->get();
        $empGroup = $user->emp_group;
        $empGroup2 = $user->emp_group_two;
        $empGroup3 = $user->emp_group_three;
        $empGroup4 = $user->emp_group_four;
        $empGroup5 = $user->emp_group_five;
        $empGroups = EmpGroup::orderBy('id', 'ASC')->get();
        $empAuth = $user->approval_authority;
        //dd($empAuth);
        $leaveEnt = LeaveEntitlement::orderBy('id', 'ASC')->where('emp_type_id', '=', $empType->id)->get();
        $leaveEarn = LeaveEarning::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        $broughtFwd = BroughtForwardLeave::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        $leaveTak = TakenLeave::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        //dd($leaveEnt);
        $leaveTypes = LeaveType::orderBy('id', 'ASC')->get();
        $burntLeave = BurntLeave::where('user_id',$user->id)->where('leave_type_id',1)->first();
        return view('user.edit')->with(compact('user','user_insesh', 'users', 'authUsers', 'empType', 'empTypes', 'empGroup','empGroup2','empGroup3','empGroup4','empGroup5', 'empGroups', 'empAuth', 'leaveTypes', 'leaveEnt', 'leaveEarn', 'broughtFwd', 'leaveBal', 'leaveTak','burntLeave'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $user->update($request->only('name', 'staff_id', 'email', 'user_type', 'join_date', 'gender', 'emp_type_id', 'emp_group_id','emp_group_two_id','emp_group_three_id','emp_group_four_id','emp_group_five_id', 'job_title', 'emergency_contact_name', 'emergency_contact_no'));
        } catch (\Exception $e) { // It's actually a QueryException but this works too
            if ($e->getCode() == 23000) {
                return redirect()->route('user_view', ['user' => $user])->with('message', 'Staff ID has already been taken. User details not updated.');
            }
        }
        return redirect()->route('user_view', ['user' => $user])->with('message', 'User profile updated succesfully');
    }

    public function profile(User $user)
    {
        $user = $user;
        $user_insesh = auth()->user();
        $users = User::orderBy('id', 'ASC')->get()->except($user->id);

        $authUsers = User::orderBy('name','ASC')->where(function ($query) {
            $query->where('user_type', 'Admin')
                ->orWhere('user_type', 'Authority')
                ->orWhere('user_type', 'Management');
        })->get();
        $empType = $user->emp_types;
        $empGroup = $user->emp_group;
        $empGroup2 = $user->emp_group_two;
        $empGroup3 = $user->emp_group_three;
        $empGroup4 = $user->emp_group_four;
        $empGroup5 = $user->emp_group_five;
        $empAuth = $user->approval_authority;
        //dd($empAuth->getAuthorityOneAttribute);
        $leaveEnt = LeaveEntitlement::orderBy('id', 'ASC')->where('emp_type_id', '=', $empType->id)->get();
        //dd($leaveEnt);
        $leaveEarn = LeaveEarning::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        //dd($leaveEarn);
        $broughtFwd = BroughtForwardLeave::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();
        $leaveTak = TakenLeave::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

        $ann_taken_first_half = LeaveApplication::where('user_id',$user->id)->where('status','Approved')->where('leave_type_id',1)->where('created_at' ,'<=', '2020-06-30')->get();
        $total_ann_taken_first_half = 0;
        foreach($ann_taken_first_half as $ann){
            $total_ann_taken_first_half += $ann->total_days;
        }

        //dd($leaveEnt);
        $leaveTypes = LeaveType::orderBy('id', 'ASC')->get();
        //dd($user->name);
        $leaveHist = LeaveApplication::where(function ($query) use ($user) {
            $query->where('status', 'APPROVED')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'CANCELLED')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'PENDING_1')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'PENDING_2')
                ->where('user_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('status', 'PENDING_3')
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
        $burntLeave = BurntLeave::where('user_id',$user->id)->where('leave_type_id',1)->first();
        return view('user.profile')->with(compact('user','user_insesh', 'users', 'authUsers', 'empType', 'empGroup','empGroup2','empGroup3','empGroup4','empGroup5', 'empAuth', 'leaveTypes', 'leaveEnt', 'leaveEarn', 'broughtFwd', 'leaveBal', 'leaveTak','leaveHist','burntLeave','total_ann_taken_first_half'));
    }

      public function deactivate(User $user)
    {
        $user->status = 'Inactive';
        $user_email = $user->email;
        $user->email = "inactive.".$user_email;
        $user->update();
        return redirect()->route('user_create')->with('message', 'User has been deactivated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back();
    }

    public function search(Request $request)
    {
	$user = auth()->user();
        $search = $request->get('search');
        //dd($search);
        $activeUsers = User::where('status','Active')->where('name','like','%'.$search.'%')->paginate(15,['*'],'active');
        //$activeUsers = User::where('status','Active')->sortable(['staff_id'])->paginate(15,['*'],'active');
        //dd($users[0]->name);

        $inactiveUsers = User::where('status','Inactive')->sortable(['staff_id'])->paginate(15,['*'],'inactive');
        //dd($users);
        $empTypes = EmpType::orderBy('id', 'ASC')->get();
        //dd($empTypes);
        $empGroups = EmpGroup::orderBy('id', 'ASC')->get();

        return view('registration.create', ['users' => $activeUsers])->with(compact('user','activeUsers','inactiveUsers','empTypes', 'empGroups'));
    }
}
