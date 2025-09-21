<?php

namespace App\Http\Controllers;

use App\Exports\GoalExport;
use App\Models\Goal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GoalController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage goal')) {
            $golas = Goal::where('created_by', \Auth::user()->creatorId())->get();
            return view('goal.index', compact('golas'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        if (\Auth::user()->can('create goal')) {
            $types = Goal::$goalType;
            return view('goal.create', compact('types'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create goal')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name'   => 'required',
            'type'   => 'required',
            'from'   => 'required',
            'to'     => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $goal             = new Goal();
        $goal->name       = $request->name;
        $goal->type       = $request->type;
        $goal->from       = $request->from;
        $goal->to         = $request->to;
        $goal->amount     = $request->amount;
        $goal->is_display = $request->has('is_display') ? 1 : 0;
        $goal->created_by = \Auth::user()->creatorId();
        $goal->save();

        return redirect()->route('goal.index')->with('success', __('Goal successfully created.'));
    }

    public function show(Goal $goal)
    {
        //
    }

    public function edit(Goal $goal)
    {
        if (!\Auth::user()->can('create goal')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $types = Goal::$goalType;
        return view('goal.edit', compact('types', 'goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        if (!\Auth::user()->can('edit goal')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($goal->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name'   => 'required',
            'type'   => 'required',
            'from'   => 'required',
            'to'     => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $goal->name       = $request->name;
        $goal->type       = $request->type;
        $goal->from       = $request->from;
        $goal->to         = $request->to;
        $goal->amount     = $request->amount;
        $goal->is_display = $request->has('is_display') ? 1 : 0;
        $goal->save();

        return redirect()->route('goal.index')->with('success', __('Goal successfully updated.'));
    }

    public function destroy(Goal $goal)
    {
        if (!\Auth::user()->can('delete goal')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($goal->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $goal->delete();
        return redirect()->route('goal.index')->with('success', __('Goal successfully deleted.'));
    }

    /**
     * BULK DELETE
     */
    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete goal')) {
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

        $query = Goal::where('created_by', \Auth::user()->creatorId())->whereIn('id', $ids);
        $count = (clone $query)->count();
        $query->delete();

        $msg = trans_choice(':count goal deleted.|:count goals deleted.', $count, ['count' => $count]);

        return $request->expectsJson()
            ? response()->json(['message' => $msg, 'deleted' => $count])
            : redirect()->back()->with('success', $msg);
    }

    /**
     * EXPORT ALL
     */
    public function export()
    {
        if (!\Auth::user()->can('manage goal')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $dateString  = date('Y-m-d_H-i-s');
        $filename    = "goals_{$companyName}_{$dateString}.xlsx";

        return Excel::download(new GoalExport(), $filename);
    }

    /**
     * EXPORT SELECTED
     */
    public function exportSelected(Request $request)
    {
        if (!\Auth::user()->can('manage goal')) {
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

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $dateString  = date('Y-m-d_H-i-s');
        $filename    = "goals_selected_{$companyName}_{$dateString}.xlsx";

        return Excel::download(new GoalExport($ids), $filename);
    }
}
