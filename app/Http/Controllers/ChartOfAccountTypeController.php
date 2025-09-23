<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccountType;
use Illuminate\Http\Request;

class ChartOfAccountTypeController extends Controller
{

    public function index()
    {
            $types = ChartOfAccountType::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('chartOfAccountType.index', compact('types'));
    }

    public function create()
    {
        return view('chartOfAccountType.create');
    }

    public function store(Request $request)
    {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $account             = new ChartOfAccountType();
            $account->name       = $request->name;
            $account->created_by = \Auth::user()->creatorId();
            $account->save();

            return redirect()->route('chart-of-account-type.index')->with('success', __('Chart of account type successfully created.'));
    }

    public function show(ChartOfAccountType $chartOfAccountType)
    {
    }

    public function edit(ChartOfAccountType $chartOfAccountType)
    {
        return view('chartOfAccountType.edit', compact('chartOfAccountType'));
    }

    public function update(Request $request, ChartOfAccountType $chartOfAccountType)
    {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $chartOfAccountType->name = $request->name;
            $chartOfAccountType->save();

            return redirect()->route('chart-of-account-type.index')->with('success', __('Chart of account type successfully updated.'));
    }

    public function destroy(ChartOfAccountType $chartOfAccountType)
    {
            $chartOfAccountType->delete();

            return redirect()->route('chart-of-account-type.index')->with('success', __('Chart of account type successfully deleted.'));
    }
}
