<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\ProductServiceUnit;
use App\Models\Tax;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductServiceExport;
use App\Imports\ProductServiceImport;
use App\Models\ChartOfAccount;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProductServiceController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('manage product & service')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $categories = ProductServiceCategory::where('created_by', Auth::user()->creatorId())
            ->where('type', 0)
            ->pluck('name', 'id')
            ->prepend(__('Select Category'), '');

        $materialTypes = [
            ''         => __('All Materials'),
            'raw'      => __('Raw material'),
            'finished' => __('Finished product'),
            'both'     => __('Both'),
        ];

        $query = ProductService::with(['unit', 'category', 'taxes', 'createdBy'])
            ->where('created_by', Auth::user()->creatorId())
            ->orderBy('created_at', 'desc');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('material_type')) {
            $query->where('material_type', $request->material_type);
        }

        $productServices = $query->get();

        return view('productservice.index', compact('productServices', 'categories', 'materialTypes'));
    }

    public function create()
    {
        if (\Auth::user()->can('create product & service')) {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'product')->get();
            $category     = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'product & service')->get()->pluck('name', 'id');
            $unit         = ProductServiceUnit::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $tax          = Tax::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $incomeChartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name'), 'chart_of_accounts.id')
                ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'income')
                ->where('chart_of_accounts.parent', '=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->get()
                ->pluck('code_name', 'id')
                ->prepend('Select Account', null);

            $incomeSubAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account')
                ->leftJoin('chart_of_account_parents', 'chart_of_accounts.parent', '=', 'chart_of_account_parents.id')
                ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'income')
                ->where('chart_of_accounts.parent', '!=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->get()
                ->toArray();

            $expenseChartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name'), 'chart_of_accounts.id')
                ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                ->whereIn('chart_of_account_types.name', ['Expenses', 'Costs of Goods Sold'])
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->get()
                ->pluck('code_name', 'id')
                ->prepend('Select Account', null);

            $expenseSubAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account')
                ->leftJoin('chart_of_account_parents', 'chart_of_accounts.parent', '=', 'chart_of_account_parents.id')
                ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                ->whereIn('chart_of_account_types.name', ['Expenses', 'Costs of Goods Sold'])
                ->where('chart_of_accounts.parent', '!=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->get()
                ->toArray();

            return view('productservice.create', compact('category', 'unit', 'tax', 'customFields', 'incomeChartAccounts', 'incomeSubAccounts', 'expenseChartAccounts', 'expenseSubAccounts'));
        }

        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function product_short(Request $request)
    {
        $response = $this->store($request, "Yes");
        return $response;
    }
    public function show($id)
{
    if (!\Auth::user()->can('manage product & service')) {
        return response()->json(['error' => __('Permission denied.')], 401);
    }

    $productService = ProductService::with(['unit','category'])
        ->where('created_by', \Auth::user()->creatorId())
        ->findOrFail($id);

    return view('productservice.show', [
        'productService' => $productService,
    ]);
}

    public function createShort()
    {
        if (\Auth::user()->can('create product & service')) {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'product')->get();
            $category     = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'product & service')->get()->pluck('name', 'id');
            $unit         = ProductServiceUnit::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $tax          = Tax::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $incomeChartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name'), 'chart_of_accounts.id')
                ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'income')
                ->where('chart_of_accounts.parent', '=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->get()
                ->pluck('code_name', 'id')
                ->prepend('Select Account', null);

            $incomeSubAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account')
                ->leftJoin('chart_of_account_parents', 'chart_of_accounts.parent', '=', 'chart_of_account_parents.id')
                ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'income')
                ->where('chart_of_accounts.parent', '!=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->get()
                ->toArray();

            $expenseChartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name'), 'chart_of_accounts.id')
                ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                ->whereIn('chart_of_account_types.name', ['Expenses', 'Costs of Goods Sold'])
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->get()
                ->pluck('code_name', 'id')
                ->prepend('Select Account', null);

            $expenseSubAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account')
                ->leftJoin('chart_of_account_parents', 'chart_of_accounts.parent', '=', 'chart_of_account_parents.id')
                ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                ->whereIn('chart_of_account_types.name', ['Expenses', 'Costs of Goods Sold'])
                ->where('chart_of_accounts.parent', '!=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->get()
                ->toArray();

            return view('productservice.create-short', compact('category', 'unit', 'tax', 'customFields', 'incomeChartAccounts', 'incomeSubAccounts', 'expenseChartAccounts', 'expenseSubAccounts'));
        }

        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function store(Request $request, string $short = 'No')
    {
        if (!\Auth::user()->can('create product & service')) {
            if ($short === 'Yes') {
                return response()->json([
                    'status'  => 0,
                    'message' => __('Permission denied.'),
                ], 403);
            }
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        [$ok, $msg] = $this->withinQouta();
        if (!$ok) {
            if ($short === 'Yes') {
                return response()->json([
                    'status'  => 0,
                    'message' => $msg,
                ], 200);
            } else {
                return redirect()->back()->with('error', $msg);
            }
        }

        $rules = [
            'name'           => 'required',
            'sku'            => 'required',
            'sale_price'     => 'required|numeric',
            'purchase_price' => 'required|numeric',
            'category_id'    => 'required',
            'type'           => 'required|in:Product,Service',
            'material_type'  => 'nullable|in:raw,finished,both',
            'reorder_level'  => 'nullable|integer|min:0',
        ];
        if (strtolower($request->type) === 'product') {
            $rules['material_type'] = 'required|in:raw,finished,both';
        }

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            if ($short === 'Yes') {
                return response()->json([
                    'status'  => 0,
                    'message' => $messages->first(),
                ], 200);
            } else {
                return redirect()->back()->with('error', $messages->first());
            }
        }

        $productService                          = new ProductService();
        $productService->name                    = $request->name;
        $productService->description             = $request->description;
        $productService->sku                     = $request->sku;
        $productService->sale_price              = $request->sale_price;
        $productService->purchase_price          = $request->purchase_price;
        $productService->tax_id                  = is_array($request->tax_id) ? implode(',', $request->tax_id) : (string) ($request->tax_id ?? '');
        $productService->unit_id                 = $request->unit_id ?? 0;
        $productService->quantity                = $request->quantity ?? 0;
        $productService->type                    = $request->type;
        $productService->material_type           = (strtolower($request->type) === 'service') ? null : $request->material_type;
        $productService->sale_chartaccount_id    = $request->sale_chartaccount_id;
        $productService->expense_chartaccount_id = $request->expense_chartaccount_id;
        $productService->category_id             = $request->category_id;
        $productService->reorder_level           = $request->reorder_level ?? null;
        $productService->created_by              = \Auth::user()->creatorId();
        $productService->save();

        CustomField::saveData($productService, $request->customField);

        if ($short === 'Yes') {
            return response()->json([
                'status'  => 1,
                'message' => __('Product successfully created.'),
                'id'      => $productService->id,
                'name'    => $productService->name,
            ], 200);
        } else {
            return redirect()->route('productservice.index')->with('success', __('Product successfully created.'));
        }
    }

    public function edit($id)
    {
        $productService = ProductService::find($id);

        if (\Auth::user()->can('edit product & service')) {
            if ($productService && $productService->created_by == \Auth::user()->creatorId()) {
                $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                    ->where('type', 'product & service')
                    ->orderBy('name')
                    ->pluck('name', 'id');

                $unit = ProductServiceUnit::where('created_by', \Auth::user()->creatorId())
                    ->orderBy('name')
                    ->pluck('name', 'id');

                $tax = Tax::where('created_by', \Auth::user()->creatorId())
                    ->orderBy('name')
                    ->pluck('name', 'id');

                if (!empty($productService->category_id)) {
                    $trashedCat = ProductServiceCategory::onlyTrashed()
                        ->where('created_by', \Auth::user()->creatorId())
                        ->find($productService->category_id);
                    if ($trashedCat) {
                        $category = collect([$trashedCat->id => $trashedCat->name . ' ' . __('(deleted)')])->union($category);
                    }
                }

                if (!empty($productService->unit_id)) {
                    $trashedUnit = ProductServiceUnit::onlyTrashed()
                        ->where('created_by', \Auth::user()->creatorId())
                        ->find($productService->unit_id);
                    if ($trashedUnit) {
                        $unit = collect([$trashedUnit->id => $trashedUnit->name . ' ' . __('(deleted)')])->union($unit);
                    }
                }

                $selectedTaxIds = collect(explode(',', (string) $productService->tax_id))
                    ->filter(fn($v) => is_numeric($v))
                    ->map(fn($v) => (int)$v)
                    ->unique();

                if ($selectedTaxIds->isNotEmpty()) {
                    $trashedTaxes = Tax::onlyTrashed()
                        ->where('created_by', \Auth::user()->creatorId())
                        ->whereIn('id', $selectedTaxIds)
                        ->get(['id', 'name']);
                    if ($trashedTaxes->isNotEmpty()) {
                        $tax = $trashedTaxes->mapWithKeys(fn($t) => [$t->id => $t->name . ' ' . __('(deleted)')])->union($tax);
                    }
                }

                $productService->customField = CustomField::getData($productService, 'product');
                $customFields                = CustomField::where('created_by', \Auth::user()->creatorId())->where('module', 'product')->get();
                $productService->tax_id      = explode(',', (string) $productService->tax_id);

                $incomeChartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name'), 'chart_of_accounts.id')
                    ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                    ->where('chart_of_account_types.name', 'income')
                    ->where('chart_of_accounts.parent', 0)
                    ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                    ->orderBy('chart_of_accounts.code')
                    ->pluck('code_name', 'id')
                    ->prepend('Select Account', 0);

                $incomeSubAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account')
                    ->leftJoin('chart_of_account_parents', 'chart_of_accounts.parent', '=', 'chart_of_account_parents.id')
                    ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                    ->where('chart_of_account_types.name', 'income')
                    ->where('chart_of_accounts.parent', '!=', 0)
                    ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                    ->get()
                    ->toArray();

                $expenseChartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name'), 'chart_of_accounts.id')
                    ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                    ->whereIn('chart_of_account_types.name', ['Expenses', 'Costs of Goods Sold'])
                    ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                    ->orderBy('chart_of_accounts.code')
                    ->pluck('code_name', 'id')
                    ->prepend('Select Account', '');

                $expenseSubAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account')
                    ->leftJoin('chart_of_account_parents', 'chart_of_accounts.parent', '=', 'chart_of_account_parents.id')
                    ->leftJoin('chart_of_account_types', 'chart_of_account_types.id', '=', 'chart_of_accounts.type')
                    ->whereIn('chart_of_account_types.name', ['Expenses', 'Costs of Goods Sold'])
                    ->where('chart_of_accounts.parent', '!=', 0)
                    ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                    ->get()
                    ->toArray();

                return view('productservice.edit', compact(
                    'category',
                    'unit',
                    'tax',
                    'productService',
                    'customFields',
                    'incomeChartAccounts',
                    'incomeSubAccounts',
                    'expenseChartAccounts',
                    'expenseSubAccounts'
                ));
            }

            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit product & service')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $productService = ProductService::find($id);
        if (!$productService || $productService->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $rules = [
            'name'           => 'required',
            'sku'            => 'required',
            'sale_price'     => 'required|numeric',
            'purchase_price' => 'required|numeric',
            'category_id'    => 'required',
            'type'           => 'required|in:Product,Service',
            'material_type'  => 'nullable|in:raw,finished,both',
            'reorder_level'  => 'nullable|integer|min:0',
        ];
        if (strtolower($request->type) === 'product') {
            $rules['material_type'] = 'required|in:raw,finished,both';
        }

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->route('productservice.index')->with('error', $messages->first());
        }

        $productService->name                     = $request->name;
        $productService->description              = $request->description;
        $productService->sku                      = $request->sku;
        $productService->sale_price               = $request->sale_price;
        $productService->purchase_price           = $request->purchase_price;
        $productService->tax_id                   = !empty($request->tax_id) ? implode(',', $request->tax_id) : '';
        $productService->unit_id                  = $request->unit_id ?? 0;
        $productService->quantity                 = $request->quantity;
        $productService->type                     = $request->type;
        $productService->material_type            = (strtolower($request->type) === 'service') ? null : $request->material_type;
        $productService->sale_chartaccount_id     = $request->sale_chartaccount_id;
        $productService->expense_chartaccount_id  = $request->expense_chartaccount_id;
        $productService->category_id              = $request->category_id;
        $productService->reorder_level = $request->reorder_level;
        $productService->created_by               = \Auth::user()->creatorId();
        $productService->save();

        CustomField::saveData($productService, $request->customField);

        return redirect()->route('productservice.index')->with('success', __('Product successfully updated.'));
    }

    public function export()
    {
        $name = 'product_service_' . date('Y-m-d i:h:s');
        return Excel::download(new ProductServiceExport(), $name . '.xlsx');
    }

    public function importFile()
    {
        return view('productservice.import');
    }

    public function import(Request $request)
    {
        $rules = [
            'file' => 'required|mimes:csv,txt,xls,xlsx',
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $products     = (new ProductServiceImport)->toArray(request()->file('file'))[0];
        $totalProduct = count($products) - 1;
        $errorArray   = [];

        for ($i = 1; $i <= count($products) - 1; $i++) {
            $items = $products[$i];

            $taxes     = explode(';', $items[5]);
            $taxesData = [];
            foreach ($taxes as $tax) {
                $t = Tax::where('id', $tax)->first();
                $taxesData[] = !empty($t->id) ? $t->id : 0;
            }
            $taxData = implode(',', $taxesData);

            $productService = new ProductService();
            $productService->name           = $items[0] ?? "";
            $productService->sku            = $items[1] ?? "";
            $productService->quantity       = $items[2] ?? "";
            $productService->sale_price     = $items[3] ?? "";
            $productService->purchase_price = $items[4] ?? "";
            $productService->tax_id         = $taxData;
            $productService->category_id    = $items[6] ?? "";
            $productService->unit_id        = $items[7] ?? "";
            $productService->type           = $items[8] ?? "";
            $productService->description    = $items[9] ?? "";
            $productService->created_by     = \Auth::user()->creatorId();

            if (empty($productService)) {
                $errorArray[] = $productService;
            } else {
                $productService->save();
            }
        }

        if (empty($errorArray)) {
            return redirect()->back()->with('success', __('Record successfully imported'));
        } else {
            $total = $totalProduct;
            $failed = count($errorArray);
            return redirect()->back()->with('error', $failed . ' ' . __('Record imported fail out of') . ' ' . $total . ' ' . __('record'));
        }
    }

    protected function withinQouta(): array
    {
        $creatorId = \Auth::user()->creatorId();
        $creator   = User::find($creatorId);
        $plan      = $creator ? Plan::find($creator->plan) : null;

        $max = $plan->product_quota ?? -1;

        if ((int)$max === -1) {
            return [true, null];
        }

        $current = ProductService::where('created_by', $creatorId)->count();
        if ($current >= (int)$max) {
            return [false, __('Your product limit is over, Please change plan.')];
        }

        return [true, null];
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete product & service')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $productService = ProductService::find($id);
        if (!$productService || $productService->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $productService->delete();

        return redirect()
            ->route('productservice.index')
            ->with('success', __('Product archived.'));
    }

    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete product & service')) {
            return redirect()->route('productservice.index')->with('error', __('Permission denied.'));
        }

        $ids = array_values(array_filter(array_map('intval', (array) $request->input('ids', []))));
        if (empty($ids)) {
            return redirect()->route('productservice.index')->with('error', __('No items selected.'));
        }

        $deleted = ProductService::query()
            ->mine()
            ->whereIn('id', $ids)
            ->delete();

        if ($deleted <= 0) {
            return redirect()->route('productservice.index')->with('error', __('Nothing was archived.'));
        }

        $msg = trans_choice(':count item archived.|:count items archived.', $deleted, ['count' => $deleted]);

        return redirect()->route('productservice.index')->with('success', $msg);
    }

    public function restore($id)
    {
        if (!\Auth::user()->can('edit product & service')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $product = ProductService::onlyTrashed()
            ->mine()
            ->findOrFail($id);

        $product->restore();

        return redirect()->route('productservice.index')->with('success', __('Product restored.'));
    }

    public function forceDestroy($id)
    {
        if (!\Auth::user()->can('delete product & service')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $product = ProductService::onlyTrashed()
            ->mine()
            ->findOrFail($id);
        $product->forceDelete();

        return redirect()->route('productservice.index')->with('success', __('Product permanently deleted.'));
    }

    public function exportSelected(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        if (empty($ids)) {
            return back()->with('error', __('No items selected.'));
        }

        if (!Auth::user()->can('manage product & service')) {
            return back()->with('error', __('Permission denied.'));
        }

        $companyName = Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $date = date('Y-m-d_H-i-s');
        $filename = "products_selected_{$companyName}_{$date}.xlsx";

        return Excel::download(new ProductServiceExport($ids), $filename);
    }
}
