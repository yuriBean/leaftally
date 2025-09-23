<?php

namespace App\Http\Controllers;

use App\Models\Mail\EmailTest;
use App\Models\Mail\testMail;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Artisan;
use Illuminate\Support\Facades\Validator;

class SystemController extends Controller
{
    public function index()
    {
        $settings              = Utility::settings();

        if (\Auth::user()->can('manage system settings')) {
            $settings              = Utility::settings();
            $admin_payment_setting = Utility::getAdminPaymentSetting();
            $currencies              = \App\Models\Currency::getCurrenciesForDropdown();
            return view('settings.index', compact('settings', 'admin_payment_setting', 'currencies'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('manage system settings')) {
            if ($request->logo_dark) {
                $request->validate(
                    [
                        'logo_dark' => 'image',
                    ]
                );

                $logoName = 'logo-dark.png';
                $dir = 'uploads/logo/';

                $validation = [
                    'mimes:' . 'png',
                    'max:' . '20480',
                ];

                $path = Utility::upload_file($request, 'logo_dark', $logoName, $dir, $validation);

                if ($path['flag'] == 1) {
                    $logo_dark = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }

            }

            if ($request->logo_light) {
                $request->validate(
                    [
                        'logo_light' => 'image',
                    ]
                );
                $lightlogoName = 'logo-light.png';

                $dir = 'uploads/logo/';

                $validation = [
                    'mimes:' . 'png',
                    'max:' . '20480',
                ];

                $path = Utility::upload_file($request, 'logo_light', $lightlogoName, $dir, $validation);

                if ($path['flag'] == 1) {
                    $logo_light = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }

            }

            if ($request->favicon) {
                $request->validate(
                    [
                        'favicon' => 'image',
                    ]
                );
                $favicon = 'favicon.png';
                
                $dir = 'uploads/logo/';
                
                $validation = [
                    'mimes:' . 'png',
                    'max:' . '20480',
                ];
                
                $path = Utility::upload_file($request, 'favicon', $favicon, $dir, $validation);

                if ($path['flag'] == 1) {
                    $favicon = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            if ($request->landing_logo) {
                $request->validate(
                    [
                        'landing_logo' => 'image',
                    ]
                );
                $landingLogoName = 'landing_logo.png';
                $path            = $request->file('landing_logo')->storeAs('uploads/logo/', $landingLogoName);
            }

            $arrEnv = [
                'SITE_RTL' => !isset($request->SITE_RTL) ? 'off' : 'on',
            ];
            Utility::setEnvironmentValue($arrEnv);

            $settings = Utility::settings();
            if (!empty($request->title_text) || !empty($request->email_verification) || !empty($request->footer_text) || !empty($request->default_language) || isset($request->display_landing_page) || isset($request->enable_signup) || isset($request->color) || isset($request->cust_theme_bg) || isset($request->cust_darklayout)) {
                $post = $request->all();
                if (!isset($request->display_landing_page)) {
                    $post['display_landing_page'] = 'off';
                }
               
                if (!isset($request->enable_signup)) {
                    $post['enable_signup'] = 'off';
                }
                if (!isset($request->email_verification)) {
                    $post['email_verification'] = 'off';
                }

                if (!isset($request->cust_theme_bg)) {
                    $cust_theme_bg         = (isset($request->cust_theme_bg)) ? 'on' : 'off';
                    $post['cust_theme_bg'] = $cust_theme_bg;
                }
                if (!isset($request->cust_darklayout)) {

                    $cust_darklayout         = isset($request->cust_darklayout) ? 'on' : 'off';
                    $post['cust_darklayout'] = $cust_darklayout;
                }

                if (!isset($request->SITE_RTL)) {
                    $SITE_RTL         = isset($request->SITE_RTL) ? 'on' : 'off';
                    $post['SITE_RTL'] = $SITE_RTL;
                }

                if (isset($request->color) && $request->color_flag == 'false') {
                    $post['color'] = $request->color;
                } else {
                    $post['color'] = $request->custom_color;
                }

                unset($post['_token'], $post['logo_dark'], $post['logo_light'], $post['favicon']);
                foreach ($post as $key => $data) {
                    if (in_array($key, array_keys($settings))) {
                        \DB::insert(
                            'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                            [
                                $data,
                                $key,
                                \Auth::user()->creatorId(),
                            ]
                        );
                    }
                }
            }

            return redirect()->back()->with('success', 'Setting successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function testEmailConnection(Request $request)
    {
        if (\Auth::user()->can('manage system settings')) {
            $result = Utility::testSMTPConnection(\Auth::user()->creatorId());
            
            if ($result['success']) {
                return response()->json(['success' => true, 'message' => $result['message']]);
            } else {
                return response()->json(['success' => false, 'message' => $result['message']]);
            }
        }
        
        return response()->json(['success' => false, 'message' => 'Permission denied.']);
    }

    public function saveEmailSettings(Request $request)
    {
        if (\Auth::user()->can('manage system settings')) {
            $request->validate(
                [
                    'mail_driver' => 'required|string|max:255',
                    'mail_host' => 'required|string|max:255',
                    'mail_port' => 'required|string|max:255',
                    'mail_username' => 'required|string|max:255',
                    'mail_password' => 'required|string|max:255',
                    'mail_encryption' => 'required|string|max:255',
                    'mail_from_address' => 'required|string|max:255',
                    'mail_from_name' => 'required|string|max:255',
                ]
            );

            $post = $request->all();
            unset($post['_token']);

            $settings = Utility::settings();
            foreach ($post as $key => $data) {
                if (in_array($key, array_keys($settings))) {
                    \DB::insert(
                        'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                        [
                            $data,
                            $key,
                            \Auth::user()->id,
                            date('Y-m-d H:i:s'),
                            date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function saveCompanySettings(Request $request)
    {
        if (\Auth::user()->can('manage company settings')) {
            $user = \Auth::user();
            $request->validate(
                [
                    'company_name' => 'required|string|max:255',
                ]
            );
            $post = $request->all();
            unset($post['_token']);

            if (!isset($post['tax_number'])) {
                $post['tax_number'] = 'off';
            }

            $settings = Utility::settings();
            foreach ($post as $key => $data) {
                if (in_array($key, array_keys($settings))) {
                    \DB::insert(
                        'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                        [
                            $data,
                            $key,
                            \Auth::user()->creatorId(),
                        ]
                    );
                }
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function savePaymentSettings(Request $request)
    {
        if (\Auth::user()->can('manage stripe settings')) {

            $request->validate(
                [
                    'currency' => 'required|string',
                    'currency_symbol' => 'required|string',
                ]
            );

            self::adminPaymentSettings($request);

            $currency = \App\Models\Currency::getByCode($request->currency);
            $currency_symbol = $currency ? $currency->symbol : $request->currency_symbol;

            $post = [
                'currency' => $request->currency,
                'currency_symbol' => $currency_symbol, 
            ];
            unset($post['_token']);
            foreach ($post as $key => $data) {
                $arr = [
                    $data,
                    $key,
                    \Auth::user()->id,
                ];
                \DB::insert(
                    'insert into admin_payment_settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    $arr
                );
            }

            return redirect()->back()->with('success', __('Payment setting updated successfully.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function saveSystemSettings(Request $request)
    {
        if (\Auth::user()->can('manage company settings')) {
            $user = \Auth::user();
            $request->validate(
                [
                    'site_currency' => 'required',
                ]
            );
            $post = $request->all();
            unset($post['_token']);

            if (!isset($post['shipping_display'])) {
                $post['shipping_display'] = 'off';
            }

            if (isset($post['site_currency'])) {
                $currency = \App\Models\Currency::getByCode($post['site_currency']);
                if ($currency) {
                    $post['site_currency_symbol'] = $currency->symbol;
                }
            }

            $settings = Utility::settings();
            $settings['footer_notes'] = $request->input('footer_notes');

            foreach ($post as $key => $data) {
                if (in_array($key, array_keys($settings))) {
                    \DB::insert(
                        'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                        [
                            $data,
                            $key,
                            \Auth::user()->creatorId(),
                            date('Y-m-d H:i:s'),
                            date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function saveBusinessSettings(Request $request)
    {
        if (\Auth::user()->can('manage business settings')) {

            $user = \Auth::user();

            if ($request->company_logo_dark) {

                $request->validate(
                    [
                        'company_logo_dark' => 'image',
                    ]
                );

                $logoName     = $user->id . '-logo-dark.png';
                $dir = 'uploads/logo/';

                $validation = [
                    'mimes:' . 'png',
                    'max:' . '20480',
                ];

                $file_path = $request->company_logo_dark;
                $image_size = $request->file('company_logo_dark')->getSize();

                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if ($result == 1) {
                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                    $path = Utility::upload_file($request, 'company_logo_dark', $logoName, $dir, $validation);

                    if ($path['flag'] == 1) {
                        $company_logo_dark = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                } else {
                    return redirect()->back()->with('error', $result);
                }

                $company_logo = !empty($request->company_logo_dark) ? $logoName : 'logo-dark.png';
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $logoName,
                        'company_logo_dark',
                        \Auth::user()->creatorId(),
                    ]
                );
                
                Utility::clearSettingsCache();
            }

            if ($request->company_logo_light) {

                $request->validate(
                    [
                        'company_logo_light' => 'image',
                    ]
                );

                $logoName     = $user->id . '-logo-light.png';

                $dir = 'uploads/logo/';

                $validation = [
                    'mimes:' . 'png',
                    'max:' . '20480',
                ];

                $file_path = $request->company_logo_light;
                $image_size = $request->file('company_logo_light')->getSize();

                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if ($result == 1) {
                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                    $path = Utility::upload_file($request, 'company_logo_light', $logoName, $dir, $validation);

                    if ($path['flag'] == 1) {
                        $company_logo_light = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                } else {
                    return redirect()->back()->with('error', $result);
                }

                $company_logo = !empty($request->company_logo_light) ? $logoName : 'logo-light.png';

                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $logoName,
                        'company_logo_light',
                        \Auth::user()->creatorId(),
                    ]
                );
                
                Utility::clearSettingsCache();
            }
            if ($request->company_favicon) {
                $request->validate(
                    [
                        'company_favicon' => 'image',
                    ]
                );
                $favicon = $user->id . '_favicon.png';

                $dir = 'uploads/logo/';

                $validation = [
                    'mimes:' . 'png',
                    'max:' . '20480',
                ];

                $file_path = $request->company_favicon;
                $image_size = $request->file('company_favicon')->getSize();

                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if ($result == 1) {
                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                    $path = Utility::upload_file($request, 'company_favicon', $favicon, $dir, $validation);

                    if ($path['flag'] == 1) {
                        $company_favicon = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                } else {
                    return redirect()->back()->with('error', $result);
                }

                $company_favicon = !empty($request->favicon) ? $favicon : 'favicon.png';

                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $favicon,
                        'company_favicon',
                        \Auth::user()->creatorId(),
                    ]
                );
                
                Utility::clearSettingsCache();
            }

            $settings = Utility::settings();

            if (!empty($request->title_text) || !empty($request->SITE_RTL) || !empty($request->cust_theme_bg) || !empty($request->cust_darklayout)) {
                $post = $request->all();

                unset($post['_token'], $post['company_logo_dark'], $post['company_logo_light'], $post['company_favicon']);

                if (!isset($request->SITE_RTL)) {
                    $post['SITE_RTL'] = 'off';
                }

                if (!isset($request->cust_theme_bg)) {
                    $post['cust_theme_bg'] = 'off';
                }

                if (!isset($request->cust_darklayout)) {
                    $post['cust_darklayout'] = 'off';
                }
                if (isset($request->color) && $request->color_flag == 'false') {
                    $post['color'] = $request->color;
                } else {
                    $post['color'] = $request->custom_color;
                }
                foreach ($post as $key => $data) {
                    if (in_array($key, array_keys($settings))) {
                        \DB::insert(
                            'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                            [
                                $data,
                                $key,
                                \Auth::user()->creatorId(),
                            ]
                        );
                    }
                }
            }

            return redirect()->back()->with('success', 'Brand Setting successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function companyIndex()
    {
        $usr = \Auth::user();

        if ($usr->type == 'company') {
            if (\Auth::user()->can('manage company settings')) {
                $settings                = Utility::settings();
                $company_payment_setting = Utility::getCompanyPaymentSetting(\Auth::user()->id);
                $currencies              = \App\Models\Currency::getCurrenciesForDropdown();

                return view('settings.company', compact('settings', 'company_payment_setting', 'currencies'));
            } else {
                return redirect()->back()->with('error', 'Permission denied.');
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function saveCompanyPaymentSettings(Request $request)
    {
        if (isset($request->is_stripe_enabled) && $request->is_stripe_enabled == 'on') {

            $request->validate([
                'stripe_key' => 'required|string|max:255',
                'stripe_secret' => 'required|string|max:255',
            ]);

            $post['is_stripe_enabled'] = $request->is_stripe_enabled;
            $post['stripe_secret']     = $request->stripe_secret;
            $post['stripe_key']        = $request->stripe_key;
        } else {
            $post['is_stripe_enabled'] = 'off';
        }

        if (isset($request->is_paypal_enabled) && $request->is_paypal_enabled == 'on') {
            $request->validate([
                'paypal_mode' => 'required',
                'paypal_client_id' => 'required',
                'paypal_secret_key' => 'required',
            ]);

            $post['is_paypal_enabled'] = $request->is_paypal_enabled;
            $post['paypal_mode']       = $request->paypal_mode;
            $post['paypal_client_id']  = $request->paypal_client_id;
            $post['paypal_secret_key'] = $request->paypal_secret_key;
        } else {
            $post['is_paypal_enabled'] = 'off';
        }

        if (isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on') {
            $request->validate([
                'paystack_public_key' => 'required|string',
                'paystack_secret_key' => 'required|string',
            ]);
            $post['is_paystack_enabled'] = $request->is_paystack_enabled;
            $post['paystack_public_key'] = $request->paystack_public_key;
            $post['paystack_secret_key'] = $request->paystack_secret_key;
        } else {
            $post['is_paystack_enabled'] = 'off';
        }

        if (isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on') {
            $request->validate([
                'flutterwave_public_key' => 'required|string',
                'flutterwave_secret_key' => 'required|string',
            ]);
            $post['is_flutterwave_enabled'] = $request->is_flutterwave_enabled;
            $post['flutterwave_public_key'] = $request->flutterwave_public_key;
            $post['flutterwave_secret_key'] = $request->flutterwave_secret_key;
        } else {
            $post['is_flutterwave_enabled'] = 'off';
        }
        if (isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on') {
            $request->validate([
                'razorpay_public_key' => 'required|string',
                'razorpay_secret_key' => 'required|string',
            ]);
            $post['is_razorpay_enabled'] = $request->is_razorpay_enabled;
            $post['razorpay_public_key'] = $request->razorpay_public_key;
            $post['razorpay_secret_key'] = $request->razorpay_secret_key;
        } else {
            $post['is_razorpay_enabled'] = 'off';
        }

        if (isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on') {
            $request->validate(
                [
                    'mercado_mode' => 'required',
                    'mercado_access_token' => 'required|string',
                ]
            );

            $post['is_mercado_enabled'] = $request->is_mercado_enabled;
            $post['mercado_mode'] = $request->mercado_mode;
            $post['mercado_access_token']     = $request->mercado_access_token;
        } else {
            $post['is_mercado_enabled'] = 'off';
        }

        if (isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on') {
            $request->validate([
                'paytm_mode' => 'required',
                'paytm_merchant_id' => 'required|string',
                'paytm_merchant_key' => 'required|string',
                'paytm_industry_type' => 'required|string',
            ]);
            $post['is_paytm_enabled']    = $request->is_paytm_enabled;
            $post['paytm_mode']          = $request->paytm_mode;
            $post['paytm_merchant_id']   = $request->paytm_merchant_id;
            $post['paytm_merchant_key']  = $request->paytm_merchant_key;
            $post['paytm_industry_type'] = $request->paytm_industry_type;
        } else {
            $post['is_paytm_enabled'] = 'off';
        }
        if (isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on') {
            $request->validate([
                'mollie_api_key' => 'required|string',
                'mollie_profile_id' => 'required|string',
                'mollie_partner_id' => 'required',
            ]);
            $post['is_mollie_enabled'] = $request->is_mollie_enabled;
            $post['mollie_api_key']    = $request->mollie_api_key;
            $post['mollie_profile_id'] = $request->mollie_profile_id;
            $post['mollie_partner_id'] = $request->mollie_partner_id;
        } else {
            $post['is_mollie_enabled'] = 'off';
        }

        if (isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on') {
            $request->validate([
                'skrill_email' => 'required|email',
            ]);
            $post['is_skrill_enabled'] = $request->is_skrill_enabled;
            $post['skrill_email']      = $request->skrill_email;
        } else {
            $post['is_skrill_enabled'] = 'off';
        }

        if (isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on') {
            $request->validate([
                'coingate_mode' => 'required|string',
                'coingate_auth_token' => 'required|string',
            ]);

            $post['is_coingate_enabled'] = $request->is_coingate_enabled;
            $post['coingate_mode']       = $request->coingate_mode;
            $post['coingate_auth_token'] = $request->coingate_auth_token;
        } else {
            $post['is_coingate_enabled'] = 'off';
        }

        if (isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on') {
            $request->validate(
                [
                    'paymentwall_public_key' => 'required|string',
                    'paymentwall_secret_key' => 'required|string',
                ]
            );
            $post['is_paymentwall_enabled'] = $request->is_paymentwall_enabled;
            $post['paymentwall_public_key'] = $request->paymentwall_public_key;
            $post['paymentwall_secret_key'] = $request->paymentwall_secret_key;
        } else {
            $post['is_paymentwall_enabled'] = 'off';
        }

        if (isset($request->is_toyyibpay_enabled) && $request->is_toyyibpay_enabled == 'on') {
            $request->validate(
                [
                    'toyyibpay_secret_key' => 'required|string',
                    'category_code' => 'required|string',
                ]
            );
            $post['is_toyyibpay_enabled'] = $request->is_toyyibpay_enabled;
            $post['toyyibpay_secret_key'] = $request->toyyibpay_secret_key;
            $post['category_code'] = $request->category_code;
        } else {
            $post['is_toyyibpay_enabled'] = 'off';
        }

        if (isset($request->is_payfast_enabled) && $request->is_payfast_enabled == 'on') {

            $request->validate(
                [
                    'payfast_merchant_id' => 'required|string',
                    'payfast_merchant_key' => 'required|string',
                    'payfast_signature' => 'required|string',
                    'payfast_mode' => 'required',

                ]
            );

            $post['payfast_mode']       = $request->payfast_mode;
            $post['is_payfast_enabled'] = $request->is_payfast_enabled;
            $post['payfast_merchant_id'] = $request->payfast_merchant_id;
            $post['payfast_merchant_key'] = $request->payfast_merchant_key;
            $post['payfast_signature'] = $request->payfast_signature;
        } else {
            $post['is_payfast_enabled'] = 'off';
        }

        if (isset($request->is_bank_enabled) && $request->is_bank_enabled == 'on') {

            $request->validate(
                [
                    'is_bank_enabled' => 'required|string',
                    'bank_detail' => 'required|string'

                ]
            );

            $post['is_bank_enabled']       = $request->is_bank_enabled;
            $post['bank_detail']       = $request->bank_detail;
        } else {
            $post['is_bank_enabled'] = 'off';
        }

        if (isset($request->is_iyzipay_enabled) && $request->is_iyzipay_enabled == 'on') {

            $request->validate(
                [
                    'iyzipay_mode' => 'required',
                    'iyzipay_private_key' => 'required',
                    'iyzipay_secret_key' => 'required',
                ]
            );

            $post['is_iyzipay_enabled'] = $request->is_iyzipay_enabled;
            $post['iyzipay_mode']       = $request->iyzipay_mode;
            $post['iyzipay_private_key']  = $request->iyzipay_private_key;
            $post['iyzipay_secret_key'] = $request->iyzipay_secret_key;
        } else {
            $post['is_iyzipay_enabled'] = 'off';
        }

        if (isset($request->is_sspay_enabled) && $request->is_sspay_enabled == 'on') {

            $request->validate(
                [
                    'sspay_category_code' => 'required',
                    'sspay_secret_key' => 'required',
                ]
            );

            $post['is_sspay_enabled'] = $request->is_sspay_enabled;
            $post['sspay_category_code']       = $request->sspay_category_code;
            $post['sspay_secret_key']  = $request->sspay_secret_key;
        } else {
            $post['is_sspay_enabled'] = 'off';
        }

        if (isset($request->is_paytab_enabled) && $request->is_paytab_enabled == 'on') {

            $request->validate(
                [
                    'paytab_profile_id' => 'required',
                    'paytab_region' => 'required',
                    'paytab_server_key' => 'required',

                ]
            );

            $post['is_paytab_enabled'] = $request->is_paytab_enabled;
            $post['paytab_profile_id']       = $request->paytab_profile_id;
            $post['paytab_region']       = $request->paytab_region;
            $post['paytab_server_key']  = $request->paytab_server_key;
        } else {
            $post['is_paytab_enabled'] = 'off';
        }

        if (isset($request->is_benefit_enabled) && $request->is_benefit_enabled == 'on') {
            $request->validate(
                [
                    'benefit_api_key' => 'required',
                    'benefit_secret_key' => 'required',
                ]
            );

            $post['is_benefit_enabled'] = $request->is_benefit_enabled;
            $post['benefit_api_key']       = $request->benefit_api_key;
            $post['benefit_secret_key']       = $request->benefit_secret_key;
        } else {
            $post['is_benefit_enabled'] = 'off';
        }

        if (isset($request->is_cashfree_enabled) && $request->is_cashfree_enabled == 'on') {

            $request->validate(
                [
                    'cashfree_api_key' => 'required',
                    'cashfree_secret_key' => 'required',
                ]
            );

            $post['is_cashfree_enabled'] = $request->is_cashfree_enabled;
            $post['cashfree_api_key']       = $request->cashfree_api_key;
            $post['cashfree_secret_key']       = $request->cashfree_secret_key;
        } else {
            $post['is_cashfree_enabled'] = 'off';
        }

        if (isset($request->is_aamarpay_enabled) && $request->is_aamarpay_enabled == 'on') {
            $request->validate(
                [
                    'aamarpay_store_id' => 'required',
                    'aamarpay_signature_key' => 'required',
                    'aamarpay_description' => 'required',
                ]
            );

            $post['is_aamarpay_enabled'] = $request->is_aamarpay_enabled;
            $post['aamarpay_store_id']       = $request->aamarpay_store_id;
            $post['aamarpay_signature_key']       = $request->aamarpay_signature_key;
            $post['aamarpay_description']       = $request->aamarpay_description;
        } else {
            $post['is_aamarpay_enabled'] = 'off';
        }

        if (isset($request->is_paytr_enabled) && $request->is_paytr_enabled == 'on') {
            $request->validate(
                [
                    'paytr_merchant_id' => 'required',
                    'paytr_merchant_key' => 'required',
                    'paytr_merchant_salt' => 'required',
                ]
            );

            $post['is_paytr_enabled'] = $request->is_paytr_enabled;
            $post['paytr_merchant_id'] = $request->paytr_merchant_id;
            $post['paytr_merchant_key'] = $request->paytr_merchant_key;
            $post['paytr_merchant_salt'] = $request->paytr_merchant_salt;
        } else {
            $post['is_paytr_enabled'] = 'off';
        }

        if (isset($request->is_yookassa_enabled) && $request->is_yookassa_enabled == 'on') {
            $request->validate(
                [
                    'is_yookassa_enabled' => 'required',
                    'yookassa_shop_id' => 'required',
                    'yookassa_secret' => 'required',
                ]
            );

            $post['is_yookassa_enabled'] = $request->is_yookassa_enabled;
            $post['yookassa_shop_id'] = $request->yookassa_shop_id;
            $post['yookassa_secret'] = $request->yookassa_secret;
        } else {
            $post['is_yookassa_enabled'] = 'off';
        }

        if (isset($request->is_xendit_enabled) && $request->is_xendit_enabled == 'on') {
            $request->validate(
                [
                    'is_xendit_enabled' => 'required',
                    'xendit_api' => 'required',
                    'xendit_token' => 'required',

                ]
            );

            $post['is_xendit_enabled'] = $request->is_xendit_enabled;
            $post['xendit_token'] = $request->xendit_token;
            $post['xendit_api'] = $request->xendit_api;
        } else {
            $post['is_xendit_enabled'] = 'off';
        }

        if (isset($request->is_midtrans_enabled) && $request->is_midtrans_enabled == 'on') {
            $request->validate(
                [
                    'midtrans_mode' => 'required',
                    'is_midtrans_enabled' => 'required',
                    'midtrans_secret' => 'required',

                ]
            );

            $post['midtrans_mode']       = $request->midtrans_mode;
            $post['is_midtrans_enabled'] = $request->is_midtrans_enabled;
            $post['midtrans_secret'] = $request->midtrans_secret;
        } else {
            $post['is_midtrans_enabled'] = 'off';
        }

        if (isset($request->is_paiementpro_enabled) && $request->is_paiementpro_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'paiementpro_merchant_id'              => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_paiementpro_enabled']         = $request->is_paiementpro_enabled;
            $post['paiementpro_merchant_id']        = $request->paiementpro_merchant_id;
        } else {
            $post['is_paiementpro_enabled'] = 'off';
        }

        if (isset($request->is_nepalste_enabled) && $request->is_nepalste_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'nepalste_mode'              => 'required|string',
                    'nepalste_public_key'       => 'required|string',
                    'nepalste_secret_key'       => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_nepalste_enabled']        = $request->is_nepalste_enabled;
            $post['nepalste_mode']              = $request->nepalste_mode;
            $post['nepalste_public_key']        = $request->nepalste_public_key;
            $post['nepalste_secret_key']        = $request->nepalste_secret_key;
        } else {
            $post['is_nepalste_enabled'] = 'off';
        }

        if (isset($request->is_cinetpay_enabled) && $request->is_cinetpay_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'cinetpay_api_key'       => 'required|string',
                    'cinetpay_secret_key'       => 'required|string',
                    'cinetpay_site_id'       => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_cinetpay_enabled']         = $request->is_cinetpay_enabled;
            $post['cinetpay_api_key']            = $request->cinetpay_api_key;
            $post['cinetpay_secret_key']            = $request->cinetpay_secret_key;
            $post['cinetpay_site_id']            = $request->cinetpay_site_id;
        } else {
            $post['is_cinetpay_enabled'] = 'off';
        }

        if (isset($request->is_fedapay_enabled) && $request->is_fedapay_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'fedapay_mode'              => 'required|string',
                    'fedapay_public_key'        => 'required|string',
                    'fedapay_secret_key'        => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_fedapay_enabled']         = $request->is_fedapay_enabled;
            $post['fedapay_mode']               = $request->fedapay_mode;
            $post['fedapay_public_key']        = $request->fedapay_public_key;
            $post['fedapay_secret_key']    = $request->fedapay_secret_key;
        } else {
            $post['is_fedapay_enabled'] = 'off';
        }

        if (isset($request->is_payhere_enabled) && $request->is_payhere_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'payhere_mode'              => 'required|string',
                    'payhere_merchant_id'       => 'required|string',
                    'payhere_merchant_secret'   => 'required|string',
                    'payhere_app_id'            => 'required|string',
                    'payhere_app_secret'        => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_payhere_enabled']         = $request->is_payhere_enabled;
            $post['payhere_mode']               = $request->payhere_mode;
            $post['payhere_merchant_id']        = $request->payhere_merchant_id;
            $post['payhere_merchant_secret']    = $request->payhere_merchant_secret;
            $post['payhere_app_secret']         = $request->payhere_app_secret;
            $post['payhere_app_id']             = $request->payhere_app_id;
        } else {
            $post['is_payhere_enabled'] = 'off';
        }

        if (isset($request->is_tap_enabled) && $request->is_tap_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'company_tap_secret_key' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_tap_enabled'] = $request->is_tap_enabled;
            $post['company_tap_secret_key'] = $request->company_tap_secret_key;
        } else {
            $post['is_tap_enabled'] = 'off';
        }

        if (isset($request->is_authorizenet_enabled) && $request->is_authorizenet_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'authorizenet_mode'              => 'required',
                    'authorizenet_merchant_login_id'       => 'required',
                    'authorizenet_merchant_transaction_key'   => 'required',
                ]
            );
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $post['is_authorizenet_enabled']         = $request->is_authorizenet_enabled;
            $post['authorizenet_mode']               = $request->authorizenet_mode;
            $post['authorizenet_merchant_login_id']        = $request->authorizenet_merchant_login_id;
            $post['authorizenet_merchant_transaction_key']    = $request->authorizenet_merchant_transaction_key;
        } else {
            $post['is_authorizenet_enabled']         = 'off';
        }

        if (isset($request->is_khalti_enabled) && $request->is_khalti_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'khalti_mode'         => 'required',
                    'khalti_secret_key'   => 'required',
                    'khalti_public_key'   => 'required',
                ]
            );
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $post['is_khalti_enabled']    = $request->is_khalti_enabled;
            $post['khalti_mode']          = $request->khalti_mode;
            $post['khalti_secret_key']    = $request->khalti_secret_key;
            $post['khalti_public_key']    = $request->khalti_public_key;
        } else {
            $post['is_khalti_enabled']    = 'off';
        }

            if (isset($request->is_ozow_enabled) && $request->is_ozow_enabled == 'on') {

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'ozow_mode'         => 'required',
                        'ozow_site_key'   => 'required',
                        'ozow_private_key'   => 'required',
                        'ozow_api_key'   => 'required',
                    ]
                );
                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->getMessageBag()->first());
                }

                $post['is_ozow_enabled']    = $request->is_ozow_enabled;
                $post['ozow_mode']          = $request->ozow_mode;
                $post['ozow_site_key']    = $request->ozow_site_key;
                $post['ozow_private_key']    = $request->ozow_private_key;
                $post['ozow_api_key']    = $request->ozow_api_key;
            } else {
                $post['is_ozow_enabled']    = 'off';
            }

        foreach ($post as $key => $data) {

            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];
            \DB::insert(
                'insert into company_payment_settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                $arr
            );
        }

        return redirect()->back()->with('success', __('Payment setting successfully updated.'));
    }

    public function testMail(Request $request)
    {
        $user = \Auth::user();

        $data                      = [];
        $data['mail_driver']       = $request->mail_driver;
        $data['mail_host']         = $request->mail_host;
        $data['mail_port']         = $request->mail_port;
        $data['mail_username']     = $request->mail_username;
        $data['mail_password']     = $request->mail_password;
        $data['mail_encryption']   = $request->mail_encryption;
        $data['mail_from_address'] = $request->mail_from_address;
        $data['mail_from_name']    = $request->mail_from_name;

        return view('settings.test_mail', compact('data'));
    }

    public function testSendMail(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'mail_driver' => 'required',
                'mail_host' => 'required',
                'mail_port' => 'required',
                'mail_username' => 'required',
                'mail_password' => 'required',
                'mail_from_address' => 'required',
                'mail_from_name' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        try {
            config(
                [
                    'mail.driver' => $request->mail_driver,
                    'mail.host' => $request->mail_host,
                    'mail.port' => $request->mail_port,
                    'mail.encryption' => $request->mail_encryption,
                    'mail.username' => $request->mail_username,
                    'mail.password' => $request->mail_password,
                    'mail.from.address' => $request->mail_from_address,
                    'mail.from.name' => $request->mail_from_name,
                ]
            );
            Mail::to($request->email)->send(new testMail());
        } catch (\Exception $e) {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }

        return response()->json(
            [
                'is_success' => true,
                'message' => __('Email send Successfully'),
            ]
        );
    }

    public function adminPaymentSettings($request)
    {

        if (isset($request->is_stripe_enabled) && $request->is_stripe_enabled == 'on') {

            $request->validate(
                [
                    'stripe_key' => 'required|string|max:255',
                    'stripe_secret' => 'required|string|max:255',
                ]
            );

            $post['is_stripe_enabled'] = $request->is_stripe_enabled;
            $post['stripe_secret']     = $request->stripe_secret;
            $post['stripe_key']        = $request->stripe_key;
        } else {
            $post['is_stripe_enabled'] = 'off';
        }
        if (isset($request->is_paypal_enabled) && $request->is_paypal_enabled == 'on') {
            $request->validate(
                [
                    'paypal_mode' => 'required',
                    'paypal_client_id' => 'required',
                    'paypal_secret_key' => 'required',
                ]
            );

            $post['is_paypal_enabled'] = $request->is_paypal_enabled;
            $post['paypal_mode']       = $request->paypal_mode;
            $post['paypal_client_id']  = $request->paypal_client_id;
            $post['paypal_secret_key'] = $request->paypal_secret_key;
        } else {
            $post['is_paypal_enabled'] = 'off';
        }

        if (isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on') {
            $request->validate(
                [
                    'paystack_public_key' => 'required|string',
                    'paystack_secret_key' => 'required|string',
                ]
            );
            $post['is_paystack_enabled'] = $request->is_paystack_enabled;
            $post['paystack_public_key'] = $request->paystack_public_key;
            $post['paystack_secret_key'] = $request->paystack_secret_key;
        } else {
            $post['is_paystack_enabled'] = 'off';
        }

        if (isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on') {
            $request->validate(
                [
                    'flutterwave_public_key' => 'required|string',
                    'flutterwave_secret_key' => 'required|string',
                ]
            );
            $post['is_flutterwave_enabled'] = $request->is_flutterwave_enabled;
            $post['flutterwave_public_key'] = $request->flutterwave_public_key;
            $post['flutterwave_secret_key'] = $request->flutterwave_secret_key;
        } else {
            $post['is_flutterwave_enabled'] = 'off';
        }

        if (isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on') {
            $request->validate(
                [
                    'razorpay_public_key' => 'required|string',
                    'razorpay_secret_key' => 'required|string',
                ]
            );
            $post['is_razorpay_enabled'] = $request->is_razorpay_enabled;
            $post['razorpay_public_key'] = $request->razorpay_public_key;
            $post['razorpay_secret_key'] = $request->razorpay_secret_key;
        } else {
            $post['is_razorpay_enabled'] = 'off';
        }
        if (isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on') {
            $request->validate(
                [
                    'mercado_access_token' => 'required|string',
                ]
            );
            $post['is_mercado_enabled'] = $request->is_mercado_enabled;
            $post['mercado_access_token']     = $request->mercado_access_token;
        } else {
            $post['is_mercado_enabled'] = 'off';
        }

        if (isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on') {
            $request->validate(
                [
                    'paytm_mode' => 'required',
                    'paytm_merchant_id' => 'required|string',
                    'paytm_merchant_key' => 'required|string',
                    'paytm_industry_type' => 'required|string',
                ]
            );
            $post['is_paytm_enabled']    = $request->is_paytm_enabled;
            $post['paytm_mode']          = $request->paytm_mode;
            $post['paytm_merchant_id']   = $request->paytm_merchant_id;
            $post['paytm_merchant_key']  = $request->paytm_merchant_key;
            $post['paytm_industry_type'] = $request->paytm_industry_type;
        } else {
            $post['is_paytm_enabled'] = 'off';
        }
        if (isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on') {
            $request->validate(
                [
                    'mollie_api_key' => 'required|string',
                    'mollie_profile_id' => 'required|string',
                    'mollie_partner_id' => 'required',
                ]
            );
            $post['is_mollie_enabled'] = $request->is_mollie_enabled;
            $post['mollie_api_key']    = $request->mollie_api_key;
            $post['mollie_profile_id'] = $request->mollie_profile_id;
            $post['mollie_partner_id'] = $request->mollie_partner_id;
        } else {
            $post['is_mollie_enabled'] = 'off';
        }

        if (isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on') {
            $request->validate(
                [
                    'skrill_email' => 'required|email',
                ]
            );
            $post['is_skrill_enabled'] = $request->is_skrill_enabled;
            $post['skrill_email']      = $request->skrill_email;
        } else {
            $post['is_skrill_enabled'] = 'off';
        }

        if (isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on') {
            $request->validate(
                [
                    'coingate_mode' => 'required|string',
                    'coingate_auth_token' => 'required|string',
                ]
            );

            $post['is_coingate_enabled'] = $request->is_coingate_enabled;
            $post['coingate_mode']       = $request->coingate_mode;
            $post['coingate_auth_token'] = $request->coingate_auth_token;
        } else {
            $post['is_coingate_enabled'] = 'off';
        }

        if (isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on') {

            $request->validate(
                [
                    'paymentwall_public_key' => 'required|string',
                    'paymentwall_private_key' => 'required|string',
                ]
            );

            $post['is_paymentwall_enabled'] = $request->is_paymentwall_enabled;
            $post['paymentwall_public_key'] = $request->paymentwall_public_key;
            $post['paymentwall_private_key'] = $request->paymentwall_private_key;
        } else {
            $post['is_paymentwall_enabled'] = 'off';
        }

        if (isset($request->is_toyyibpay_enabled) && $request->is_toyyibpay_enabled == 'on') {

            $request->validate(
                [
                    'toyyibpay_secret_key' => 'required|string',
                    'category_code' => 'required|string',
                ]
            );

            $post['is_toyyibpay_enabled'] = $request->is_toyyibpay_enabled;
            $post['toyyibpay_secret_key'] = $request->toyyibpay_secret_key;
            $post['category_code'] = $request->category_code;
        } else {
            $post['is_toyyibpay_enabled'] = 'off';
        }

        if (isset($request->is_payfast_enabled) && $request->is_payfast_enabled == 'on') {

            $request->validate(
                [
                    'payfast_merchant_id' => 'required|string',
                    'payfast_merchant_key' => 'required|string',
                    'payfast_signature' => 'required|string',
                    'payfast_mode' => 'required',
                ]
            );
            $post['payfast_mode']         = $request->payfast_mode;
            $post['is_payfast_enabled']   = $request->is_payfast_enabled;
            $post['payfast_merchant_id']  = $request->payfast_merchant_id;
            $post['payfast_merchant_key'] = $request->payfast_merchant_key;
            $post['payfast_signature']    = $request->payfast_signature;
        } else {
            $post['is_payfast_enabled'] = 'off';
        }

        if (isset($request->is_manually_enabled) && $request->is_manually_enabled == 'on') {

            $request->validate(
                [
                    'is_manually_enabled' => 'required|string',
                ]
            );

            $post['is_manually_enabled']       = $request->is_manually_enabled;
        } else {
            $post['is_manually_enabled'] = 'off';
        }

        if (isset($request->is_bank_enabled) && $request->is_bank_enabled == 'on') {

            $request->validate(
                [
                    'is_bank_enabled' => 'required|string',
                    'bank_detail' => 'required|string'

                ]
            );

            $post['is_bank_enabled']       = $request->is_bank_enabled;

            $post['bank_detail']       = $request->bank_detail;
        } else {
            $post['is_bank_enabled'] = 'off';
        }

        if (isset($request->is_iyzipay_enabled) && $request->is_iyzipay_enabled == 'on') {

            $request->validate(
                [
                    'iyzipay_mode' => 'required',
                    'iyzipay_private_key' => 'required',
                    'iyzipay_secret_key' => 'required',
                ]
            );

            $post['is_iyzipay_enabled'] = $request->is_iyzipay_enabled;
            $post['iyzipay_mode']       = $request->iyzipay_mode;
            $post['iyzipay_private_key']  = $request->iyzipay_private_key;
            $post['iyzipay_secret_key'] = $request->iyzipay_secret_key;
        } else {
            $post['is_iyzipay_enabled'] = 'off';
        }

        if (isset($request->is_sspay_enabled) && $request->is_sspay_enabled == 'on') {

            $request->validate(
                [
                    'sspay_category_code' => 'required',
                    'sspay_secret_key' => 'required',
                ]
            );

            $post['is_sspay_enabled'] = $request->is_sspay_enabled;
            $post['sspay_category_code']       = $request->sspay_category_code;
            $post['sspay_secret_key']  = $request->sspay_secret_key;
        } else {
            $post['is_sspay_enabled'] = 'off';
        }

        if (isset($request->is_paytab_enabled) && $request->is_paytab_enabled == 'on') {

            $request->validate(
                [
                    'paytab_profile_id' => 'required',
                    'paytab_region' => 'required',
                    'paytab_server_key' => 'required',

                ]
            );

            $post['is_paytab_enabled'] = $request->is_paytab_enabled;
            $post['paytab_profile_id']       = $request->paytab_profile_id;
            $post['paytab_region']       = $request->paytab_region;
            $post['paytab_server_key']  = $request->paytab_server_key;
        } else {
            $post['is_paytab_enabled'] = 'off';
        }

        if (isset($request->is_benefit_enabled) && $request->is_benefit_enabled == 'on') {
            $request->validate(
                [
                    'benefit_api_key' => 'required',
                    'benefit_secret_key' => 'required',
                ]
            );

            $post['is_benefit_enabled'] = $request->is_benefit_enabled;
            $post['benefit_api_key']       = $request->benefit_api_key;
            $post['benefit_secret_key']       = $request->benefit_secret_key;
        } else {
            $post['is_benefit_enabled'] = 'off';
        }

        if (isset($request->is_cashfree_enabled) && $request->is_cashfree_enabled == 'on') {
            $request->validate(
                [
                    'cashfree_api_key' => 'required',
                    'cashfree_secret_key' => 'required',
                ]
            );

            $post['is_cashfree_enabled'] = $request->is_cashfree_enabled;
            $post['cashfree_api_key']       = $request->cashfree_api_key;
            $post['cashfree_secret_key']       = $request->cashfree_secret_key;
        } else {
            $post['is_cashfree_enabled'] = 'off';
        }

        if (isset($request->is_aamarpay_enabled) && $request->is_aamarpay_enabled == 'on') {
            $request->validate(
                [
                    'aamarpay_store_id' => 'required',
                    'aamarpay_signature_key' => 'required',
                    'aamarpay_description' => 'required',
                ]
            );

            $post['is_aamarpay_enabled'] = $request->is_aamarpay_enabled;
            $post['aamarpay_store_id']       = $request->aamarpay_store_id;
            $post['aamarpay_signature_key']       = $request->aamarpay_signature_key;
            $post['aamarpay_description']       = $request->aamarpay_description;
        } else {
            $post['is_aamarpay_enabled'] = 'off';
        }

        if (isset($request->is_paytr_enabled) && $request->is_paytr_enabled == 'on') {
            $request->validate(
                [
                    'paytr_merchant_id' => 'required',
                    'paytr_merchant_key' => 'required',
                    'paytr_merchant_salt' => 'required',
                ]
            );

            $post['is_paytr_enabled'] = $request->is_paytr_enabled;
            $post['paytr_merchant_id'] = $request->paytr_merchant_id;
            $post['paytr_merchant_key'] = $request->paytr_merchant_key;
            $post['paytr_merchant_salt'] = $request->paytr_merchant_salt;
        } else {
            $post['is_paytr_enabled'] = 'off';
        }

        if (isset($request->is_yookassa_enabled) && $request->is_yookassa_enabled == 'on') {
            $request->validate(
                [
                    'is_yookassa_enabled' => 'required',
                    'yookassa_shop_id' => 'required',
                    'yookassa_secret' => 'required',
                ]
            );

            $post['is_yookassa_enabled'] = $request->is_yookassa_enabled;
            $post['yookassa_shop_id'] = $request->yookassa_shop_id;
            $post['yookassa_secret'] = $request->yookassa_secret;
        } else {
            $post['is_yookassa_enabled'] = 'off';
        }

        if (isset($request->is_xendit_enabled) && $request->is_xendit_enabled == 'on') {
            $request->validate(
                [
                    'is_xendit_enabled' => 'required',
                    'xendit_api' => 'required',
                    'xendit_token' => 'required',

                ]
            );

            $post['is_xendit_enabled'] = $request->is_xendit_enabled;
            $post['xendit_token'] = $request->xendit_token;
            $post['xendit_api'] = $request->xendit_api;
        } else {
            $post['is_xendit_enabled'] = 'off';
        }

        if (isset($request->is_midtrans_enabled) && $request->is_midtrans_enabled == 'on') {
            $request->validate(
                [
                    'midtrans_mode' => 'required',
                    'is_midtrans_enabled' => 'required',
                    'midtrans_secret' => 'required',

                ]
            );

            $post['midtrans_mode']       = $request->midtrans_mode;
            $post['is_midtrans_enabled'] = $request->is_midtrans_enabled;
            $post['midtrans_secret'] = $request->midtrans_secret;
        } else {
            $post['is_midtrans_enabled'] = 'off';
        }

        if (isset($request->is_paiementpro_enabled) && $request->is_paiementpro_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'paiementpro_merchant_id'              => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_paiementpro_enabled']         = $request->is_paiementpro_enabled;
            $post['paiementpro_merchant_id']        = $request->paiementpro_merchant_id;
        } else {
            $post['is_paiementpro_enabled'] = 'off';
        }

        if (isset($request->is_nepalste_enabled) && $request->is_nepalste_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'nepalste_mode'              => 'required|string',
                    'nepalste_public_key'       => 'required|string',
                    'nepalste_secret_key'       => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_nepalste_enabled']        = $request->is_nepalste_enabled;
            $post['nepalste_mode']              = $request->nepalste_mode;
            $post['nepalste_public_key']        = $request->nepalste_public_key;
            $post['nepalste_secret_key']        = $request->nepalste_secret_key;
        } else {
            $post['is_nepalste_enabled'] = 'off';
        }

        if (isset($request->is_cinetpay_enabled) && $request->is_cinetpay_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'cinetpay_api_key'       => 'required|string',
                    'cinetpay_secret_key'       => 'required|string',
                    'cinetpay_site_id'       => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_cinetpay_enabled']         = $request->is_cinetpay_enabled;
            $post['cinetpay_api_key']            = $request->cinetpay_api_key;
            $post['cinetpay_secret_key']            = $request->cinetpay_secret_key;
            $post['cinetpay_site_id']            = $request->cinetpay_site_id;
        } else {
            $post['is_cinetpay_enabled'] = 'off';
        }

        if (isset($request->is_fedapay_enabled) && $request->is_fedapay_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'fedapay_mode'              => 'required|string',
                    'fedapay_public_key'        => 'required|string',
                    'fedapay_secret_key'        => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_fedapay_enabled']         = $request->is_fedapay_enabled;
            $post['fedapay_mode']               = $request->fedapay_mode;
            $post['fedapay_public_key']        = $request->fedapay_public_key;
            $post['fedapay_secret_key']    = $request->fedapay_secret_key;
        } else {
            $post['is_fedapay_enabled'] = 'off';
        }

        if (isset($request->is_payhere_enabled) && $request->is_payhere_enabled == 'on') {
            $validator = Validator::make(
                $request->all(),
                [
                    'payhere_mode'              => 'required|string',
                    'payhere_merchant_id'       => 'required|string',
                    'payhere_merchant_secret'   => 'required|string',
                    'payhere_app_id'            => 'required|string',
                    'payhere_app_secret'        => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_payhere_enabled']         = $request->is_payhere_enabled;
            $post['payhere_mode']               = $request->payhere_mode;
            $post['payhere_merchant_id']        = $request->payhere_merchant_id;
            $post['payhere_merchant_secret']    = $request->payhere_merchant_secret;
            $post['payhere_app_secret']         = $request->payhere_app_secret;
            $post['payhere_app_id']             = $request->payhere_app_id;
        } else {
            $post['is_payhere_enabled'] = 'off';
        }

        if (isset($request->is_tap_enabled) && $request->is_tap_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'company_tap_secret_key' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_tap_enabled'] = $request->is_tap_enabled;
            $post['company_tap_secret_key'] = $request->company_tap_secret_key;
        } else {
            $post['is_tap_enabled'] = 'off';
        }

        if (isset($request->is_authorizenet_enabled) && $request->is_authorizenet_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'authorizenet_mode'              => 'required',
                    'authorizenet_merchant_login_id'       => 'required',
                    'authorizenet_merchant_transaction_key'   => 'required',
                ]
            );
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $post['is_authorizenet_enabled']         = $request->is_authorizenet_enabled;
            $post['authorizenet_mode']               = $request->authorizenet_mode;
            $post['authorizenet_merchant_login_id']        = $request->authorizenet_merchant_login_id;
            $post['authorizenet_merchant_transaction_key']    = $request->authorizenet_merchant_transaction_key;
        } else {
            $post['is_authorizenet_enabled']         = 'off';
        }

        if (isset($request->is_khalti_enabled) && $request->is_khalti_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'khalti_mode'         => 'required',
                    'khalti_secret_key'   => 'required',
                    'khalti_public_key'   => 'required',
                ]
            );
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $post['is_khalti_enabled']    = $request->is_khalti_enabled;
            $post['khalti_mode']          = $request->khalti_mode;
            $post['khalti_secret_key']    = $request->khalti_secret_key;
            $post['khalti_public_key']    = $request->khalti_public_key;
        } else {
            $post['is_khalti_enabled']    = 'off';
        }

        if (isset($request->is_ozow_enabled) && $request->is_ozow_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'ozow_mode'         => 'required',
                    'ozow_site_key'      => 'required',
                    'ozow_private_key'   => 'required',
                    'ozow_api_key'       => 'required',
                ]
            );
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $post['is_ozow_enabled']    = $request->is_ozow_enabled;
            $post['ozow_mode']          = $request->ozow_mode;
            $post['ozow_site_key']    = $request->ozow_site_key;
            $post['ozow_private_key']    = $request->ozow_private_key;
            $post['ozow_api_key']    = $request->ozow_api_key;
        } else {
            $post['is_ozow_enabled']    = 'off';
        }

        foreach ($post as $key => $data) {

            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];
            \DB::insert(
                'insert into admin_payment_settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                $arr
            );
        }
    }

    public function saveTwilioSettings(Request $request)
    {
        $post = [];
        $post['twilio_sid'] = $request->input('twilio_sid');
        $post['twilio_token'] = $request->input('twilio_token');
        $post['twilio_from'] = $request->input('twilio_from');
        $post['customer_notification'] = $request->has('customer_notification') ? $request->input('customer_notification') : 0;
        $post['vender_notification'] = $request->has('vender_notification') ? $request->input('vender_notification') : 0;
        $post['invoice_notification'] = $request->has('invoice_notification') ? $request->input('invoice_notification') : 0;
        $post['revenue_notification'] = $request->has('revenue_notification') ? $request->input('revenue_notification') : 0;
        $post['bill_notification'] = $request->has('bill_notification') ? $request->input('bill_notification') : 0;
        $post['proposal_notification'] = $request->has('proposal_notification') ? $request->input('proposal_notification') : 0;
        $post['payment_notification'] = $request->has('payment_notification') ? $request->input('payment_notification') : 0;
        $post['reminder_notification'] = $request->has('reminder_notification') ? $request->input('reminder_notification') : 0;

        if (isset($post) && !empty($post) && count($post) > 0) {
            $created_at = $updated_at = date('Y-m-d H:i:s');

            foreach ($post as $key => $data) {
                DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ',
                    [
                        $data,
                        $key,
                        Auth::user()->id,
                        $created_at,
                        $updated_at,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', __('Telegram updated successfully.'));
    }

    public function recaptchaSettingStore(Request $request)
    {
        $rules = [];

        if ($request->recaptcha_module == 'yes') {
            $rules['google_recaptcha_key'] = 'required|string|max:50';
            $rules['google_recaptcha_secret'] = 'required|string|max:50';
            $rules['google_recaptcha_version'] = 'required|string|max:50';
        }

        $validator = \Validator::make(
            $request->all(),
            $rules
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $post = $request->all();
        unset($post['_token']);

        if (!isset($post['recaptcha_module'])) {
            $post['recaptcha_module'] = 'off';
        }

        $settings = Utility::settings();
        foreach ($post as $key => $data) {
            if (in_array($key, array_keys($settings))) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                    [
                        $data,
                        $key,
                        \Auth::user()->creatorId(),
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s'),
                    ]
                );
            }
        }

        return redirect()->back()->with('success', __('Recaptcha Settings updated successfully'));
    }

    public function storageSettingStore(Request $request)
    {

        if (isset($request->storage_setting) && $request->storage_setting == 'local') {

            $request->validate(
                [

                    'local_storage_validation' => 'required',
                    'local_storage_max_upload_size' => 'required',
                ]
            );

            $post['storage_setting'] = $request->storage_setting;
            $local_storage_validation = implode(',', $request->local_storage_validation);
            $post['local_storage_validation'] = $local_storage_validation;
            $post['local_storage_max_upload_size'] = $request->local_storage_max_upload_size;
        }

        if (isset($request->storage_setting) && $request->storage_setting == 's3') {
            $request->validate(
                [
                    's3_key'                  => 'required',
                    's3_secret'               => 'required',
                    's3_region'               => 'required',
                    's3_bucket'               => 'required',
                    's3_url'                  => 'required',
                    's3_endpoint'             => 'required',
                    's3_max_upload_size'      => 'required',
                    's3_storage_validation'   => 'required',
                ]
            );
            $post['storage_setting']            = $request->storage_setting;
            $post['s3_key']                     = $request->s3_key;
            $post['s3_secret']                  = $request->s3_secret;
            $post['s3_region']                  = $request->s3_region;
            $post['s3_bucket']                  = $request->s3_bucket;
            $post['s3_url']                     = $request->s3_url;
            $post['s3_endpoint']                = $request->s3_endpoint;
            $post['s3_max_upload_size']         = $request->s3_max_upload_size;
            $s3_storage_validation              = implode(',', $request->s3_storage_validation);
            $post['s3_storage_validation']      = $s3_storage_validation;
        }

        if (isset($request->storage_setting) && $request->storage_setting == 'wasabi') {
            $request->validate(
                [
                    'wasabi_key'                    => 'required',
                    'wasabi_secret'                 => 'required',
                    'wasabi_region'                 => 'required',
                    'wasabi_bucket'                 => 'required',
                    'wasabi_url'                    => 'required',
                    'wasabi_root'                   => 'required',
                    'wasabi_max_upload_size'        => 'required',
                    'wasabi_storage_validation'     => 'required',
                ]
            );
            $post['storage_setting']            = $request->storage_setting;
            $post['wasabi_key']                 = $request->wasabi_key;
            $post['wasabi_secret']              = $request->wasabi_secret;
            $post['wasabi_region']              = $request->wasabi_region;
            $post['wasabi_bucket']              = $request->wasabi_bucket;
            $post['wasabi_url']                 = $request->wasabi_url;
            $post['wasabi_root']                = $request->wasabi_root;
            $post['wasabi_max_upload_size']     = $request->wasabi_max_upload_size;
            $wasabi_storage_validation          = implode(',', $request->wasabi_storage_validation);
            $post['wasabi_storage_validation']  = $wasabi_storage_validation;
        }

        foreach ($post as $key => $data) {

            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];

            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                $arr
            );
        }

        return redirect()->back()->with('success', 'Storage setting successfully updated.');
    }

    public function SeoSettings(Request $request)
    {

        $validator = \Validator::make(
            $request->all(),
            [
                'meta_keywords' => 'required',
                'meta_description' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if (!empty($request->meta_image)) {
            if ($request->meta_image) {
                $path = storage_path('uploads/metaevent/' . Utility::settings()['meta_image']);

                if (!empty($path)) {
                    if (file_exists($path)) {
                        \File::delete($path);
                    }
                }
            }

            $img_name = time() . '_' . 'meta_image.png';

            $dir = 'uploads/metaevent';

            $validation = [

                'max:' . '20480',
            ];

            $path = Utility::upload_file($request, 'meta_image', $img_name, $dir, $validation);

            if ($path['flag'] == 1) {
                $logo_dark = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }

            $post['meta_image']  = $img_name;
        }

        $post['meta_keywords']            = $request->meta_keywords;
        $post['meta_description']         = $request->meta_description;

        foreach ($post as $key => $data) {
            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];

            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                $arr
            );
        }

        return redirect()->back()->with('success', 'SEO setting successfully updated.');
    }

    public function CookieConsent(Request $request)
    {
        if ($request['cookie']) {
            $settings = Utility::cookies();

            if ($settings['enable_cookie'] == "on" && $settings['cookie_logging'] == "on") {
                $allowed_levels = ['necessary', 'analytics', 'targeting'];
                $levels = array_filter($request['cookie'], function ($level) use ($allowed_levels) {
                    return in_array($level, $allowed_levels);
                });
                $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                $browser_name = $whichbrowser->browser->name ?? null;
                $os_name = $whichbrowser->os->name ?? null;
                $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
                $device_type = Utility::get_device_type($_SERVER['HTTP_USER_AGENT']);

                $ip = $_SERVER['REMOTE_ADDR'];
                $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));

                $date = (new \DateTime())->format('Y-m-d');
                $time = (new \DateTime())->format('H:i:s') . ' UTC';

                $new_line = implode(',', [
                    $ip, $date, $time, json_encode($request['cookie']), $device_type, $browser_language, $browser_name, $os_name,
                    isset($query) ? $query['country'] : '', isset($query) ? $query['region'] : '', isset($query) ? $query['regionName'] : '', isset($query) ? $query['city'] : '', isset($query) ? $query['zip'] : '', isset($query) ? $query['lat'] : '', isset($query) ? $query['lon'] : ''
                ]);

                if (!file_exists(storage_path() . '/uploads/sample/data.csv')) {

                    $first_line = 'IP,Date,Time,Accepted cookies,Device type,Browser language,Browser name,OS Name,Country,Region,RegionName,City,Zipcode,Lat,Lon';
                    file_put_contents(storage_path() . '/uploads/sample/data.csv', $first_line . PHP_EOL, FILE_APPEND | LOCK_EX);
                }
                file_put_contents(storage_path() . '/uploads/sample/data.csv', $new_line . PHP_EOL, FILE_APPEND | LOCK_EX);

                return response()->json('success');
            }
            return response()->json('error');
        }
        return redirect()->back();
    }

    public function saveCookieSettings(Request $request)
    {

        $validator = \Validator::make(
            $request->all(),
            [
                'cookie_title' => 'required',
                'cookie_description' => 'required',
                'strictly_cookie_title' => 'required',
                'strictly_cookie_description' => 'required',
                'more_information_title' => 'required',
                'contactus_url' => 'required',
            ]
        );

        $post = $request->all();

        unset($post['_token']);

        if ($request->enable_cookie) {
            $post['enable_cookie'] = 'on';
        } else {
            $post['enable_cookie'] = 'off';
        }
        if ($request->cookie_logging) {
            $post['cookie_logging'] = 'on';
        } else {

            $post['cookie_logging'] = 'off';
        }

        if ($post['enable_cookie'] == 'on') {

            $post['cookie_title']            = $request->cookie_title;
            $post['cookie_description']            = $request->cookie_description;
            $post['strictly_cookie_title']            = $request->strictly_cookie_title;
            $post['strictly_cookie_description']            = $request->strictly_cookie_description;
            $post['more_information_title']            = $request->more_information_title;
            $post['contactus_url']            = $request->contactus_url;
        }
        $settings = Utility::cookies();

        foreach ($post as $key => $data) {

            if (in_array($key, array_keys($settings))) {

                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->creatorId(),
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s'),
                    ]
                );
            }
        }
        return redirect()->back()->with('success', 'Cookie setting successfully saved.');
    }

    public function chatgptkey(Request $request)
    {
        if (\Auth::user()->type == 'super admin') {
            $user = \Auth::user();
            if (!empty($request->chatgpt_key)) {
                $post = $request->all();
                $post['chatgpt_key'] = $request->chatgpt_key;
                $post['chatgpt_model_name'] = $request->chatgpt_model_name;

                unset($post['_token']);
                foreach ($post as $key => $data) {

                    $settings = Utility::settings();

                    if (in_array($key, array_keys($settings))) {

                        \DB::insert(
                            'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                            [
                                $data,
                                $key,
                                \Auth::user()->creatorId(),
                                date('Y-m-d H:i:s'),
                                date('Y-m-d H:i:s'),
                            ]
                        );
                    }
                }
            }
            return redirect()->back()->with('success', __('Chatgpykey successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveCompanyEmailSetting(Request $request)
    {
        $request->validate(
            [
                'mail_driver' => 'required|string|max:255',
                'mail_host' => 'required|string|max:255',
                'mail_port' => 'required|string|max:255',
                'mail_username' => 'required|string|max:255',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|string|max:255',
                'mail_from_address' => 'required|string|max:255',
                'mail_from_name' => 'required|string|max:255',
            ]
        );

        $post = $request->all();
        unset($post['_token']);

        $settings = Utility::settings();
        foreach ($post as $key => $data) {
            if (in_array($key, array_keys($settings))) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->creatorId(),
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s'),
                    ]
                );
            }
        }

        return redirect()->back()->with('success', __('Email setting successfully updated.'));
    }
}
