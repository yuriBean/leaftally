<?php

namespace App\Http\Controllers;

use App\Exports\AssetExport;
use App\Models\Asset;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage assets')) {
            $assets = Asset::where('created_by', \Auth::user()->creatorId())->get();
            return view('assets.index', compact('assets'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        if (\Auth::user()->can('create assets')) {
            return view('assets.create');
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create assets')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name'               => 'required',
            'purchase_date'      => 'required',
            'supported_date'     => 'required',
            'amount'             => 'required|numeric|min:0',
            'depreciation_rate'  => 'nullable|numeric|min:0|max:100',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $assets                     = new Asset();
        $assets->name               = $request->name;
        $assets->purchase_date      = $request->purchase_date;
        $assets->supported_date     = $request->supported_date;
        $assets->amount             = $request->amount;
        $assets->depreciation_rate  = $request->depreciation_rate ?? 0.00;
        $assets->description        = $request->description;
        $assets->created_by         = \Auth::user()->creatorId();
        $assets->save();

        return redirect()->route('account-assets.index')->with('success', __('Assets successfully created.'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit assets')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        $asset = Asset::find($id);
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit assets')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $asset = Asset::find($id);
        if ($asset->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name'               => 'required',
            'purchase_date'      => 'required',
            'supported_date'     => 'required',
            'amount'             => 'required|numeric|min:0',
            'depreciation_rate'  => 'nullable|numeric|min:0|max:100',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $asset->name               = $request->name;
        $asset->purchase_date      = $request->purchase_date;
        $asset->supported_date     = $request->supported_date;
        $asset->amount             = $request->amount;
        $asset->depreciation_rate  = $request->depreciation_rate ?? 0.00;
        $asset->description        = $request->description;
        $asset->save();

        return redirect()->route('account-assets.index')->with('success', __('Assets successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete assets')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $asset = Asset::find($id);
        if ($asset->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $asset->delete();
        return redirect()->route('account-assets.index')->with('success', __('Assets successfully deleted.'));
    }

    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete assets')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ids = $request->input('ids', []);
        if (!is_array($ids) || count($ids) === 0) {
            return redirect()->back()->with('error', __('No items selected.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $toDelete = Asset::where('created_by', $creatorId)->whereIn('id', $ids)->pluck('id')->all();

        if (empty($toDelete)) {
            return redirect()->back()->with('error', __('Nothing to delete.'));
        }

        Asset::whereIn('id', $toDelete)->delete();

        return redirect()->back()->with('success', __(':count asset(s) deleted.', ['count' => count($toDelete)]));
    }

    public function export()
    {
        if (!\Auth::user()->can('manage assets')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $company  = preg_replace('/[^A-Za-z0-9\-_]/', '_', (\Auth::user()->name ?? 'Company'));
        $filename = "assets_{$company}_" . date('Y-m-d_H-i-s') . ".xlsx";

        return Excel::download(new AssetExport(), $filename);
    }

    public function exportSelected(Request $request)
    {
        if (!\Auth::user()->can('manage assets')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ids = $request->input('ids', []);
        if (!is_array($ids) || count($ids) === 0) {
            return redirect()->back()->with('error', __('No items selected.'));
        }

        $company  = preg_replace('/[^A-Za-z0-9\-_]/', '_', (\Auth::user()->name ?? 'Company'));
        $filename = "assets_selected_{$company}_" . date('Y-m-d_H-i-s') . ".xlsx";

        return Excel::download(new AssetExport($ids), $filename);
    }
}
