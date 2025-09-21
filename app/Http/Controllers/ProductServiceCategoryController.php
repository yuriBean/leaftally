<?php

namespace App\Http\Controllers;

use App\Exports\ProductCategoryExport;
use App\Models\Bill;
use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductServiceCategoryController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage constant category')) {
            $categories = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->get();
            return view('productServiceCategory.index', compact('categories'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        if (\Auth::user()->can('create constant category')) {
            $types = ProductServiceCategory::$catTypes;

            $type = ['' => 'Select Category Type'];
            $types = array_merge($type, $types);

            $chart_accounts = ChartOfAccount::select(\DB::raw('CONCAT(code, " - ", name) AS code_name, id'))
                ->where('created_by', \Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
            $chart_accounts->prepend('Select Account', '');

            return view('productServiceCategory.create', compact('types'));
        }
        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create constant category')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name'  => 'required|max:20',
            'type'  => 'required',
            'color' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $category                   = new ProductServiceCategory();
        $category->name             = $request->name;
        $category->color            = $request->color;
        $category->type             = $request->type;
        $category->chart_account_id = !empty($request->chart_account) ? $request->chart_account : 0;
        $category->created_by       = \Auth::user()->creatorId();
        $category->save();

        return redirect()->route('product-category.index')->with('success', __('Category successfully created.'));
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit constant category')) {
            $types    = ProductServiceCategory::$catTypes;
            $category = ProductServiceCategory::find($id);

            return view('productServiceCategory.edit', compact('category', 'types'));
        }
        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit constant category')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $category = ProductServiceCategory::find($id);
        if ($category->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name'  => 'required|max:20',
            'type'  => 'required',
            'color' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $category->name  = $request->name;
        $category->color = $request->color;
        $category->type  = $request->type;
        $category->save();

        return redirect()->route('product-category.index')->with('success', __('Category successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete constant category')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $category = ProductServiceCategory::find($id);
        if ($category->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        // prevent delete if in use
        if ($category->type == 0) {
            $inUse = ProductService::where('category_id', $category->id)->first();
        } elseif ($category->type == 1) {
            $inUse = Invoice::where('category_id', $category->id)->first();
        } else {
            $inUse = Bill::where('category_id', $category->id)->first();
        }

        if (!empty($inUse)) {
            return redirect()->back()->with('error', __('this category is already assign so please move or remove this category related data.'));
        }

        $category->delete();

        return redirect()->route('product-category.index')->with('success', __('Category successfully deleted.'));
    }

    /**
     * BULK DELETE
     */
    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete constant category')) {
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

        $categories = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
            ->whereIn('id', $ids)
            ->get();

        foreach ($categories as $category) {
            // dependency check identical to single delete
            if ($category->type == 0) {
                $inUse = ProductService::where('category_id', $category->id)->exists();
            } elseif ($category->type == 1) {
                $inUse = Invoice::where('category_id', $category->id)->exists();
            } else {
                $inUse = Bill::where('category_id', $category->id)->exists();
            }

            if ($inUse) {
                $blocked[] = ['id' => $category->id, 'name' => $category->name];
                continue;
            }

            $category->delete();
            $deleted++;
        }

        $msgParts = [];
        if ($deleted > 0) {
            $msgParts[] = trans_choice(':count category deleted.|:count categories deleted.', $deleted, ['count' => $deleted]);
        }
        if (count($blocked) > 0) {
            $msgParts[] = __('Some categories could not be deleted because they are in use.');
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
        if (!\Auth::user()->can('manage constant category')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $dateString  = date('Y-m-d_H-i-s');
        $filename    = "categories_{$companyName}_{$dateString}.xlsx";

        return Excel::download(new ProductCategoryExport(), $filename);
    }

    /**
     * EXPORT SELECTED
     */
    public function exportSelected(Request $request)
    {
        if (!\Auth::user()->can('manage constant category')) {
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
        $filename    = "categories_selected_{$companyName}_{$dateString}.xlsx";

        return Excel::download(new ProductCategoryExport($ids), $filename);
    }

    // ---- existing helper endpoints below ----

    public function getProductCategories()
    {
        $cat = ProductServiceCategory::getallCategories();
        $all_products = ProductService::getallproducts()->count();

        $html = '<div class="mb-3 mr-2 zoom-in ">
                  <div class="card rounded-10 card-stats mb-0 cat-active overflow-hidden" data-id="0">
                     <div class="category-select" data-cat-id="0">
                        <button type="button" class="btn tab-btns btn-primary">' . __("All Categories") . '</button>
                     </div>
                  </div>
               </div>';
        foreach ($cat as $key => $c) {
            $dcls = 'category-select';
            $html .= ' <div class="mb-3 mr-2 zoom-in cat-list-btn">
                          <div class="card rounded-10 card-stats mb-0 overflow-hidden " data-id="' . $c->id . '">
                             <div class="' . $dcls . '" data-cat-id="' . $c->id . '">
                                <button type="button" class="btn tab-btns btn-primary">' . $c->name . '</button>
                             </div>
                          </div>
                       </div>';
        }
        return Response($html);
    }

    public function getAccount(Request $request)
    {
        $chart_accounts = [];
        if ($request->type == 'income') {
            $chart_accounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Income')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } elseif ($request->type == 'expense') {
            $chart_accounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Expenses')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } elseif ($request->type == 'asset') {
            $chart_accounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Assets')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } elseif ($request->type == 'liability') {
            $chart_accounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Liabilities')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } elseif ($request->type == 'equity') {
            $chart_accounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Equity')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } elseif ($request->type == 'costs of good sold') {
            $chart_accounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Costs of Goods Sold')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } else {
            $chart_accounts = 0;
        }

        $subAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account');
        $subAccounts->leftjoin('chart_of_account_parents', 'chart_of_accounts.parent', 'chart_of_account_parents.id');
        $subAccounts->where('chart_of_accounts.parent', '!=', 0);
        $subAccounts->where('chart_of_accounts.created_by', \Auth::user()->creatorId());
        $subAccounts = $subAccounts->get()->toArray();

        $response = [
            'chart_accounts' => $chart_accounts,
            'sub_accounts'   => $subAccounts,
        ];

        return response()->json($response);
    }
    
 public function short(Request $request)
    {
        if(\Auth::user()->can('create constant category'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:255',
                                   'color' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $category             = new ProductServiceCategory();
            $category->name       = $request->name;
            $category->color      = $request->color;
            $category->type       = $request->type;
            $category->chart_account_id = !empty($request->chart_account) ? $request->chart_account : 0;
            $category->created_by = \Auth::user()->creatorId();
            $category->save();

            $all_category     = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $options = '';
        foreach ($all_category as $id => $name) {
    $options .= '<option value="' . $id . '" ' . ($id == $category->id ? 'selected' : '') . '>' . $name . '</option>';
}

            return response()->json([
            'status' => 1,
            'message' => __('New Category Added.'),
            'options' => $options,
        ], 200);
        }
        else
        {
            return response()->json([
            'status' => 0,
            'message' => __('Permission denied.'),
        ], 403);
        }
    }
}
