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
use Carbon\Carbon;
use App\LeaveType;
use App\User;
use App\LeaveBalance;
use App\Holiday;

class ReplacementLeaveController extends Controller
{
    public function create()
    {

        //Get THIS user id
        $user = auth()->user();
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = User::orderBy('id', 'ASC')->where('emp_group_id', '=', $user->emp_group_id)->get()->except($user->id);
        //dd($groupMate->name);

        //Get approval authorities for this user
        //Change id to CYNTHIA'S ID
        $leaveAuth = User::orderBy('id', 'ASC')->where('id', '!=', '4')->where('user_type', 'Authority')->get()->except($user->id);

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

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

        return view('leaveapp.replacement')->with(compact('user',  'groupMates', 'leaveAuth', 'leaveBal', 'all_dates'));
    }

    public function store()
    {
    }

    public function edit()
    {
    }

    public function update()
    {
    }

    public function approve()
    {
    }

    public function deny()
    {
    }
}
