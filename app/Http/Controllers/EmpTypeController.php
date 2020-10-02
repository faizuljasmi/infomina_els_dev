<?php

/**
 * @author Faizul Jasmi
 * @email faizul.jasmi@infomina.com.my
 * @create date 2020-01-07 09:03:50
 * @modify date 2020-01-07 09:03:50
 * @desc [description]
 */

namespace App\Http\Controllers;

use App\EmpType;
use Illuminate\Http\Request;
use App\LeaveType;
use App\LeaveEntitlement;

class EmpTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Get all employee types already been created
        $allTypes = $this->getAllTypes();
        //Get all leave types, to be assigned to employee type
        $allLeaveTypes = $this->getAllLeaveTypes();
        //Get leave entitlements
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->get();
        //dd($leaveEnt);

        return view('emptype.create')->with(compact('allTypes', 'allLeaveTypes', 'leaveEnt'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(),[
            'name' => ['required', 'string', 'max:255'],
        ]);
        //Create employee type
        $emptype = EmpType::create(request(['name']));

        return redirect()->to('/emptype/create')->with('message', 'Employee type created succesfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpType  $empType
     * @return \Illuminate\Http\Response
     */
    public function show(EmpType $empType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpType  $empType
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpType $empType)
    {
        //
        return view ('emptype.edit')->with(compact('empType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpType  $empType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmpType $empType)
    {
        $empType->update($request->only('name'));
        return redirect()->to('/emptype/create')->with('message', 'Employee type updated succesfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpType  $empType
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpType $empType)
    {
        //
        $empType->delete();
        return back();
    }

    protected function getAllLeaveTypes(){
        return LeaveType::orderBy('id')->get();
    }
    protected function getAllTypes(){
        return EmpType::orderBy('id')->get();
    }
}
