<?php

namespace App\Http\Controllers;

use App\Exports\ContractTypeExport;
use App\Models\Contract;
use App\Models\ContractType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ContractTypeController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage constant contract type')) {
            $types = ContractType::where('created_by', \Auth::user()->creatorId())->get();
            return view('contractType.index', compact('types'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        return view('contractType.create');
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create constant contract type')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $types             = new ContractType();
        $types->name       = $request->name;
        $types->created_by = \Auth::user()->creatorId();
        $types->save();

        return redirect()->route('contractType.index')->with('success', __('Contract Type successfully created.'));
    }

    public function show(ContractType $contractType)
    {
        return redirect()->route('contractType.index');
    }

    public function edit(ContractType $contractType)
    {
        return view('contractType.edit', compact('contractType'));
    }

    public function update(Request $request, ContractType $contractType)
    {
        if (!\Auth::user()->can('edit constant contract type')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $contractType->name       = $request->name;
        $contractType->created_by = \Auth::user()->creatorId();
        $contractType->save();

        return redirect()->route('contractType.index')->with('success', __('Contract Type successfully updated.'));
    }

    public function destroy(ContractType $contractType)
    {
        if (!\Auth::user()->can('delete constant contract type')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $data = Contract::where('type', $contractType->id)->first();
        if (!empty($data)) {
            return redirect()->back()->with('error', __('this type is already use so please transfer or delete this type related data.'));
        }

        $contractType->delete();

        return redirect()->route('contractType.index')->with('success', __('Contract Type successfully deleted.'));
    }

    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete constant contract type')) {
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

        $types = ContractType::where('created_by', \Auth::user()->creatorId())
            ->whereIn('id', $ids)
            ->get();

        foreach ($types as $type) {
            $inUse = Contract::where('type', $type->id)->exists();
            if ($inUse) {
                $blocked[] = ['id' => $type->id, 'name' => $type->name];
                continue;
            }
            $type->delete();
            $deleted++;
        }

        $msgParts = [];
        if ($deleted > 0) {
            $msgParts[] = trans_choice(':count type deleted.|:count types deleted.', $deleted, ['count' => $deleted]);
        }
        if (count($blocked) > 0) {
            $msgParts[] = __('Some types could not be deleted because they are in use.');
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

    public function export()
    {
        if (!\Auth::user()->can('manage constant contract type')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $company = \Auth::user()->name ?? 'Company';
        $company = preg_replace('/[^A-Za-z0-9\-_]/', '_', $company);
        $file = "contract_types_{$company}_" . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new ContractTypeExport(), $file);
    }

    public function exportSelected(Request $request)
    {
        if (!\Auth::user()->can('manage constant contract type')) {
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
        $file = "contract_types_selected_{$company}_" . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new ContractTypeExport($ids), $file);
    }
}
