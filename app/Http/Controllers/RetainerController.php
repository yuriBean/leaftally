<?php

namespace App\Http\Controllers;

use App\Exports\RetainerExport;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceProduct;
use App\Models\Plan;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\Retainer;
use App\Models\RetainerPayment;
use App\Models\RetainerProduct;
use App\Models\Transaction;
use App\Models\CustomField;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\TrashedSelect;

class RetainerController extends Controller
{
    public function index(Request $request)
    {
        if (!\Auth::user()->can('manage retainer')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $customer = TrashedSelect::activeOptions(Customer::class, \Auth::user()->creatorId())
            ->prepend('Select Customer', '');

        $status = Retainer::$statues;

        $query = Retainer::where('created_by', \Auth::user()->creatorId());

        if (!empty($request->customer)) {
            $query->where('customer_id', $request->customer);
        }

        if (str_contains((string)$request->issue_date, ' to ')) {
            $date_range = explode(' to ', $request->issue_date);
            $query->whereBetween('issue_date', $date_range);
        } elseif (!empty($request->issue_date)) {
            $query->where('issue_date', $request->issue_date);
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        $retainers = $query->get();

        return view('retainer.index', compact('retainers', 'customer', 'status'));
    }

    public function create($customerId)
    {
        if (!\Auth::user()->can('create retainer')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $customFields    = CustomField::where('created_by', \Auth::user()->creatorId())->where('module', 'retainer')->get();
        $retainer_number = \Auth::user()->retainerNumberFormat($this->retainerNumber());

        $customers = TrashedSelect::activeOptions(Customer::class, \Auth::user()->creatorId())
            ->prepend('Select Customer', '');

        $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'income')
            ->get()->pluck('name', 'id');
        $category->prepend('Select Category', '');

        $product_services = TrashedSelect::activeOptions(ProductService::class, \Auth::user()->creatorId())
            ->prepend('--', '');

        return view('retainer.create', compact('customers', 'retainer_number', 'product_services', 'category', 'customFields', 'customerId'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create retainer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'customer_id' => 'required',
                'issue_date'  => 'required',
                'category_id' => 'required',
                'items'       => 'required',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $retainer                 = new Retainer();
        $retainer->retainer_id    = $this->retainerNumber();
        $retainer->customer_id    = $request->customer_id;
        $retainer->status         = 0;
        $retainer->issue_date     = $request->issue_date;
        $retainer->category_id    = $request->category_id;
        $retainer->discount_apply = isset($request->discount_apply) ? 1 : 0;
        $retainer->created_by     = \Auth::user()->creatorId();
        $retainer->save();

        Utility::starting_number($retainer->retainer_id + 1, 'retainer');
        CustomField::saveData($retainer, $request->customField);

        $products = $request->items;
        for ($i = 0; $i < count($products); $i++) {
            $RetainerProduct               = new RetainerProduct();
            $RetainerProduct->retainer_id  = $retainer->id;
            $RetainerProduct->product_id   = $products[$i]['item'];
            $RetainerProduct->quantity     = $products[$i]['quantity'];
            $RetainerProduct->tax          = $products[$i]['tax'] ?? 0;
            $RetainerProduct->discount     = $products[$i]['discount'];
            $RetainerProduct->price        = $products[$i]['price'];
            $RetainerProduct->description  = $products[$i]['description'];
            $RetainerProduct->save();
        }

        return redirect()->route('retainer.index')->with('success', __('Retainer successfully created.'));
    }

    public function show($ids)
    {
        if (!\Auth::user()->can('show retainer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $id       = Crypt::decrypt($ids);
        $retainer = Retainer::find($id);

        if (!$retainer) {
            return redirect()->back()->with('error', __('Retainer is empty.'));
        }

        $users = User::where('id', $retainer->created_by)->first();
        $plan  = Plan::find($users->plan);

        if ($retainer->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $customer = $retainer->customer;
        $iteams   = $retainer->items;
        $status   = Retainer::$statues;

        $retainer->customField = CustomField::getData($retainer, 'retainer');
        $customFields          = CustomField::where('created_by', \Auth::user()->creatorId())->where('module', 'retainer')->get();

        return view('retainer.view', compact('retainer', 'customer', 'iteams', 'status', 'customFields', 'users', 'plan'));
    }

    public function edit($ids)
    {
        if (!\Auth::user()->can('edit retainer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $id              = Crypt::decrypt($ids);
        $retainer        = Retainer::find($id);
        $retainer_number = \Auth::user()->retainerNumberFormat($retainer->retainer_id);

        $customers = TrashedSelect::optionsWithUsed(
            Customer::class,
            \Auth::user()->creatorId(),
            [$retainer->customer_id],
            'name'
        );

        $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'income')->get()->pluck('name', 'id');
        $category->prepend('Select Category', '');

        $usedIds = RetainerProduct::where('retainer_id', $retainer->id)
            ->pluck('product_id')->filter()->unique()->values()->all();

        $product_services = TrashedSelect::optionsWithUsed(
            ProductService::class,
            \Auth::user()->creatorId(),
            $usedIds
        );

        $retainer->customField = CustomField::getData($retainer, 'retainer');
        $customFields          = CustomField::where('created_by', \Auth::user()->creatorId())->where('module', 'retainer')->get();

        $items = [];
        foreach ($retainer->items as $retainerItem) {
            $itemAmount               = $retainerItem->quantity * $retainerItem->price;
            $retainerItem->itemAmount = $itemAmount;
            $retainerItem->taxes      = Utility::tax($retainerItem->tax) ?? 0;
            $items[]                  = $retainerItem;
        }

        return view('retainer.edit', compact('customers', 'product_services', 'retainer', 'retainer_number', 'category', 'customFields', 'items'));
    }

    function retainerNumber()
    {
        $latest = Utility::getValByName('retainer_starting_number');
        if (!$latest) {
            return 1;
        }
        return $latest;
    }

    public function product(Request $request)
    {
        $product = TrashedSelect::findWithTrashed(ProductService::class, $request->product_id);

        $data['product'] = $product;
        $data['unit']    = ($product && !empty($product->unit)) ? ($product->unit->name ?? 0) : 0;
        $data['taxRate'] = $taxRate = $product && !empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0;
        $data['taxes']   = $product && !empty($product->tax_id) ? $product->tax($product->tax_id) : 0;

        $salePrice           = $product && isset($product->sale_price) ? $product->sale_price : 0;
        $quantity            = 1;
        $data['totalAmount'] = ($salePrice * $quantity);

        $data['deleted_hint'] = $product ? 0 : 1;
        $data['display_name'] = $product ? $product->name : __('Deleted product (ID: :id)', ['id' => (string)$request->product_id]);

        return json_encode($data);
    }

    public function update(Request $request, Retainer $retainer)
    {
        if (!\Auth::user()->can('edit retainer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($retainer->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'customer_id' => 'required',
                'issue_date'  => 'required',
                'category_id' => 'required',
                'items'       => 'required',
            ]
        );
        if ($validator->fails()) {
            return redirect()->route('retainer.index')->with('error', $validator->getMessageBag()->first());
        }

        $retainer->customer_id    = $request->customer_id;
        $retainer->issue_date     = $request->issue_date;
        $retainer->category_id    = $request->category_id;
        $retainer->discount_apply = isset($request->discount_apply) ? 1 : 0;
        $retainer->save();

        CustomField::saveData($retainer, $request->customField);

        $products = $request->items;

        for ($i = 0; $i < count($products); $i++) {
            $retainerProduct = RetainerProduct::find($products[$i]['id']);

            if ($retainerProduct == null) {
                $retainerProduct               = new RetainerProduct();
                $retainerProduct->retainer_id  = $retainer->id;

                $idKey = isset($products[$i]['item']) ? 'item' : (isset($products[$i]['items']) ? 'items' : null);
                if ($idKey && ProductService::where('id', $products[$i][$idKey])->exists()) {
                    Utility::total_quantity('minus', $products[$i]['quantity'], $products[$i][$idKey]);
                }

                $updatePrice = ($products[$i]['price'] * $products[$i]['quantity']) + ($products[$i]['itemTaxPrice']) - ($products[$i]['discount']);
                Utility::updateUserBalance('customer', $request->customer_id, $updatePrice, 'credit');
            } else {
                if (ProductService::where('id', $retainerProduct->product_id)->exists()) {
                    Utility::total_quantity('minus', $retainerProduct->quantity, $retainerProduct->product_id);
                }
            }

            if (isset($products[$i]['item']) && ProductService::where('id', $products[$i]['item'])->exists()) {
                $retainerProduct->product_id = $products[$i]['item'];
            }

            $retainerProduct->quantity    = $products[$i]['quantity'];
            $retainerProduct->tax         = $products[$i]['tax'] ?? 0;
            $retainerProduct->discount    = $products[$i]['discount'];
            $retainerProduct->price       = $products[$i]['price'];
            $retainerProduct->description = $products[$i]['description'];
            $retainerProduct->save();
        }

        return redirect()->route('retainer.index')->with('success', __('Retainer successfully updated.'));
    }

    public function destroy(Retainer $retainer)
    {
        if (!\Auth::user()->can('delete retainer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($retainer->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        foreach ($retainer->payments as $retPay) {
            Utility::bankAccountBalance($retPay->account_id, $retPay->amount, 'debit');

            $retainerpayment = RetainerPayment::find($retPay->id);
            $retPay->delete();
            if ($retainerpayment) {
                $retainerpayment->delete();
            }
        }

        if ($retainer->customer_id != 0 && $retainer->status != 0) {
            Utility::updateUserBalance('customer', $retainer->customer_id, $retainer->getDue(), 'debit');
        }

        RetainerProduct::where('retainer_id', $retainer->id)->delete();

        $retainer->delete();

        return redirect()->route('retainer.index')->with('success', __('Retainer successfully deleted.'));
    }

    public function duplicate($retainer_id)
    {
        if (!\Auth::user()->can('duplicate retainer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $retainer                       = Retainer::where('id', $retainer_id)->first();
        $duplicateRetainer              = new Retainer();
        $duplicateRetainer->retainer_id = $this->retainerNumber();
        $duplicateRetainer->customer_id = $retainer['customer_id'];
        $duplicateRetainer->issue_date  = date('Y-m-d');
        $duplicateRetainer->send_date   = null;
        $duplicateRetainer->category_id = $retainer['category_id'];
        $duplicateRetainer->status      = 0;
        $duplicateRetainer->created_by  = $retainer['created_by'];
        $duplicateRetainer->save();

        if ($duplicateRetainer) {
            $retainerProduct = RetainerProduct::where('retainer_id', $retainer_id)->get();
            foreach ($retainerProduct as $product) {
                $duplicateProduct               = new RetainerProduct();
                $duplicateProduct->retainer_id  = $duplicateRetainer->id;
                $duplicateProduct->product_id   = $product->product_id;
                $duplicateProduct->quantity     = $product->quantity;
                $duplicateProduct->tax          = $product->tax ?? 0;
                $duplicateProduct->discount     = $product->discount;
                $duplicateProduct->price        = $product->price;
                $duplicateProduct->save();
            }
        }

        return redirect()->back()->with('success', __('Retainer duplicate successfully.'));
    }

    public function sent($id)
    {
        if (!\Auth::user()->can('send retainer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $retainer            = Retainer::where('id', $id)->first();
        $retainer->send_date = date('Y-m-d');
        $retainer->status    = 1;
        $retainer->save();

        $customer           = Customer::where('id', $retainer->customer_id)->first();
        $retainer->name     = !empty($customer) ? $customer->name : '';
        $retainer->retainer = \Auth::user()->retainerNumberFormat($retainer->retainer_id);

        $retainerId    = Crypt::encrypt($retainer->id);
        $retainer->url = route('retainer.pdf', $retainerId);

        Utility::updateUserBalance('customer', $customer->id, $retainer->getTotal(), 'credit');

        $uArr = [
            'retainer_name'   => $retainer->name,
            'retainer_number' => $retainer->retainer,
            'retainer_url'    => $retainer->url,
        ];

        try {
            $resp = Utility::sendEmailTemplate('retainer_sent', [$customer->id => $customer->email], $uArr);
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        return redirect()->back()->with('success', __('Retainer successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function statusChange(Request $request, $id)
    {
        $retainer         = Retainer::find($id);
        $retainer->status = $request->status;
        $retainer->save();

        return redirect()->back()->with('success', __('Retainer status changed successfully.'));
    }

    public function payretainer($retainer_id)
    {
        try {
            if (empty($retainer_id)) {
                return abort('404', 'The Link You Followed Has Expired');
            }

            $id = Crypt::decrypt($retainer_id);
            $retainer = Retainer::where('id', $id)->first();

            if (is_null($retainer)) {
                return abort('404', 'The Link You Followed Has Expired');
            }

            $settings = Utility::settings();

            $items         = [];
            $totalTaxPrice = 0;
            $totalQuantity = 0;
            $totalRate     = 0;
            $totalDiscount = 0;
            $taxesData     = [];

            foreach ($retainer->items as $item) {
                $totalQuantity += $item->quantity;
                $totalRate     += $item->price;
                $totalDiscount += $item->discount;
                $taxes         = Utility::tax($item->tax) ?? 0;

                $itemTaxes = [];
                foreach ($taxes as $tax) {
                    if (!empty($tax)) {
                        $taxPrice            = Utility::taxRate($tax->rate, $item->price, $item->quantity);
                        $totalTaxPrice       += $taxPrice;
                        $itemTax['tax_name'] = $tax->tax_name;
                        $itemTax['tax']      = $tax->tax . '%';
                        $itemTax['price']    = Utility::priceFormat($settings, $taxPrice);
                        $itemTaxes[]         = $itemTax;

                        if (array_key_exists($tax->name, $taxesData)) {
                            $taxesData[$itemTax['tax_name']] = $taxesData[$tax->tax_name] + $taxPrice;
                        } else {
                            $taxesData[$tax->tax_name] = $taxPrice;
                        }
                    } else {
                        $taxPrice            = Utility::taxRate(0, $item->price, $item->quantity);
                        $totalTaxPrice       += $taxPrice;
                        $itemTax['tax_name'] = 'No Tax';
                        $itemTax['tax']      = '';
                        $itemTax['price']    = Utility::priceFormat($settings, $taxPrice);
                        $itemTaxes[]         = $itemTax;

                        if (array_key_exists('No Tax', $taxesData)) {
                            $taxesData[$tax->tax_name] = $taxesData['No Tax'] + $taxPrice;
                        } else {
                            $taxesData['No Tax'] = $taxPrice;
                        }
                    }
                }
                $item->itemTax = $itemTaxes;
                $items[]       = $item;
            }
            $retainer->items         = $items;
            $retainer->totalTaxPrice = $totalTaxPrice;
            $retainer->totalQuantity = $totalQuantity;
            $retainer->totalRate     = $totalRate;
            $retainer->totalDiscount = $totalDiscount;
            $retainer->taxesData     = $taxesData;

            $ownerId = $retainer->created_by;

            $company_setting   = Utility::settingById($ownerId);
            $payment_setting   = Utility::invoice_payment_settings($ownerId);

            $users = User::where('id', $retainer->created_by)->first();
            if (!is_null($users)) {
                \App::setLocale($users->lang);
            } else {
                $users = User::where('type', 'owner')->first();
                \App::setLocale($users->lang);
            }

            $customer = $retainer->customer;
            $iteams   = $retainer->items;
            $company_payment_setting = Utility::getCompanyPaymentSetting($retainer->created_by);

            $plan = Plan::find($users->plan);

            return view('retainer.retainerpay', compact('retainer', 'iteams', 'company_setting', 'users', 'company_payment_setting', 'plan'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Not Found');
        }
    }

    public function retainer($retainer_id)
    {
        $settings   = Utility::settings();
        $retainerId = Crypt::decrypt($retainer_id);
        $retainer   = Retainer::where('id', $retainerId)->first();

        $data  = DB::table('settings')->where('created_by', $retainer->created_by)->get();
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        $customer = $retainer->customer;

        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];

        foreach ($retainer->items as $product) {
            $item              = new \stdClass();
            $item->name        = !empty($product->product) ? $product->product->name : '';
            $item->quantity    = $product->quantity;
            $item->tax         = $product->tax ?? 0;
            $item->discount    = $product->discount;
            $item->price       = $product->price;
            $item->description = $product->description;

            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            $taxes = Utility::tax($product->tax) ?? 0;
            $itemTaxes = [];
            if (!empty($item->tax)) {
                foreach ($taxes as $tax) {
                    $taxPrice      = Utility::taxRate($tax->rate, $item->price, $item->quantity, $item->discount);
                    $totalTaxPrice += $taxPrice;

                    $itemTax['name']      = $tax->name;
                    $itemTax['rate']      = $tax->rate . '%';
                    $itemTax['price']     = Utility::priceFormat($settings, $taxPrice);
                    $itemTax['tax_price'] = $taxPrice;
                    $itemTaxes[]          = $itemTax;

                    if (array_key_exists($tax->name, $taxesData)) {
                        $taxesData[$tax->name] = $taxesData[$tax->name] + $taxPrice;
                    } else {
                        $taxesData[$tax->name] = $taxPrice;
                    }
                }
                $item->itemTax = $itemTaxes;
            } else {
                $item->itemTax = [];
            }
            $items[] = $item;
        }

        $retainer->itemData      = $items;
        $retainer->totalTaxPrice = $totalTaxPrice;
        $retainer->totalQuantity = $totalQuantity;
        $retainer->totalRate     = $totalRate;
        $retainer->totalDiscount = $totalDiscount;
        $retainer->taxesData     = $taxesData;

        $retainer->customField = CustomField::getData($retainer, 'retainer');
        $customFields          = [];
        if (!empty(\Auth::user())) {
            $customFields = CustomField::where('created_by', \Auth::user()->creatorId())->where('module', 'retainer')->get();
        }

        $logo          = asset(Storage::url('uploads/logo/'));
        $company_logo  = Utility::getValByName('company_logo_dark');
        $settings_data = Utility::settingsById($retainer->created_by);
        $retainer_logo = $settings_data['retainer_logo'];
        if (isset($retainer_logo) && !empty($retainer_logo)) {
            $img = Utility::get_file('retainer_logo/') . $retainer_logo;
        } else {
            $img = isset($company_logo) && !empty($company_logo) ? asset($logo . '/' . $company_logo) . '?v=' . time() : '';
        }

        if ($retainer) {
            $color      = '#' . (isset($settings['retainer_color']) ? $settings['retainer_color'] : '297cc0');
            $font       = isset($settings['retainer_font']) ? $settings['retainer_font'] : 'Inter';
            $font_color = Utility::getFontColor($color);

            return view('retainer.templates.' . $settings['retainer_template'], compact('retainer', 'color', 'settings', 'customer', 'img', 'font_color', 'customFields', 'font'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function resent($id)
    {
        if (!\Auth::user()->can('send retainer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $retainer = Retainer::where('id', $id)->first();

        $customer           = Customer::where('id', $retainer->customer_id)->first();
        $retainer->name     = !empty($customer) ? $customer->name : '';
        $retainer->retainer = \Auth::user()->retainerNumberFormat($retainer->retainer_id);

        $retainerId    = Crypt::encrypt($retainer->id);
        $retainer->url = route('retainer.pdf', $retainerId);

        $uArr = [
            'retainer_name'   => $retainer->name,
            'retainer_number' => $retainer->retainer,
            'retainer_url'    => $retainer->url,
        ];

        try {
            $resp = Utility::sendEmailTemplate('retainer_sent', [$customer->id => $customer->email], $uArr);
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        return redirect()->back()->with('success', __('Retainer successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function saveRetainerTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $post = $request->all();
        unset($post['_token']);

        if ($request->retainer_logo) {
            $request->validate(['retainer_logo' => 'image']);

            $dir = 'retainer_logo/';
            $retainer_logo = $user->id . '_retainer_logo.png';
            $validation = [
                'mimes:' . 'png',
                'max:' . '20480',
            ];

            $path = Utility::upload_file($request, 'retainer_logo', $retainer_logo, $dir, $validation);
            if ($path['flag'] != 1) {
                return redirect()->back()->with('error', __($path['msg']));
            }

            $post['retainer_logo'] = $retainer_logo;
        }

        if (isset($post['retainer_template']) && (!isset($post['retainer_color']) || empty($post['retainer_color']))) {
            $post['retainer_color'] = "ffffff";
        }

        foreach ($post as $key => $data) {
            DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                [$data, $key, \Auth::user()->creatorId()]
            );
        }

        return redirect()->back()->with('success', __('Retainer Setting updated successfully'));
    }

    public function previewRetainer($template, $color, Request $request)
    {
        $settings = Utility::settings();
        $retainer = new Retainer();

        $customer                   = new \stdClass();
        $customer->email            = '<Email>';
        $customer->shipping_name    = '<Customer Name>';
        $customer->shipping_country = '<Country>';
        $customer->shipping_state   = '<State>';
        $customer->shipping_city    = '<City>';
        $customer->shipping_phone   = '<Customer Phone Number>';
        $customer->shipping_zip     = '<Zip>';
        $customer->shipping_address = '<Address>';
        $customer->billing_name     = '<Customer Name>';
        $customer->billing_country  = '<Country>';
        $customer->billing_state    = '<State>';
        $customer->billing_city     = '<City>';
        $customer->billing_phone    = '<Customer Phone Number>';
        $customer->billing_zip      = '<Zip>';
        $customer->billing_address  = '<Address>';
        $customer->sku              = 'Test123';

        $totalTaxPrice = 0;
        $taxesData     = [];
        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $item           = new \stdClass();
            $item->name     = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax      = 5;
            $item->discount = 50;
            $item->price    = 100;

            $taxes = ['Tax 1', 'Tax 2'];

            $itemTaxes = [];
            foreach ($taxes as $k => $tax) {
                $taxPrice              = 10;
                $totalTaxPrice        += $taxPrice;
                $itemTax['name']       = 'Tax ' . $k;
                $itemTax['rate']       = '10 %';
                $itemTax['price']      = '$10';
                $itemTax['tax_price']  = 10;
                $itemTaxes[]           = $itemTax;

                if (array_key_exists('Tax ' . $k, $taxesData)) {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                } else {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }

        $retainer->retainer_id = 1;
        $retainer->issue_date  = date('Y-m-d H:i:s');
        $retainer->due_date    = date('Y-m-d H:i:s');
        $retainer->itemData    = $items;

        $retainer->totalTaxPrice = 60;
        $retainer->totalQuantity = 3;
        $retainer->totalRate     = 300;
        $retainer->totalDiscount = 10;
        $retainer->taxesData     = $taxesData;
        $retainer->customField   = [];
        $customFields            = [];

        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $font = $request->get('font', 'Inter');

        $logo          = asset(Storage::url('uploads/logo/'));
        $company_logo  = Utility::getValByName('company_logo_dark');
        $retainer_logo = Utility::getValByName('retainer_logo');
        if (isset($retainer_logo) && !empty($retainer_logo)) {
            $img = Utility::get_file('retainer_logo/') . $retainer_logo;
        } else {
            $img = isset($company_logo) && !empty($company_logo) ? asset($logo . '/' . $company_logo) . '?v=' . time() : '';
        }

        return view('retainer.templates.' . $template, compact('retainer', 'preview', 'color', 'img', 'settings', 'customer', 'font_color', 'customFields', 'font'));
    }

    public function payment($retainer_id)
    {
        if (!\Auth::user()->can('create payment invoice')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $retainer  = Retainer::where('id', $retainer_id)->first();
        $customers = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $categories = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))
            ->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

        return view('retainer.payment', compact('customers', 'categories', 'accounts', 'retainer'));
    }

    public function createPayment(Request $request, $retainer_id)
    {
        if (!\Auth::user()->can('create payment invoice')) {
            return;
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'date'       => 'required',
                'amount'     => 'required',
                'account_id' => 'required',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $retainerPayment                 = new RetainerPayment();
        $retainerPayment->retainer_id    = $retainer_id;
        $retainerPayment->date           = $request->date;
        $retainerPayment->amount         = $request->amount;
        $retainerPayment->account_id     = $request->account_id;
        $retainerPayment->payment_method = 0;
        $retainerPayment->reference      = $request->reference;
        $retainerPayment->description    = $request->description;

        if (!empty($request->add_receipt)) {
            $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
            $retainerPayment->add_receipt = $fileName;

            $dir  = 'uploads/retainerpayment';
            $path = Utility::upload_file($request, 'add_receipt', $fileName, $dir, []);
            if ($path['flag'] != 1) {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $retainerPayment->save();
        }
        $retainerPayment->save();

        $retainer = Retainer::where('id', $retainer_id)->first();
        $due      = $retainer->getDue();
        $total    = $retainer->getTotal();

        if ($retainer->status == 0) {
            $retainer->send_date = date('Y-m-d');
            $retainer->save();
        }

        if ($due <= 0) {
            $retainer->status = 4;
        } else {
            $retainer->status = 3;
        }
        $retainer->save();

        $retainerPayment->user_id    = $retainer->customer_id;
        $retainerPayment->user_type  = 'Customer';
        $retainerPayment->type       = 'Partial';
        $retainerPayment->created_by = \Auth::user()->id;
        $retainerPayment->payment_id = $retainerPayment->id;
        $retainerPayment->category   = 'Retainer';
        $retainerPayment->account    = $request->account_id;

        Transaction::addTransaction($retainerPayment);

        $customer = Customer::where('id', $retainer->customer_id)->first();

        $payment            = new RetainerPayment();
        $payment->name      = $customer['name'];
        $payment->date      = \Auth::user()->dateFormat($request->date);
        $payment->amount    = \Auth::user()->priceFormat($request->amount);
        $payment->retainer  = 'retainer ' . \Auth::user()->retainerNumberFormat($retainer->retainer_id);
        $payment->dueAmount = \Auth::user()->priceFormat($retainer->getDue());

        Utility::updateUserBalance('customer', $retainer->customer_id, $request->amount, 'debit');
        Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

        $uArr = [
            'payment_name'     => $payment->name,
            'payment_amount'   => $payment->amount,
            'retainer_number'  => $payment->retainer,
            'payment_date'     => $payment->date,
            'payment_dueAmount'=> $payment->dueAmount,
        ];
        try {
            $resp = Utility::sendEmailTemplate('new_retainer_payment', [$customer->id => $customer->email], $uArr);
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        return redirect()->back()->with('success', __('Payment successfully added.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function paymentReminder($retainer_id)
    {
        $retainer            = Retainer::find($retainer_id);
        $customer            = Customer::where('id', $retainer->customer_id)->first();
        $retainer->dueAmount = \Auth::user()->priceFormat($retainer->getDue());
        $retainer->name      = $customer['name'];
        $retainer->date      = \Auth::user()->dateFormat($retainer->send_date);
        $retainer->retainer  = \Auth::user()->retainerNumberFormat($retainer->retainer_id);

        $uArr = [
            'payment_name'     => $retainer->name,
            'invoice_number'   => $retainer->retainer,
            'payment_dueAmount'=> $retainer->dueAmount,
            'payment_date'     => $retainer->date,
        ];
        try {
            $resp = Utility::sendEmailTemplate('payment_reminder', [$customer->id => $customer->email], $uArr);
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        $module  = 'New Reminder';
        $webhook = Utility::webhookSetting($module);
        if ($webhook) {
            $parameter = json_encode($retainer);
            $status    = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
            if ($status == true) {
                return redirect()->back()->with('success', __('Payment reminder successfully send.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
            } else {
                return redirect()->back()->with('error', __('Webhook call failed.'));
            }
        }

        return redirect()->back()->with('success', __('Payment reminder successfully send.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function paymentDestroy(Request $request, $retainer_id, $payment_id)
    {
        if (!\Auth::user()->can('delete payment invoice')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $payment = RetainerPayment::find($payment_id);
        RetainerPayment::where('id', $payment_id)->delete();

        $retainer = Retainer::where('id', $retainer_id)->first();
        $due      = $retainer->getDue();
        $total    = $retainer->getTotal();

        if ($due > 0 && $total != $due) {
            $retainer->status = 3;
        } else {
            $retainer->status = 2;
        }

        $retainer->save();

        $type = 'Partial';
        $user = 'Customer';
        Transaction::destroyTransaction($payment_id, $type, $user);

        Utility::updateUserBalance('customer', $retainer->customer_id, $payment->amount, 'credit');
        Utility::bankAccountBalance($payment->account_id, $payment->amount, 'debit');

        return redirect()->back()->with('success', __('Payment successfully deleted.'));
    }

    function invoiceNumber()
    {
        $latest = Invoice::where('created_by', \Auth::user()->creatorId())->latest()->first();
        return $latest ? $latest->invoice_id + 1 : 1;
    }

    public function convert($retainer_id)
    {
        if (!\Auth::user()->can('convert invoice retainer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $retainer             = Retainer::where('id', $retainer_id)->first();
        $retainer->is_convert = 1;
        $retainer->save();

        $convertInvoice              = new Invoice();
        $convertInvoice->invoice_id  = $this->invoiceNumber();
        $convertInvoice->customer_id = $retainer['customer_id'];
        $convertInvoice->issue_date  = date('Y-m-d');
        $convertInvoice->due_date    = date('Y-m-d');
        $convertInvoice->send_date   = null;
        $convertInvoice->category_id = $retainer['category_id'];
        $convertInvoice->status      = 0;
        $convertInvoice->created_by  = $retainer['created_by'];
        $convertInvoice->save();

        $retainer->converted_invoice_id = $convertInvoice->id;
        $retainer->save();

        if ($convertInvoice) {
            $retainerProduct = RetainerProduct::where('retainer_id', $retainer_id)->get();
            foreach ($retainerProduct as $product) {
                $duplicateProduct              = new InvoiceProduct();
                $duplicateProduct->invoice_id  = $convertInvoice->id;
                $duplicateProduct->product_id  = $product->product_id;
                $duplicateProduct->quantity    = $product->quantity;
                $duplicateProduct->tax         = $product->tax;
                $duplicateProduct->discount    = $product->discount;
                $duplicateProduct->price       = $product->price;
                $duplicateProduct->save();

                Utility::total_quantity('minus', $duplicateProduct->quantity, $duplicateProduct->product_id);

                $type        = 'invoice';
                $type_id     = $convertInvoice->id;
                $description = $duplicateProduct->quantity . '  ' . __(' quantity sold in invoice') . ' ' . \Auth::user()->invoiceNumberFormat($convertInvoice->invoice_id);
                Utility::addProductStock($product->product_id, $duplicateProduct->quantity, $type, $description, $type_id);
            }
        }
        if ($convertInvoice) {
            $retainerPayment = RetainerPayment::where('retainer_id', $retainer_id)->get();
            foreach ($retainerPayment as $payment) {
                $duplicatePayment                 = new InvoicePayment();
                $duplicatePayment->invoice_id     = $convertInvoice->id;
                $duplicatePayment->date           = $payment->date;
                $duplicatePayment->amount         = $payment->amount;
                $duplicatePayment->account_id     = $payment->account_id;
                $duplicatePayment->payment_method = $payment->payment_method;
                $duplicatePayment->receipt        = $payment->receipt;
                $duplicatePayment->payment_type   = $payment->payment_type;
                $duplicatePayment->reference      = $payment->reference;
                $duplicatePayment->description    = 'Payment by Retainer ' . \Auth::user()->retainerNumberFormat($retainer->retainer_id);
                $duplicatePayment->save();
            }
        }

        return redirect()->back()->with('success', __('Retainer to invoice convert successfully.'));
    }

    public function customerRetainer(Request $request)
    {
        if (!\Auth::user()->can('manage customer proposal')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $status = Retainer::$statues;

        $query = Retainer::where('customer_id', \Auth::user()->id)
            ->where('status', '!=', '0')
            ->where('created_by', \Auth::user()->creatorId());

        if (str_contains((string)$request->issue_date, ' to ')) {
            $date_range = explode(' to ', $request->issue_date);
            $query->whereBetween('issue_date', $date_range);
        } elseif (!empty($request->issue_date)) {
            $query->where('issue_date', $request->issue_date);
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        $retainers = $query->get();

        return view('retainer.index', compact('retainers', 'status'));
    }

    public function customerRetainerShow($id)
    {
        $retainer_id = Crypt::decrypt($id);
        $retainer    = Retainer::where('id', $retainer_id)->first();

        if ($retainer->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $customer = $retainer->customer;
        $iteams   = $retainer->items;

        $company_payment_setting = Utility::getCompanyPaymentSetting($retainer->created_by);

        return view('retainer.view', compact('retainer', 'customer', 'iteams', 'company_payment_setting'));
    }

    public function customerRetainerSend($retainer_id)
    {
        return view('customer.retainer_send', compact('retainer_id'));
    }

    public function customerRetainerSendMail(Request $request, $retainer_id)
    {
        $validator = \Validator::make(
            $request->all(),
            ['email' => 'required|email']
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $email    = $request->email;
        $retainer = Retainer::where('id', $retainer_id)->first();

        $customer          = Customer::where('id', $retainer->customer_id)->first();
        $retainer->name    = !empty($customer) ? $customer->name : '';
        $retainer->retainer = \Auth::user()->retainerNumberFormat($retainer->retainer_id);

        $retainerId    = Crypt::encrypt($retainer->id);
        $retainer->url = route('retainer.pdf', $retainerId);

        $uArr = [
            'retainer_name'   => $retainer->name,
            'retainer_number' => $retainer->retainer,
            'retainer_url'    => $retainer->url,
        ];

        try {
            $resp = Utility::sendEmailTemplate('customer_retainer_sent', [$customer->id => $customer->email], $uArr);
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        return redirect()->back()->with('success', __('Retainer successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function export()
    {
        $name = 'customer_' . date('Y-m-d_H-i-s');
        return Excel::download(new RetainerExport(), $name . '.xlsx');
    }

    public function productDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete retainer product')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $retainerProduct = RetainerProduct::find($request->id);
        $retainer        = Retainer::find($retainerProduct->retainer_id);

        Utility::updateUserBalance('customer', $retainer->customer_id, $request->amount, 'debit');

        RetainerProduct::where('id', $request->id)->delete();

        return redirect()->back()->with('success', __('Retainer product successfully deleted.'));
    }

    public function items(Request $request)
    {
        $items = RetainerProduct::where('retainer_id', $request->retainer_id)
            ->where('product_id', $request->product_id)
            ->first();

        return json_encode($items);
    }

    public function bulkDestroy(Request $request)
    {
        if (!\Auth::user()->can('delete retainer')) {
            return redirect()->route('retainer.index')->with('error', __('Permission denied.'));
        }

        $ids = array_filter((array)$request->input('ids', []));
        if (empty($ids)) {
            return redirect()->route('retainer.index')->with('error', __('No retainers selected.'));
        }

        $retainers = Retainer::where('created_by', \Auth::user()->creatorId())
            ->whereIn('id', $ids)
            ->get();

        $deleted = 0;

        foreach ($retainers as $retainer) {
            foreach ($retainer->payments as $pay) {
                Utility::bankAccountBalance($pay->account_id, $pay->amount, 'debit');

                $retainerPayment = RetainerPayment::find($pay->id);
                $pay->delete();
                if ($retainerPayment) {
                    $retainerPayment->delete();
                }
            }

            if ($retainer->customer_id != 0 && $retainer->status != 0) {
                Utility::updateUserBalance('customer', $retainer->customer_id, $retainer->getDue(), 'debit');
            }

            RetainerProduct::where('retainer_id', $retainer->id)->delete();

            $retainer->delete();
            $deleted++;
        }

        $msg = trans_choice(':count retainer deleted.|:count retainers deleted.', $deleted, ['count' => $deleted]);
        return redirect()->route('retainer.index')->with('success', $msg);
    }

    public function exportSelected(Request $request)
    {
        $ids = array_filter((array)$request->input('ids', []));
        if (empty($ids)) {
            return redirect()->route('retainer.index')->with('error', __('No retainers selected.'));
        }

        $name = 'retainers_selected_' . date('Y-m-d_H-i-s');
        return Excel::download(new RetainerExport($ids), $name . '.xlsx');
    }
}
