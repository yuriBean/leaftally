<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PlanController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage plan')) {
            $plans = (\Auth::user()->type == 'super admin')
                ? Plan::get()
                : Plan::where('is_disable', 1)->get();

            $admin_payment_setting = Utility::getAdminPaymentSetting();
            return view('plan.index', compact('plans', 'admin_payment_setting'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        if (!Auth::user()->can('create plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $arrDuration = [
            'lifetime' => __('Lifetime'),
            'month'    => __('Per Month'),
            'year'     => __('Per Year'),
        ];

        $systemFeatures = [
            'manage product & service' => __('Product & Service Management'),
            'manage customer'          => __('Customer Management'),
            'manage vender'            => __('Vendor Management'),
            'manage user'              => __('User Management'),
            'manage role'              => __('Role & Permission Management'),
            'manage invoice'           => __('Invoice Management'),
            'manage bill'              => __('Bill Management'),
            'manage payment'           => __('Payment Management'),
            'manage revenue'           => __('Revenue Management'),
            'manage bank account'      => __('Bank Account Management'),
            'manage assets'            => __('Asset Management'),
            'manage transaction'       => __('Transaction Management'),
            'manage transfer'          => __('Transfer Management'),
            'manage journal entry'     => __('Journal Entry Management'),
            'manage chart of account'  => __('Chart of Accounts'),
            'income report'            => __('Income Reports'),
            'expense report'           => __('Expense Reports'),
            'income vs expense report' => __('Income vs Expense Reports'),
            'balance sheet report'     => __('Balance Sheet Reports'),
            'ledger report'            => __('Ledger Reports'),
            'trial balance report'     => __('Trial Balance Reports'),
            'tax report'               => __('Tax Reports'),
            'statement report'         => __('Statement Reports'),
            'stock report'             => __('Stock Reports'),
            'manage proposal'          => __('Proposal Management'),
            'manage contract'          => __('Contract Management'),
            'manage goal'              => __('Goal Management'),
            'manage credit note'       => __('Credit Note Management'),
            'manage debit note'        => __('Debit Note Management'),
            'manage retainer'          => __('Retainer Management'),
            'manage bom'               => __('Bill of Materials'),
            'manage production'        => __('Production Management'),
            'manage constant tax'      => __('Tax Configuration'),
            'manage constant unit'     => __('Unit Configuration'),
            'manage constant category' => __('Category Configuration'),
            'manage constant payment method' => __('Payment Method Configuration'),
            'manage constant custom field'   => __('Custom Field Configuration'),
            'manage company settings'  => __('Company Settings'),
            'manage system settings'   => __('System Settings'),
            'manage coupon'            => __('Coupon Management'),
            'manage order'             => __('Order Management'),
            'enable_chatgpt'           => __('AI Chat GPT Integration'),
        ];

        return view('plan.create', compact('arrDuration', 'systemFeatures'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validation = [
            'name'          => 'required|unique:plans',
            'price'         => 'required|numeric|min:0',
            'duration'      => 'required',
            'storage_limit' => 'required|numeric',

            'max_users'           => 'nullable|numeric',
            'invoice_quota'       => 'nullable|numeric',
            'payroll_quota'       => 'nullable|numeric',
            'product_quota'       => 'nullable|numeric',
            'expense_quota'       => 'nullable|numeric',
            'client_quota'        => 'nullable|numeric',
            'vendor_quota'        => 'nullable|numeric',
            'manufacturing_quota' => 'nullable|numeric',
        ];
        $request->validate($validation);

        $post = $request->all();

        $post['features'] = json_encode($request->input('features', []), JSON_UNESCAPED_UNICODE);

        $editableToggles = [
            'user_access_management',
            'payroll_enabled',
            'budgeting_enabled',
            'tax_management_enabled',
            'audit_trail_enabled',
            'manufacturing_enabled',
        ];
        foreach ($editableToggles as $key) {
            $post[$key] = $request->has($key) ? 1 : 0;
        }

        $post['invoice_enabled']            = 1;
        $post['product_management_enabled'] = 1;
        $post['expense_tracking_enabled']   = 1;
        $post['client_management_enabled']  = 1;
        $post['vendor_management_enabled']  = 1;
        $post['inventory_enabled']          = 1;

        $quotas = [
            'max_users',
            'invoice_quota',
            'payroll_quota',
            'product_quota',
            'expense_quota',
            'client_quota',
            'vendor_quota',
            'manufacturing_quota',
        ];
        foreach ($quotas as $q) {
            $post[$q] = $request->filled($q) ? (int) $request->input($q) : -1;
        }

        $post['max_venders']   = (int) $post['vendor_quota'];
        $post['max_customers'] = (int) $post['client_quota'];
        $post['max_employees'] = (int) $post['payroll_quota'];

        if ($request->has('trial')) {
            $post['trial'] = 1;
            $post['trial_days'] = $request->input('trial_days');
        } else {
            $post['trial'] = 0;
            $post['trial_days'] = null;
        }

        $post['enable_chatgpt'] = $request->has('enable_chatgpt') ? 'on' : 'off';

        if ($request->hasFile('image')) {
            $extension       = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = 'plan_' . time() . '.' . $extension;
            $dir = storage_path('uploads/plan/');
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $post['image'] = $fileNameToStore;
            $request->file('image')->move($dir, $fileNameToStore);
        }

        try {
            Plan::create($post);
            Artisan::call('optimize:clear');
            return redirect()->back()->with('success', __('Plan successfully created.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to create plan: ') . $e->getMessage());
        }
    }

    public function edit($plan_id)
    {
        if (!Auth::user()->can('edit plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $arrDuration = [
            'lifetime' => __('Lifetime'),
            'month'    => __('Per Month'),
            'year'     => __('Per Year'),
        ];

        $systemFeatures = [
            'manage product & service' => __('Product & Service Management'),
            'manage customer'          => __('Customer Management'),
            'manage vender'            => __('Vendor Management'),
            'manage user'              => __('User Management'),
            'manage role'              => __('Role & Permission Management'),
            'manage invoice'           => __('Invoice Management'),
            'manage bill'              => __('Bill Management'),
            'manage payment'           => __('Payment Management'),
            'manage revenue'           => __('Revenue Management'),
            'manage bank account'      => __('Bank Account Management'),
            'manage assets'            => __('Asset Management'),
            'manage transaction'       => __('Transaction Management'),
            'manage transfer'          => __('Transfer Management'),
            'manage journal entry'     => __('Journal Entry Management'),
            'manage chart of account'  => __('Chart of Accounts'),
            'income report'            => __('Income Reports'),
            'expense report'           => __('Expense Reports'),
            'income vs expense report' => __('Income vs Expense Reports'),
            'balance sheet report'     => __('Balance Sheet Reports'),
            'ledger report'            => __('Ledger Reports'),
            'trial balance report'     => __('Trial Balance Reports'),
            'tax report'               => __('Tax Reports'),
            'statement report'         => __('Statement Reports'),
            'stock report'             => __('Stock Reports'),
            'manage proposal'          => __('Proposal Management'),
            'manage contract'          => __('Contract Management'),
            'manage goal'              => __('Goal Management'),
            'manage credit note'       => __('Credit Note Management'),
            'manage debit note'        => __('Debit Note Management'),
            'manage retainer'          => __('Retainer Management'),
            'manage bom'               => __('Bill of Materials'),
            'manage production'        => __('Production Management'),
            'manage constant tax'      => __('Tax Configuration'),
            'manage constant unit'     => __('Unit Configuration'),
            'manage constant category' => __('Category Configuration'),
            'manage constant payment method' => __('Payment Method Configuration'),
            'manage constant custom field'   => __('Custom Field Configuration'),
            'manage company settings'  => __('Company Settings'),
            'manage system settings'   => __('System Settings'),
            'manage coupon'            => __('Coupon Management'),
            'manage order'             => __('Order Management'),
            'enable_chatgpt'           => __('AI Chat GPT Integration'),
        ];

        $plan = Plan::find($plan_id);

        return view('plan.edit', compact('plan', 'arrDuration', 'systemFeatures'));
    }

  public function update(Request $request, $plan_id)
{
    if (!Auth::user()->can('edit plan')) {
        return back()->with('error', __('Permission denied.'));
    }

    $plan = Plan::find($plan_id);
    if (!$plan) {
        return back()->with('error', __('Plan not found.'));
    }

    $request->validate([
        'name'          => 'required|unique:plans,name,' . $plan_id,
        'price'         => 'nullable|numeric|min:0',
        'storage_limit' => 'required|numeric',
        'max_users'            => 'nullable|numeric',
        'invoice_quota'        => 'nullable|numeric',
        'payroll_quota'        => 'nullable|numeric',
        'product_quota'        => 'nullable|numeric',
        'expense_quota'        => 'nullable|numeric',
        'client_quota'         => 'nullable|numeric',
        'vendor_quota'         => 'nullable|numeric',
        'manufacturing_quota'  => 'nullable|numeric',
    ]);

    $b = fn($key) => $request->has($key) ? 1 : 0;
    $q = fn($key) => $request->filled($key) ? (int)$request->input($key) : -1;

    $plan->name          = $request->input('name');
    if ($request->filled('price')) {
        $plan->price     = (float) $request->input('price');
    }
    if ($request->filled('description') || $request->has('description')) {
        $plan->description = $request->input('description');
    }
    if ($request->filled('duration')) {
        $plan->duration  = $request->input('duration');
    }
    $plan->storage_limit = (int) $request->input('storage_limit');

    $plan->user_access_management = $b('user_access_management');
    $plan->payroll_enabled        = $b('payroll_enabled');
    $plan->budgeting_enabled      = $b('budgeting_enabled');
    $plan->tax_management_enabled = $b('tax_management_enabled');
    $plan->audit_trail_enabled    = $b('audit_trail_enabled');
    $plan->manufacturing_enabled  = $b('manufacturing_enabled');

    $plan->invoice_enabled            = 1;
    $plan->product_management_enabled = 1;
    $plan->expense_tracking_enabled   = 1;
    $plan->client_management_enabled  = 1;
    $plan->vendor_management_enabled  = 1;
    $plan->inventory_enabled          = 1;

    $plan->max_users            = $q('max_users');
    $plan->invoice_quota        = $q('invoice_quota');
    $plan->payroll_quota        = $q('payroll_quota');
    $plan->product_quota        = $q('product_quota');
    $plan->expense_quota        = $q('expense_quota');
    $plan->client_quota         = $q('client_quota');
    $plan->vendor_quota         = $q('vendor_quota');
    $plan->manufacturing_quota  = $q('manufacturing_quota');

    $plan->max_customers = (int) $plan->client_quota;
    $plan->max_venders   = (int) $plan->vendor_quota;
    $plan->max_employees = (int) $plan->payroll_quota;

    $plan->features = json_encode($request->input('features', []), JSON_UNESCAPED_UNICODE);

    if ($request->has('enable_chatgpt')) {
        $plan->enable_chatgpt = 'on';
    } else {
        $plan->enable_chatgpt = 'off';
    }
    if ($request->has('trial')) {
        $plan->trial = 1;
        $plan->trial_days = $request->input('trial_days');
    } else {
        $plan->trial = 0;
        $plan->trial_days = null;
    }

    if ($request->hasFile('image')) {
        $extension       = $request->file('image')->getClientOriginalExtension();
        $fileNameToStore = 'plan_' . time() . '.' . $extension;
        $dir = storage_path('uploads/plan/');
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        if (!empty($plan->image)) {
            $old = $dir . '/' . $plan->image;
            if (\Illuminate\Support\Facades\File::exists($old)) {
                @chmod($old, 0755);
                @\Illuminate\Support\Facades\File::delete($old);
            }
        }
        $request->file('image')->move($dir, $fileNameToStore);
        $plan->image = $fileNameToStore;
    }

    if ($plan->save()) {
        \Artisan::call('optimize:clear');
        return back()->with('success', __('Plan successfully updated.'));
    }

    return back()->with('error', __('Something is wrong.'));
}

    public function userPlan(Request $request)
    {
        $objUser = Auth::user();
        $planID  = Crypt::decrypt($request->code);
        $plan    = Plan::find($planID);
        if ($plan) {
            return redirect()->back()->with('error', __('Free plans are not available. Please purchase a plan.'));
        }
        return redirect()->back()->with('error', __('Plan not found.'));
    }

    public function planTrial($plan)
    {
        $objUser = \Auth::user();
        $planID  = Crypt::decrypt($plan);
        $plan    = Plan::find($planID);

        if (!$plan) {
            return redirect()->back()->with('error', __('Plan not found.'));
        }

        if ($plan->price > 0) {
            $user = User::find($objUser->id);
            $user->trial_plan = $planID;
            $currentDate = date('Y-m-d');
            $numberOfDaysToAdd = $plan->trial_days;
            $newDate = date('Y-m-d', strtotime($currentDate . ' + ' . $numberOfDaysToAdd . ' days'));
            $user->trial_expire_date = $newDate;
            $user->save();

            $objUser->assignPlan($planID);

            return redirect()->route('plans.index')->with('success', __('Plan successfully activated.'));
        }
        return redirect()->back()->with('error', __('Something is wrong.'));
    }

    public function destroy(Request $request, $id)
    {
        $userPlan = User::where('plan', $id)->first();
        if ($userPlan != null) {
            return redirect()->back()->with('error', __('The company has subscribed to this plan, so it cannot be deleted.'));
        }
        $plan = Plan::find($id);
        if ($plan && $plan->id == $id) {
            $plan->delete();
            return redirect()->back()->with('success', __('Plan deleted successfully'));
        }
        return redirect()->back()->with('error', __('Something went wrong'));
    }

    public function planDisable(Request $request)
    {
        $userPlan = User::where('plan', $request->id)->first();
        if ($userPlan != null) {
            return response()->json(['error' => __('The company has subscribed to this plan, so it cannot be disabled.')]);
        }

        Plan::where('id', $request->id)->update(['is_disable' => $request->is_disable]);

        if ($request->is_disable == 1) {
            return response()->json(['success' => __('Plan successfully enable.')]);
        }
        return response()->json(['success' => __('Plan successfully disable.')]);
    }
}
