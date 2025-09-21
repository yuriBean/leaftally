<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use App\Exports\BankExport;
use Maatwebsite\Excel\Facades\Excel;

class BankController extends Controller
{
  public function index()
  {
    if (!\Auth::user()->can('manage constant bank')) {
      return redirect()->back()->with('error', __('Permission denied.'));
    }

    $items = Bank::orderBy('name')->get();
    return view('constants.hr.banks.index', compact('items'));
  }

  public function create()
  {
    if (!\Auth::user()->can('create constant bank')) {
      return response()->json(['error' => __('Permission denied.')], 401);
    }

    $title = __('Add Bank');
    return view('constants.hr.banks.create', compact('title'));
  }

  public function store(Request $r)
  {
    if (!\Auth::user()->can('create constant bank')) {
      return redirect()->back()->with('error', __('Permission denied.'));
    }

    $d = $r->validate(['name'=>'required|string|max:120|unique:banks,name']);
    Bank::create($d);
    return back()->with('success', __('Bank created successfully.'));
  }

  public function edit(Bank $bank)
  {
    if (!\Auth::user()->can('edit constant bank')) {
      return response()->json(['error' => __('Permission denied.')], 401);
    }

    $title = __('Edit Bank');
    return view('constants.hr.banks.edit', compact('bank','title'));
  }

  public function update(Request $r, Bank $bank)
  {
    if (!\Auth::user()->can('edit constant bank')) {
      return redirect()->back()->with('error', __('Permission denied.'));
    }

    $d = $r->validate(['name'=>'required|string|max:120|unique:banks,name,'.$bank->id]);
    $bank->update($d);
    return back()->with('success', __('Bank updated successfully.'));
  }

  public function destroy(Bank $bank)
  {
    if (!\Auth::user()->can('delete constant bank')) {
      return redirect()->back()->with('error', __('Permission denied.'));
    }

    $bank->delete();
    return redirect()->route('banks.index')->with('success', __('Bank deleted.'));
  }

  /**
   * Bulk delete selected banks.
   */
  public function bulkDestroy(Request $request)
  {
    if (!\Auth::user()->can('delete constant bank')) {
      return $request->expectsJson()
        ? response()->json(['message' => __('Permission denied.')], 403)
        : redirect()->back()->with('error', __('Permission denied.'));
    }

    $ids = $request->input('ids', []);
    if (is_string($ids)) {
      $ids = array_filter(array_map('trim', explode(',', $ids)));
    }
    $ids = array_values(array_unique(array_map('intval', (array)$ids)));

    if (empty($ids)) {
      return $request->expectsJson()
        ? response()->json(['message' => __('No records selected.')], 422)
        : redirect()->back()->with('error', __('No records selected.'));
    }

    $deleted = Bank::whereIn('id', $ids)->delete();

    $message = $deleted
      ? trans_choice(':count bank deleted.|:count banks deleted.', $deleted, ['count' => $deleted])
      : __('No changes made.');

    if ($request->expectsJson()) {
      return response()->json(['message' => $message, 'deleted' => $deleted]);
    }

    return redirect()->back()->with($deleted ? 'success' : 'error', $message);
  }

  /**
   * Export all banks.
   */
  public function export()
  {
    if (!\Auth::user()->can('manage constant bank')) {
      return redirect()->back()->with('error', __('Permission denied.'));
    }

    $org = \Auth::user()->name ?? 'Org';
    $org = preg_replace('/[^A-Za-z0-9\-_]/', '_', $org);
    $file = "banks_{$org}_" . date('Y-m-d_H-i-s') . '.xlsx';

    return Excel::download(new BankExport(), $file);
  }

  /**
   * Export selected banks.
   */
  public function exportSelected(Request $request)
  {
    if (!\Auth::user()->can('manage constant bank')) {
      return redirect()->back()->with('error', __('Permission denied.'));
    }

    $ids = $request->input('ids', []);
    if (is_string($ids)) {
      $ids = array_filter(array_map('trim', explode(',', $ids)));
    }
    $ids = array_values(array_unique(array_map('intval', (array)$ids)));

    if (empty($ids)) {
      return redirect()->back()->with('error', __('No records selected.'));
    }

    $org = \Auth::user()->name ?? 'Org';
    $org = preg_replace('/[^A-Za-z0-9\-_]/', '_', $org);
    $file = "banks_selected_{$org}_" . date('Y-m-d_H-i-s') . '.xlsx';

    return Excel::download(new BankExport($ids), $file);
  }
}
