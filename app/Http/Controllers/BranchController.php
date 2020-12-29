<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use App\State;
use App\Branch;

class BranchController extends Controller
{
    public function index(){
        $branches = Branch::all();
        return view('branch.index')->with(compact('branches'));
    }

    public function create(){
        $countries = Country::all();
        $states = State::all();
        return view('branch.create')->with(compact('states','countries'));
    }

    public function store(Request $request){
        $branch = new Branch;
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->zipcode = $request->zipcode;
        $branch->city = $request->city;
        $branch->state_id = $request->state_id;
        $branch->country_id = $request->country_id;
        $branch->save();

        return redirect()->to('/branches/index')->with('message', $branch->name . ' added succesfully.');
    }

    public function edit(Branch $branch){
        $countries = Country::all();
        $states = State::where('country_id', $branch->country_id)->get();
        return view('branch.edit')->with(compact('branch','states','countries'));
    }

    public function update(Request $request, Branch $branch){
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->zipcode = $request->zipcode;
        $branch->city = $request->city;
        $branch->state_id = $request->state_id;
        $branch->country_id = $request->country_id;
        $branch->save();

        return redirect()->to('/branches/index')->with('message', $branch->name . ' updated succesfully.');
    }

    // public function add_specific(State $state){
    //     $countries = Country::all();
    //     $states = State::all();
    //     return view('state.create')->with(compact('countries','states','state'));
    // }

    public function delete(){

    }
}
