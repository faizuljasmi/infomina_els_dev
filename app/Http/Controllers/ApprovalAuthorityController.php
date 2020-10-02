<?php


namespace App\Http\Controllers;

/**
 * @author Faizul Jasmi
 * @email faizul.jasmi@infomina.com.my
 * @create date 2020-01-07 09:03:50
 * @modify date 2020-01-07 09:03:50
 * @desc [description]
 */

use App\ApprovalAuthority;
use Illuminate\Http\Request;
use App\User;

class ApprovalAuthorityController extends Controller
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
    public function create(User $user)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        //Gets user ID
        $user_id = $user->id;
        //dd($request->authority_1_id);
        //$approvalAuth = ApprovalAuthority::create(request(['user_id','authority_1_id','authority_2_id','authority_3_id']));

        //Create new instance of ApprvAuth
        $aa = new ApprovalAuthority;
        //Assign all attributes
        $aa->user_id = $user_id;
        $aa->authority_1_id = $request->authority_1_id;
        $aa->authority_2_id = $request->authority_2_id;
        $aa->authority_3_id = $request->authority_3_id;
        //Save
        $aa->save();
        return redirect()->route('user_view', ['user' => $user])->with('message', 'User approval authority created succesfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ApprovalAuthority  $approvalAuthority
     * @return \Illuminate\Http\Response
     */
    public function show(ApprovalAuthority $approvalAuthority)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ApprovalAuthority  $approvalAuthority
     * @return \Illuminate\Http\Response
     */
    public function edit(ApprovalAuthority $approvalAuthority)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ApprovalAuthority  $approvalAuthority
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ApprovalAuthority $approvalAuthority)
    {
        $approvalAuthority->update($request->only('authority_1_id','authority_2_id','authority_3_id'));
        return redirect()->route('user_view', ['user' => $approvalAuthority->user_id])->with('message', 'User approval authority updated succesfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ApprovalAuthority  $approvalAuthority
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApprovalAuthority $approvalAuthority)
    {
        //
    }
}
