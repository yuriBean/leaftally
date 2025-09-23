<?php

namespace Modules\LandingPage\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LandingPage\Entities\LandingPageSetting;

class PricingPlanController extends Controller
{
    public function index()
    {
        $settings = LandingPageSetting::settings();
        return view('landingpage::landingpage.pricing_plan', compact('settings'));
    }

    public function create()
    {
        return view('landingpage::create');
    }

    public function store(Request $request)
    {
        $data['is_pricing_plan_section_on']= isset($request->is_pricing_plan_section_on) && $request->is_pricing_plan_section_on == 'on' ? 'on' : 'off' ;
        $data['plan_title']= $request->plan_title;
        $data['plan_heading']= $request->plan_heading;
        $data['plan_description']= $request->plan_description;

        foreach($data as $key => $value){
            LandingPageSetting::updateOrCreate(['name' =>  $key],['value' => $value]);
        }

        return redirect()->back()->with(['success'=> 'Plan update successfully']);
    }

    public function show($id)
    {
        return view('landingpage::show');
    }

    public function edit($id)
    {
        return view('landingpage::edit');
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
