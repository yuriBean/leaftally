<?php

namespace Modules\LandingPage\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LandingPage\Entities\LandingPageSetting;
use Modules\LandingPage\Entities\JoinUs;
use Illuminate\Support\Facades\Validator;

class JoinUsController extends Controller
{
    public function index()
    {
        $join_us = JoinUs::get();
        return view('landingpage::landingpage.joinus', compact('join_us'));
    }

    public function create()
    {
        return view('landingpage::create');
    }

    public function store(Request $request)
    {

        $data['is_joinus_section_on']= isset($request->is_joinus_section_on) && $request->is_joinus_section_on == 'on' ? 'on' : 'off' ;
        $data['joinus_heading']= $request->joinus_heading;
        $data['joinus_description']= $request->joinus_description;

        foreach($data as $key => $value){
            LandingPageSetting::updateOrCreate(['name' =>  $key],['value' => $value]);
        }

        return redirect()->back()->with(['success'=> 'Setting update successfully']);

    }

    public function show($id)
    {
        return view('landingpage::landingpage.joinus');
    }

    public function edit($id)
    {
        return view('landingpage::landingpage.joinus');
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
        $join = JoinUs::find($id);
        $join->delete();

        return redirect()->back()->with(['success'=> 'You are joined with our community']);
    }

    public function joinUsUserStore(Request $request){

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|unique:join_us',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
    
            return redirect()->back()->with('error', $messages->first());
        }
        $join = new JoinUs;
        $join->email = $request->email;
        $join->save();
    
        return redirect()->back()->with(['success'=> 'You are joined with our community']);
    }
}
