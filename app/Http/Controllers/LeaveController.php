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
use App\BroughtForwardLeave;
use App\BurntLeave;
use App\EmpType;
use App\LeaveApplication;
use App\LeaveBalance;
use App\LeaveEarning;
use App\LeaveEntitlement;
use App\LeaveType;
use App\TakenLeave;
use App\User;

class LeaveController extends Controller
{
    public function setEarnings(Request $request, User $user)
    {

        //dd($request->all());
        $input = $request->all();

        //Loop thru each of it
        foreach ($input as $key => $val) {

            //To eliminate first entry which is token__
            if (strpos($key, 'leave_') === false) {
                continue;
            }
            //Trim, only in get the id
            $key = trim($key, "leave_");

            //Check for duplicate leave earning
            // $dupcheck = LeaveEarning::where('leave_type_id', '=', (int)$key, 'AND', 'user_id', '=', $user->id)->first();
            $dupcheck = LeaveEarning::orderBy('leave_type_id', 'ASC')->where(function ($query) use ($user, $key) {
                $query->where('leave_type_id', (int) $key)
                    ->where('user_id', $user->id);
            })->first();
            //Check for duplicate leave balance
            $lbCheck = LeaveBalance::orderBy('leave_type_id', 'ASC')->where(function ($query) use ($user, $key) {
                $query->where('leave_type_id', (int) $key)
                    ->where('user_id', $user->id);
            })->first();
            //Check for  Brought Forward duplicate
            $bfcheck = BroughtForwardLeave::orderBy('leave_type_id', 'ASC')->where(function ($query) use ($user, $key) {
                $query->where('leave_type_id', (int) $key)
                    ->where('user_id', $user->id);
            })->first();


            //If there is no  existing earning,save as new one
            if ($dupcheck == null) {

                //Set new leave earning
                $le = new LeaveEarning;
                $le->user_id = $user->id;
                $le->leave_type_id = (int) $key;
                $le->no_of_days = (float) $val;
                $le->save();

                //Set new leave taken
                $lt = new TakenLeave;
                $lt->user_id = $user->id;
                $lt->leave_type_id = (int) $key;
                $lt->no_of_days = 0;
                $lt->save();

                //Set new brough forward
                $bf = new BroughtForwardLeave;
                $bf->user_id = $user->id;
                $bf->leave_type_id = (int) $key;
                $bf->no_of_days = 0;
                $bf->save();

                //Set new leave balance
                $lb = new LeaveBalance;
                $lb->user_id = $user->id;
                $lb->leave_type_id = (int) $key;
                $lb->no_of_days = (float) $val;
                $lb->save();
            }

            //Else, find the difference between old and new earning, subtract from balance and earning
            else {
                //If the new set earn is lower than brought forward leave
                if ((int) $val < $bfcheck->no_of_days) {
                    return back()->with('message', 'Fail to set new earning. Make sure the value is not lower than existing brought forward leaves');
                }

                if ($dupcheck->no_of_days > (float) $val) {
                    //Decreasing, minus from leave earning
                    $diff = $dupcheck->no_of_days - (float) $val;
                    $dupcheck->no_of_days -= $diff;

                    //Decreasing, minus from balance
                    $lbCheck->no_of_days -= $diff;
                } else {
                    //Increasing, add to leave earning
                    $diff = (float) $val - $dupcheck->no_of_days;
                    $dupcheck->no_of_days += $diff;

                    //Increasing, add to balance
                    $lbCheck->no_of_days += $diff;
                    // $lbCheck->no_of_days += $bfcheck->no_of_days;
                }
                $dupcheck->save();
                $lbCheck->save();
            }
        }

        //update balance

        return back()->with('message', 'Leave earnings for' . $user->name . ' have been updated');
    }

    public function setBroughtForward(Request $request, User $user)
    {

        //dd($request->all());
        $input = $request->all();

        //Loop thru each of it
        foreach ($input as $key => $val) {

            //To eliminate first entry which is token__
            if (strpos($key, 'leave_') === false) {
                continue;
            }
            //Trim, only in get the id
            $key = trim($key, "leave_");
            //dd((float)$val);

            //Check for  Brought Forward duplicate
            $dupcheck = BroughtForwardLeave::orderBy('leave_type_id', 'ASC')->where(function ($query) use ($user, $key) {
                $query->where('leave_type_id', (int) $key)
                    ->where('user_id', $user->id);
            })->first();

            //Check leave earning for similar leave type
            $leCheck = LeaveEarning::orderBy('leave_type_id', 'ASC')->where(function ($query) use ($user, $key) {
                $query->where('leave_type_id', (int) $key)
                    ->where('user_id', $user->id);
            })->first();
            //Check leave balance of similar leave type
            $lbCheck = LeaveBalance::orderBy('leave_type_id', 'ASC')->where(function ($query) use ($user, $key) {
                $query->where('leave_type_id', (int) $key)
                    ->where('user_id', $user->id);
            })->first();

            //If there is no duplicate,save as new one
            if ($dupcheck == null) {
                $bf = new BroughtForwardLeave;
                $bf->user_id = $user->id;
                $bf->leave_type_id = (int) $key;
                $bf->no_of_days = (float) $val;
                $bf->save();

                //Add brought forward to leave earning
                if ($leCheck == null) {
                    $le = new LeaveEarning;
                    $le->user_id = $user->id;
                    $le->leave_type_id = (int) $key;
                    $le->no_of_days = (float) $val;
                    $le->save();

                    $lb = new LeaveBalance;
                    $lb->user_id = $user->id;
                    $lb->leave_type_id = (int) $key;
                    $lb->no_of_days = (float) $val;
                    $lb->save();
                }
                //If got existing earning, just update.
                else {
                    $leCheck->no_of_days += (float) $val;
                    $lbCheck->no_of_days = $leCheck->no_of_days;
                    $leCheck->save();
                    $lbCheck->save();
                }
            }
            //If got existing broughtforward, update existing
            else {

                //Add brought forward to leave earning
                if ($leCheck == null) {
                    $le = new LeaveEarning;
                    $le->user_id = $user->id;
                    $le->leave_type_id = (int) $key;
                    $le->no_of_days = (float) $val;
                    $le->save();

                    $lb = new LeaveBalance;
                    $lb->user_id = $user->id;
                    $lb->leave_type_id = (int) $key;
                    $lb->no_of_days = (float) $val;
                    $lb->save();
                }
                //If got existing earning, just update.
                else {
                    //If the new value is less than old value
                    if ($dupcheck->no_of_days > (float) $val) {
                        //Minus the diff from the earning
                        $diff = $dupcheck->no_of_days - (float) $val;
                        $leCheck->no_of_days -= $diff;
                        $lbCheck->no_of_days = $leCheck->no_of_days;
                    } else {
                        $diff = (float) $val - $dupcheck->no_of_days;
                        $leCheck->no_of_days += $diff;
                        $lbCheck->no_of_days = $leCheck->no_of_days;
                    }
                    $leCheck->save();
                    $lbCheck->save();
                }
                $dupcheck->no_of_days = (float) $val;
                $dupcheck->save();
            }
        }
        return back()->with('message', 'Brought forward leaves for ' . $user->name . ' have been updated');
    }
}
