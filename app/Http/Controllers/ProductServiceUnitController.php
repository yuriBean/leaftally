<?php

namespace App\Http\Controllers;

use App\Exports\ProductUnitExport;
use App\Models\ProductService;
use App\Models\ProductServiceUnit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductServiceUnitController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage constant unit')) {
            $units = ProductServiceUnit::where('created_by', \Auth::user()->creatorId())->get();
            return view('productServiceUnit.index', compact('units'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        if (\Auth::user()->can('create constant unit')) {
            return view('productServiceUnit.create');
        }
        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create constant unit')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $unit             = new ProductServiceUnit();
        $unit->name       = $request->name;
        $unit->created_by = \Auth::user()->creatorId();
        $unit->save();

        return redirect()->route('product-unit.index')->with('success', __('Unit successfully created.'));
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit constant unit')) {
            $unit = ProductServiceUnit::find($id);
            return view('productServiceUnit.edit', compact('unit'));
        }
        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit constant unit')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $unit = ProductServiceUnit::find($id);
        if ($unit->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $unit->name = $request->name;
        $unit->save();

        return redirect()->route('product-unit.index')->with('success', __('Unit successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete constant unit')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $unit = ProductServiceUnit::find($id);
        if ($unit->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $inUse = ProductService::where('unit_id', $unit->id)->first();
        if (!empty($inUse)) {
            return redirect()->back()->with('error', __('this unit is already assign so please move or remove this unit related data.'));
        }

        $unit->delete();

        return redirect()->route('product-unit.index')->with('success', __('Unit successfully deleted.'));
    }

    /**
     * BULK DELETE
     */
    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete constant unit')) {
            return $request->expectsJson()
                ? response()->json(['message' => __('Permission denied.')], 403)
                : redirect()->back()->with('error', __('Permission denied.'));
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids)));
        }
        $ids = array_values(array_unique(array_map('intval', (array) $ids)));

        if (empty($ids)) {
            return $request->expectsJson()
                ? response()->json(['message' => __('No records selected.')], 422)
                : redirect()->back()->with('error', __('No records selected.'));
        }

        $deleted = 0;
        $blocked = [];

        $units = ProductServiceUnit::where('created_by', \Auth::user()->creatorId())
            ->whereIn('id', $ids)
            ->get();

        foreach ($units as $unit) {
            $inUse = ProductService::where('unit_id', $unit->id)->exists();
            if ($inUse) {
                $blocked[] = ['id' => $unit->id, 'name' => $unit->name];
                continue;
            }
            $unit->delete();
            $deleted++;
        }

        $msgParts = [];
        if ($deleted > 0) {
            $msgParts[] = trans_choice(':count unit deleted.|:count units deleted.', $deleted, ['count' => $deleted]);
        }
        if (count($blocked) > 0) {
            $msgParts[] = __('Some units could not be deleted because they are in use.');
        }
        $message = implode(' ', $msgParts) ?: __('No changes made.');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'deleted' => $deleted,
                'blocked' => $blocked,
            ]);
        }

        return redirect()->back()->with($deleted ? 'success' : 'error', $message);
    }

    /**
     * EXPORT ALL
     */
    public function export()
    {
        if (!\Auth::user()->can('manage constant unit')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $company = \Auth::user()->name ?? 'Company';
        $company = preg_replace('/[^A-Za-z0-9\-_]/', '_', $company);
        $file = "units_{$company}_" . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new ProductUnitExport(), $file);
    }

    /**
     * EXPORT SELECTED
     */
    public function exportSelected(Request $request)
    {
        if (!\Auth::user()->can('manage constant unit')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids)));
        }
        $ids = array_values(array_unique(array_map('intval', (array) $ids)));

        if (empty($ids)) {
            return redirect()->back()->with('error', __('No records selected.'));
        }

        $company = \Auth::user()->name ?? 'Company';
        $company = preg_replace('/[^A-Za-z0-9\-_]/', '_', $company);
        $file = "units_selected_{$company}_" . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new ProductUnitExport($ids), $file);
    }

public function short(Request $request)
{
    if (\Auth::user()->can('create constant unit')) {
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|max:20',
            ]
        );

        // If validation fails
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json([
                'status' => 0,
                'message' => $messages->first(),
            ], 400); // HTTP 400 Bad Request
        }

        // Create the new unit
        $unit = new ProductServiceUnit();
        $unit->name = $request->name;
        $unit->created_by = \Auth::user()->creatorId();
        $unit->save();

        $units = ProductServiceUnit::all();
         $options = '';
        foreach ($units as $unit) {
            $options .= '<option value="' . $unit->id . '" ' . ($unit->id == $unit->id ? 'selected' : '') . '>' . $unit->name . '</option>';
        }

        return response()->json([
            'status' => 1,
            'message' => __('New Unit Added.'),
            'options' => $options,
        ], 200);
    } else {
        // Permission denied response
        return response()->json([
            'status' => 0,
            'message' => __('Permission denied.'),
        ], 403); // HTTP 403 Forbidden
    }
}

}
