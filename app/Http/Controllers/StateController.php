<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\State;
use App\Country;

class StateController extends Controller
{
    public function index(){
        $states = State::all();
        $countries = Country::all();
        return view('state.index')->with(compact('states','countries'));
    }

    public function create(){
        $countries = Country::all();
        return view('state.create')->with(compact('countries'));
    }

    public function store(Request $request){
        $state = new State;
        $state->name = $request->name;
        $state->country_id = $request->country_id;
        $state->save();
        return redirect()->to('/states/index')->with('message', $state->name . ' created succesfully.');
    }

    public function edit(State $state){
        $countries = Country::all();
        return view('state.edit')->with(compact('state','countries'));
    }

    public function update(Request $request, State $state){
        $state_name = $request->name;
        $state->name = $state_name;
        $state->country_id = $request->country_id;
        $state->save();
        return redirect()->to('/states/index')->with('message', $state->name . ' updated succesfully.');
    }

    public function add_specific(Country $country){
        $countries = Country::all();
        return view('state.create')->with(compact('countries','country'));
    }

    public function filter(Request $request){
        $country_id = $request->id;
        $country = Country::where('id',$country_id)->first();
        $states = $country->states;
        return response()->json($states);
    }

    public function delete(){

    }
}
