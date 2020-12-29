<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;

class CountryController extends Controller
{
    public function index(){
        $countries = Country::all();
        return view('country.index')->with(compact('countries'));
    }

    public function create(){
        return view('country.create');
    }

    public function store(Request $request){
        $country = new Country;
        $country->name = $request->name;
        $country->save();
        return redirect()->to('/countries/index')->with('message', $country->name . ' created succesfully.');
    }

    public function edit(Country $country){
        return view('country.edit')->with(compact('country'));
    }

    public function update(Request $request, Country $country){
        $country_name = $request->name;
        $country->name = $country_name;
        $country->save();
        return redirect()->to('/countries/index')->with('message', $country->name . ' updated succesfully.');
    }

    public function delete(){

    }
}
