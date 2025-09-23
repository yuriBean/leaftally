<?php

namespace Modules\LandingPage\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LandingPage\Entities\LandingPageSetting;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landingpage::landingpage.topbar');
    }

    public function create()
    {
        return view('landingpage::create');
    }

    public function store(Request $request)
    {

        $data = [
            "topbar_status" => $request->topbar_status ? $request->topbar_status : "off",
            "feature_status" => $request->feature_status ? $request->feature_status : "off",
            "topbar_notification_msg" =>  $request->topbar_notification_msg,
        ];

        foreach($data as $key => $value){

            LandingPageSetting::updateOrCreate(['name' =>  $key],['value' => $value]);
        }

        return redirect()->back()->with(['success'=> 'Topbar setting update successfully']);

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
