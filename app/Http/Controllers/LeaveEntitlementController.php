<?php

/**
 * @author Faizul Jasmi
 * @email faizul.jasmi@infomina.com.my
 * @create date 2020-01-07 09:03:50
 * @modify date 2020-01-07 09:03:50
 * @desc [description]
 */

namespace App\Http\Controllers;

use App\LeaveEntitlement;
use Illuminate\Http\Request;
use App\EmpType;
use App\LeaveType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class LeaveEntitlementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(EmpType $empType)
    {
        //
        $allLeaveTypes = $this->getAllLeaveTypes();
        //dd($allLeaveTypes[0]->name);
        $emp = $empType;
        //dd($emp->id);
        $leaveEnt = LeaveEntitlement::orderBy('id', 'ASC')->where('emp_type_id', '=', $emp->id)->get();
        //dd($leaveEnt);
        return view('leaveent.create')->with(compact('empType', 'allLeaveTypes', 'leaveEnt'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, EmpType $empType)
    {
        //Validate input is number
        // $this->validate(request(),[
        //     'no_of_days' => ['required', 'integer'],
        // ]);

        //Get all input from Create View
        $input = $request->all();
        //dd($input);
        //Loop thru each of it
        foreach ($input as $key => $val) {

            //To eliminate first entry which is token__
            if (strpos($key, 'leave_') === false) {
                continue;
            }
            //Trim, only in get the id
            $key = trim($key, "leave_");

            //Check for duplicate
            $dupcheck = LeaveEntitlement::where('leave_type_id', '=', (int) $key)->where('emp_type_id', '=', $empType->id)->first();
            //If there is no duplicate,save as new one
            if ($dupcheck == null) {

                $le = new LeaveEntitlement;
                $le->emp_type_id = $empType->id;
                $le->leave_type_id = (int) $key;
                $le->no_of_days = (float) $val;
                $le->save();
            }
            //If not, update.
            else {
                $dupcheck->no_of_days = (float) $val;
                $dupcheck->save();
                // $message = 'Entitled leave for '.$empType->name.' updated succesfully';
                // return redirect()->to('/emptype/create')->with('message', $message);
            }
            // dd($dupcheck->id);



            //echo $key;
            // LeaveEntitlement::create([
            //     'emp_type_id' => (int)$empType->id,
            //     'leave_type_id' => (int)$key,
            //     'no_of_days' => (float)$val,
            // ]);
        }

        // leave_1 => 4,
        // leave_2 => 5
        // leave_4 => 4

        //$leaveEnt = LeaveEntitlement::create(request(['name']));


        // foreach($input as $in){
        //      LeaveEntitlement::create([
        //         'emp_type_id' => $empType->id,
        //         'leave_type_id' => $request->leave_id[$key],
        //         'date' =>
        //         'cost' => $cost,
        //         'trend' => 0
        //     ]);
        // }


        return redirect()->to('/emptype/create')->with('message', 'Leave entitlement added succesfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LeaveEntitlement  $leaveEntitlement
     * @return \Illuminate\Http\Response
     */
    public function show(LeaveEntitlement $leaveEntitlement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LeaveEntitlement  $leaveEntitlement
     * @return \Illuminate\Http\Response
     */
    public function edit(LeaveEntitlement $leaveEntitlement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LeaveEntitlement  $leaveEntitlement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LeaveEntitlement $leaveEntitlement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LeaveEntitlement  $leaveEntitlement
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeaveEntitlement $leaveEntitlement)
    {
        //
    }

    protected function getAllLeaveTypes()
    {
        return LeaveType::orderBy('id')->get();
    }
}
