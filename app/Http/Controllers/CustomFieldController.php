<?php

namespace App\Http\Controllers;

use App\Exports\CustomFieldExport;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomFieldController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage constant custom field')) {
            $custom_fields = CustomField::where('created_by', \Auth::user()->creatorId())->get();
            return view('customFields.index', compact('custom_fields'));
        }
        return redirect()->back()->with('error', __('Permission Denied.'));
    }

    public function create()
    {
        if (\Auth::user()->can('create constant custom field')) {
            $types   = CustomField::$fieldTypes;
            $modules = CustomField::$modules;
            return view('customFields.create', compact('types', 'modules'));
        }
        return response()->json(['error' => __('Permission Denied.')], 401);
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create constant custom field')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name'   => 'required|max:20',
            'type'   => 'required',
            'module' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $custom_field             = new CustomField();
        $custom_field->name       = $request->name;
        $custom_field->type       = $request->type;
        $custom_field->module     = $request->module;
        $custom_field->options    = $request->type === 'select' ? $request->options : null;
        $custom_field->created_by = \Auth::user()->creatorId();
        $custom_field->save();

        return redirect()->route('custom-field.index')->with('success', __('Custom Field successfully created!'));
    }

    public function show(CustomField $customField)
    {
        return redirect()->route('custom-field.index');
    }

    public function edit(CustomField $customField)
    {
        if (\Auth::user()->can('edit constant custom field')) {
            if ($customField->created_by == \Auth::user()->creatorId()) {
                $types   = CustomField::$fieldTypes;
                $modules = CustomField::$modules;
                return view('customFields.edit', compact('customField', 'types', 'modules'));
            }
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
        return response()->json(['error' => __('Permission Denied.')], 401);
    }

    public function update(Request $request, CustomField $customField)
    {
        if (!\Auth::user()->can('edit constant custom field')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        if ($customField->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $customField->name = $request->name;
        if ($customField->type === 'select' && $request->has('options')) {
            $customField->options = $request->options;
        }
        $customField->save();

        return redirect()->route('custom-field.index')->with('success', __('Custom Field successfully updated!'));
    }

    public function destroy(CustomField $customField)
    {
        if (!\Auth::user()->can('delete constant custom field')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        if ($customField->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $customField->delete();

        return redirect()->route('custom-field.index')->with('success', __('Custom Field successfully deleted!'));
    }

    /**
     * BULK DELETE
     */
    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete constant custom field')) {
            return $request->expectsJson()
                ? response()->json(['message' => __('Permission Denied.')], 403)
                : redirect()->back()->with('error', __('Permission Denied.'));
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

        $deleted = CustomField::where('created_by', \Auth::user()->creatorId())
            ->whereIn('id', $ids)
            ->delete();

        $message = $deleted
            ? trans_choice(':count field deleted.|:count fields deleted.', $deleted, ['count' => $deleted])
            : __('No changes made.');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'deleted' => $deleted]);
        }

        return redirect()->back()->with($deleted ? 'success' : 'error', $message);
    }

    /**
     * EXPORT ALL
     */
    public function export()
    {
        if (!\Auth::user()->can('manage constant custom field')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $company = \Auth::user()->name ?? 'Company';
        $company = preg_replace('/[^A-Za-z0-9\-_]/', '_', $company);
        $file = "custom_fields_{$company}_" . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new CustomFieldExport(), $file);
    }

    /**
     * EXPORT SELECTED
     */
    public function exportSelected(Request $request)
    {
        if (!\Auth::user()->can('manage constant custom field')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
        $file = "custom_fields_selected_{$company}_" . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new CustomFieldExport($ids), $file);
    }
}
