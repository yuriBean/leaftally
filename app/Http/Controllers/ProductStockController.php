<?php

namespace App\Http\Controllers;

use App\Models\ProductService;
use App\Models\ProductStock;
use App\Models\Utility;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CurrentStockExport;
use Illuminate\Support\Facades\Auth;

class ProductStockController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage product & service'))
        {
            $productServices = ProductService::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=','Product')->get();
            return view('productstock.index', compact('productServices'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
    }

    public function store(Request $request)
    {

    }

    public function show(ProductStock $productStock)
    {
    }

    public function edit($id)
    {
        $productService = ProductService::find($id);
        if (\Auth::user()->can('edit product & service'))
        {
            if ($productService->created_by == \Auth::user()->creatorId())
            {
                return view('productstock.edit', compact( 'productService'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit product & service'))
        {
            $productService = ProductService::find($id);
            if($request->quantity_type == 'Add')
            {
                $total=$productService->quantity + $request->quantity;

            }
            else {
                $total=$productService->quantity - $request->quantity;
            }

            if ($productService->created_by == \Auth::user()->creatorId())
            {
                $productService->quantity        = $total;
                $productService->created_by     = \Auth::user()->creatorId();
                $productService->save();

                $type='manually';
                $type_id = 0;
                $description=$request->quantity.'  '.__('quantity added by manually');
                Utility::addProductStock( $productService->id,$request->quantity,$type,$description,$type_id);

                return redirect()->route('productstock.index')->with('success', __('Product quantity updated manually.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(ProductStock $productStock)
    {
    }

    public function exportSelected(Request $request)
{
    if (!Auth::user()->can('manage product & service')) {
        return back()->with('error', __('Permission denied.'));
    }

    $ids = array_filter((array) $request->input('ids', []));
    if (empty($ids)) {
        return back()->with('error', __('No products selected.'));
    }

    $companyName = Auth::user()->name ?? 'Company';
    $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
    $date = date('Y-m-d_H-i-s');
    $filename = "stock_selected_{$companyName}_{$date}.xlsx";

    return Excel::download(new CurrentStockExport($ids), $filename);
}
}

