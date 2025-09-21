<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Twilio\Rest\Client;
use App\Mail\CommonEmailTemplate;
use Illuminate\Support\Facades\Mail;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ReferralTransaction;
use App\Models\ReferralSetting;

class Utility extends Model
{
    private static $taxes = NULL;
    private static $taxsData = NULL;
    private static $taxRateData = NULL;
    private static $adminSetting = NULL;
    private static $Setting = NULL;
    private static $languageSetting = NULL;
    private static $getRatingData = NULL;

    public static function clearSettingsCache()
    {
        self::$Setting = null;
        self::$settingById = null;

        // Clear Laravel application cache if needed
        try {
            \Cache::forget('settings');
            \Cache::tags(['settings'])->flush();
        } catch (\Exception $e) {
            // Cache clearing failed but continue
        }
    }

    public static function settings()
    {
        if (is_null(self::$Setting)) {

            $data = DB::table('settings');
            if (\Auth::check()) {
                $userId = \Auth::user()->creatorId();
                $data = $data->where('created_by', '=', $userId);
            } else {
                $data = $data->where('created_by', '=', 1);
            }
            $data = $data->get();
            self::$Setting = $data;
        }
        $data = self::$Setting;
        $settings = [
            "site_currency" => "NGN",
            "site_currency_symbol" => "₦",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "#INVO",
            "journal_prefix" => "#JUR",
            "invoice_color" => "ffffff",
            "proposal_prefix" => "#PROP",
            "retainer_prefix" => "#RET",
            "employee_prefix" => "#EMP",
            "proposal_color" => "ffffff",
            "proposal_font" => "Lato",
            "retainer_color" => "ffffff",
            "retainer_font" => "Inter",
            "bill_prefix" => "#BILL",
            "bill_color" => "ffffff",
            "proposal_logo" => "2_proposal_logo.png",
            "retainer_logo" => "2_retainer_logo.png",
            "invoice_logo" => "2_invoice_logo.png",
            "bill_logo" => "2_bill_logo.png",
            "customer_prefix" => "#CUST",
            "vender_prefix" => "#VEND",
            "contract_prefix" => "#CON",
            "contract_template" => 'template1',
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "retainer_template" => "template1",
            "registration_number" => "",
            "tax_number" => "on",
            "vat_number" => "",
            "default_language" => "en",
            "company_default_language" => "en",
            "enable_stripe" => "",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_type" => "",
            "shipping_display" => "on",
            "journal_prefix" => "#JUR",
            "display_landing_page" => "on",
            "title_text" => "",
            "footer_text" => "",
            // 'gdpr_cookie' => " ",
            "enable_chatgpt" => "",
            "chatgpt_key" => "",
            "chatgpt_model_name" => "",
            'cookie_text' => "",
            "twilio_sid" => "",
            "twilio_token" => "",
            "twilio_from" => "",
            "enable_signup" => "on",
            "invoice_starting_number" => "1",
            "proposal_starting_number" => "1",
            "bill_starting_number" => "1",
            "dark_logo" => "logo-dark.png",
            "light_logo" => "logo-light.png",
            "company_logo_light" => "logo-light.png",
            "company_logo_dark" => "logo-dark.png",
            "company_favicon" => "",
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "color" => 'theme-3',
            "SITE_RTL" => "off",
            'color_flag' => 'false',
            "retainer_starting_number" => "1",
            "storage_setting" => "",
            "local_storage_validation" => "",
            "local_storage_max_upload_size" => "",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url" => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
            "email_verification" => "off",
            "meta_image" => "",
            'enable_cookie' => 'on',
            'necessary_cookies' => 'on',
            'cookie_logging' => 'on',
            'cookie_title' => 'We use cookies!',
            'cookie_description' => 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it',
            'strictly_cookie_title' => 'Strictly necessary cookies',
            'strictly_cookie_description' => 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
            'more_information_description' => 'For any queries in relation to our policy on cookies and your choices, please',
            "more_information_title" => "",
            'contactus_url' => '#',

            'mail_driver' => '',
            'mail_host' => '',
            'mail_port' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => '',
            'mail_from_address' => '',
            'mail_from_name' => '',

            'recaptcha_module' => '',
            'google_recaptcha_key' => '',
            'google_recaptcha_secret' => '',
            'google_recaptcha_version' => '',

            'qr_display' => '',
            'retainer_qr_display' => '',
            'invoice_qr_display' => '',
            'bill_qr_display' => '',
            'payslip_name' => '',
            'payslip_salary_month' => '',
            'payslip_url' => '',
        ];


        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    private static $cookie = null;

    public static function cookies()
    {
        if (is_null(self::$cookie)) {
            $data = DB::table('settings');
            if (\Auth::check()) {
                $userId = \Auth::user()->creatorId();
                $data = $data->where('created_by', '=', $userId);
            } else {
                $data = $data->where('created_by', '=', 1);
            }
            self::$cookie = $data->get();
        }
        $cookies = [
            'enable_cookie' => 'on',
            'necessary_cookies' => 'on',
            'cookie_logging' => 'on',
            'cookie_title' => 'We use cookies!',
            'cookie_description' => 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it',
            'strictly_cookie_title' => 'Strictly necessary cookies',
            'strictly_cookie_description' => 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
            'more_information_description' => 'For any queries in relation to our policy on cookies and your choices, please',
            "more_information_title" => "",
            'contactus_url' => '#',
        ];

        if (!is_null(self::$cookie)) {
            foreach (self::$cookie as $row) {
                if (array_key_exists($row->name, $cookies)) {
                    if ($row->value) {
                        $cookies[$row->name] = $row->value;
                    }
                }
            }
        }

        return $cookies;
    }


    private static $settingById = null;
    public static function settingsById($id)
    {
        if (is_null(self::$settingById)) {
            $data = DB::table('settings');
            $data = $data->where('created_by', '=', $id);
            $data = $data->get();
            self::$settingById = $data;
        }
        $data = self::$settingById;

        $settings = [
            "site_currency" => "USD",
            "currency" => "USD",
            "currency_symbol" => "$",
            "site_currency_symbol" => "$",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "#INVO",
            "journal_prefix" => "#JUR",
            "invoice_color" => "ffffff",
            "proposal_prefix" => "#PROP",
            "proposal_color" => "ffffff",
            "proposal_font" => "Lato",
            "proposal_logo" => "2_proposal_logo.png",
            "retainer_logo" => "2_retainer_logo.png",
            "invoice_logo" => "2_invoice_logo.png",
            "bill_logo" => "2_bill_logo.png",
            "retainer_color" => "ffffff",
            "retainer_font" => "Inter",
            "bill_prefix" => "#BILL",
            "bill_color" => "ffffff",
            "customer_prefix" => "#CUST",
            "vender_prefix" => "#VEND",
            "contract_prefix" => "#CON",
            "retainer_prefix" => "#RET",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "retainer_template" => "template1",
            "contract_template" => "template1",
            "registration_number" => "",
            "vat_number" => "",
            "default_language" => "en",
            "enable_stripe" => "",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_number" => "on",
            "tax_type" => "",
            "shipping_display" => "on",
            "journal_prefix" => "#JUR",
            "display_landing_page" => "on",
            "title_text" => "",
            // 'gdpr_cookie' => "off",
            'cookie_text' => "",
            "twilio_sid" => "",
            "twilio_token" => "",
            "twilio_from" => "",
            "dark_logo" => "logo-dark.png",
            "light_logo" => "logo-light.png",
            "company_logo_light" => "logo-light.png",
            "company_logo_dark" => "logo-dark.png",
            "company_favicon" => "",
            "SITE_RTL" => "off",
            "owner_signature" => "",
            "cust_darklayout" => "off",
            "footer_text" => "",

            'mail_driver' => '',
            'mail_host' => '',
            'mail_port' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => '',
            'mail_from_address' => '',
            'mail_from_name' => '',
            'payslip_name' => '',
            'payslip_salary_month' => '',
            'payslip_url' => '',

        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function flagOfCountry()
    {
        $arr = [
            'ar' => '🇦🇪 ar',
            'da' => '🇩🇰 da',
            'de' => '🇩🇪 de',
            'es' => '🇪🇸 es',
            'fr' => '🇫🇷 fr',
            'it' => '🇮🇹 it',
            'ja' => '🇯🇵 ja',
            'nl' => '🇳🇱 nl',
            'pl' => '🇵🇱 pl',
            'ru' => '🇷🇺 ru',
            'pt' => '🇵🇹 pt',
            'en' => '🇮🇳 en',
            'tr' => '🇹🇷 tr',
            'pt-br' => '🇵🇹 pt-br',
        ];
        return $arr;
    }

    public static function languagecreate()
    {
        $languages = Utility::langList();
        foreach ($languages as $key => $lang) {
            $languageExist = Language::where('code', $key)->first();
            if (empty($languageExist)) {
                $language = new Language();
                $language->code = $key;
                $language->fullname = $lang;
                $language->save();
            }
        }
    }

    public static function langList()
    {
        $languages = [
            "ar" => "Arabic",
            "zh" => "Chinese",
            "da" => "Danish",
            "de" => "German",
            "en" => "English",
            "es" => "Spanish",
            "fr" => "French",
            "he" => "Hebrew",
            "it" => "Italian",
            "ja" => "Japanese",
            "nl" => "Dutch",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "ru" => "Russian",
            "tr" => "Turkish",
            "pt-br" => "Portuguese(Brazil)"
        ];
        return $languages;
    }

    public static function langSetting()
    {
        $data = DB::table('settings');
        $data = $data->where('created_by', '=', 1)->get();
        if (count($data) == 0) {
            $data = DB::table('settings')->where('created_by', '=', 1)->get();
        }
        $settings = [];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function languages()
    {
        if (self::$languageSetting == null) {
            $languages = Utility::langList();

            if (\Schema::hasTable('languages')) {
                $settings = Utility::langSetting();
                if (!empty($settings['disable_lang'])) {
                    $disabledlang = explode(',', $settings['disable_lang']);
                    $languages = Language::whereNotIn('code', $disabledlang)->pluck('fullName', 'code');
                } else {
                    $languages = Language::pluck('fullname', 'code');
                }
                self::$languageSetting = $languages;
            }
        }

        return self::$languageSetting;
    }

    // public static function languages()
    // {
    //     $dir     = base_path() . '/resources/lang/';
    //     $glob    = glob($dir . "*", GLOB_ONLYDIR);
    //     $arrLang = array_map(
    //         function ($value) use ($dir) {
    //             return str_replace($dir, '', $value);
    //         },
    //         $glob
    //     );
    //     $arrLang = array_map(
    //         function ($value) use ($dir) {
    //             return preg_replace('/[0-9]+/', '', $value);
    //         },
    //         $arrLang
    //     );
    //     $arrLang = array_filter($arrLang);

    //     return $arrLang;
    // }


    private static $storageSetting = null;
    public static function getStorageSetting()
    {
        if (self::$getRatingData == null) {
            $data = DB::table('settings');
            $data = $data->where('created_by', '=', 1);
            $data = $data->get();
            self::$storageSetting = $data;
        }
        $data = self::$storageSetting;

        $settings = [
            "storage_setting" => "",
            "local_storage_validation" => "",
            "local_storage_max_upload_size" => "",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url" => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function getValByName($key)
    {
        $setting = Utility::settings();
        if (!isset($setting[$key]) || empty($setting[$key])) {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function getValByName1($key)
    {
        $setting = Utility::getGdpr();
        if (!isset($setting[$key]) || empty($setting[$key])) {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}='{$envValue}'\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if (!file_put_contents($envFile, $str)) {
            return false;
        }

        return true;
    }

    public static function templateData()
    {
        $arr = [];
        $arr['colors'] = [
            '003580',
            '666666',
            '6676ef',
            'f50102',
            'f9b034',
            'fbdd03',
            'c1d82f',
            '37a4e4',
            '8a7966',
            '6a737b',
            '050f2c',
            '0e3666',
            '3baeff',
            '3368e6',
            'b84592',
            'f64f81',
            'f66c5f',
            'fac168',
            '46de98',
            '40c7d0',
            'be0028',
            '2f9f45',
            '371676',
            '52325d',
            '511378',
            '0f3866',
            '48c0b6',
            '297cc0',
            'ffffff',
            '000',
        ];
        $arr['templates'] = [
            "template1" => "New York",
            "template2" => "Toronto",
            "template3" => "Rio",
            "template4" => "London",
            "template5" => "Istanbul",
            "template6" => "Mumbai",
            "template7" => "Hong Kong",
            "template8" => "Tokyo",
            "template9" => "Sydney",
            "template10" => "Paris",
        ];

        return $arr;
    }

    public static function priceFormat($settings, $price)
    {
        $decimal_number = Utility::getValByName('decimal_number') ? Utility::getValByName('decimal_number') : 0;
        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, $decimal_number) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public static function currencySymbol($settings)
    {
        return $settings['site_currency_symbol'];
    }

    public static function dateFormat($settings, $date)
    {
        return date($settings['site_date_format'], strtotime($date));
    }

    public static function timeFormat($settings, $time)
    {
        return date($settings['site_time_format'], strtotime($time));
    }

    public static function invoiceNumberFormat($settings, $number)
    {
        $settings = Utility::settings();
        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public static function proposalNumberFormat($settings, $number)
    {
        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public static function retainerNumberFormat($settings, $number)
    {
        $settings = Utility::settings();
        return $settings["retainer_prefix"] . sprintf("%05d", $number);
    }

    public static function customerProposalNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public static function customerRetainerNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["retainer_prefix"] . sprintf("%05d", $number);
    }

    public static function customerInvoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public static function billNumberFormat($settings, $number)
    {
        return $settings["bill_prefix"] . sprintf("%05d", $number);
    }

    public static function vendorBillNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["bill_prefix"] . sprintf("%05d", $number);
    }
    public static function contractNumberFormat($settings, $number)
    {
        return $settings["contract_prefix"] . sprintf("%05d", $number);
    }

    public static function getTax($tax)
    {
        if (self::$taxes == null) {
            $tax = Tax::find($tax);
            self::$taxes = $tax;
        }
        return self::$taxes;
    }

    public static function tax($taxes)
    {

        $taxArr = explode(',', $taxes);
        $taxes = [];
        foreach ($taxArr as $tax) {
            $taxes[] = Tax::find($tax);
        }

        return $taxes;
    }


    // public static function tax($taxes)
    // {
    //     if (self::$taxsData == null) {
    //         $taxArr = explode(',', $taxes);
    //         $taxes  = [];
    //         foreach ($taxArr as $tax) {
    //             $taxes[] = self::getTax($tax);
    //         }
    //         self::$taxsData = $taxes;
    //     }

    //     return self::$taxsData;
    // }

    // public static function taxRate($taxRate, $price, $quantity, $discount)
    // {


    //     return ($taxRate / 100) * ($price * $quantity - $discount);
    // }
    // public static function taxRate($taxRate, $price, $quantity)
    // {

    //     return ($taxRate / 100) * ($price * $quantity);
    // }



    public static function taxRate($taxRate, $price, $quantity, $discount = 0)
    {

        //        return ($taxRate / 100) * (($price-$discount) * $quantity);
        return (($price * $quantity) - $discount) * ($taxRate / 100);
    }



    // public static function totalTaxRate($taxes)
    // {

    //     if (self::$taxRateData == null) {
    //         $taxArr  = explode(',', $taxes);
    //         $taxRate = 0;
    //         foreach ($taxArr as $tax) {
    //             $tax     = self::getTax($tax);
    //             $taxRate += !empty($tax->rate) ? $tax->rate : 0;
    //         }
    //         self::$taxRateData = $taxRate;
    //     }
    //     return self::$taxRateData;
    // }

    public static function totalTaxRate($taxes)
    {

        $taxArr = explode(',', $taxes);
        $taxRate = 0;

        foreach ($taxArr as $tax) {

            $tax = Tax::find($tax);
            $taxRate += !empty($tax->rate) ? $tax->rate : 0;
        }

        return $taxRate;
    }

    public static function userBalance($users, $id, $amount, $type)
    {
        if ($users == 'customer') {
            $user = Customer::find($id);
        } else {
            $user = Vender::find($id);
        }
        if (!empty($user)) {
            if ($type == 'credit') {
                $oldBalance = $user->balance;
                $userBalance = $oldBalance + $amount;
                $user->balance = $userBalance;
                $user->save();
            } elseif ($type == 'debit') {
                $oldBalance = $user->balance;
                $userBalance = $oldBalance - $amount;
                $user->balance = $userBalance;
                $user->save();
            }
        }
    }

    public static function updateUserBalance($users, $id, $amount, $type)
    {
        if ($users == 'customer') {
            $user = Customer::find($id);
        } else {
            $user = Vender::find($id);
        }

        if (!empty($user)) {
            if ($type == 'credit') {
                $oldBalance = $user->balance;
                $userBalance = $oldBalance - $amount;
                $user->balance = $userBalance;
                $user->save();
            } elseif ($type == 'debit') {
                $oldBalance = $user->balance;
                $userBalance = $oldBalance + $amount;
                $user->balance = $userBalance;
                $user->save();
            }
        }
    }

    public static function bankAccountBalance($id, $amount, $type)
    {
        $bankAccount = BankAccount::find($id);
        if ($bankAccount) {
            if ($type == 'credit') {
                $oldBalance = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance + $amount;
                $bankAccount->save();
            } elseif ($type == 'debit') {
                $oldBalance = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance - $amount;
                $bankAccount->save();
            }
        }
    }

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for ($i = 0; $i < count($C); ++$i) {
            if ($C[$i] <= 0.03928) {
                $C[$i] = $C[$i] / 12.92;
            } else {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if ($L > 0.179) {
            $color = 'black';
        } else {
            $color = 'white';
        }

        return $color;
    }

    public static function getInvoiceColor($settings = null)
    {
        return self::getDocumentColor('invoice_color', $settings);
    }

    public static function getProposalColor($settings = null)
    {
        return self::getDocumentColor('proposal_color', $settings);
    }

    public static function getBillColor($settings = null)
    {
        return self::getDocumentColor('bill_color', $settings);
    }

    public static function getRetainerColor($settings = null)
    {
        return self::getDocumentColor('retainer_color', $settings);
    }

    private static function getDocumentColor($colorKey, $settings = null)
    {
        if (is_null($settings)) {
            $settings = self::settings();
        }
        
        // Use the general theme color if document color is not set or is default white
        if (empty($settings[$colorKey]) || $settings[$colorKey] == 'ffffff') {
            $themeColor = !empty($settings['color']) ? $settings['color'] : 'theme-3';
            // Extract color from theme class (theme-3 = primary color)
            $colorMap = [
                'theme-1' => '51459d',  // Blue
                'theme-2' => 'ff3a6e',  // Pink  
                'theme-3' => '007C38',  // Green (primary)
                'theme-4' => 'ffa21d',  // Orange
                'theme-5' => '3ec9d6',  // Cyan
            ];
            return isset($colorMap[$themeColor]) ? $colorMap[$themeColor] : '007C38';
        }
        
        return $settings[$colorKey];
    }


    public static function delete_directory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }


    public static function getCompanyPaymentSettingWithOutAuth($user_id)
    {
        // dd($user_id)
        $data = \DB::table('company_payment_settings');
        $settings = [];
        $data = $data->where('created_by', '=', $user_id);
        $data = $data->get();
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    // public static function getAdminPaymentSetting()
    // {
    //     if (self::$adminSetting == null) {

    //         $data     = \DB::table('admin_payment_settings');
    //         $settings = [];
    //         if (\Auth::check()) {
    //             $user_id = 1;
    //             $data    = $data->where('created_by', '=', $user_id);
    //         }
    //         self::$adminSetting = $data;
    //         foreach ($data as $row) {
    //             $settings[$row->name] = $row->value;
    //         }
    //     }

    //     return self::$adminSetting;
    // }

    public static function getAdminPaymentSetting()
    {
        if (self::$adminSetting === null) {
            $settings = [];

            if (\Auth::check()) {
                $user_id = 1; // You may want to replace this with the actual user's ID.
                $data = \DB::table('admin_payment_settings')
                    ->where('created_by', $user_id)
                    ->get();

                foreach ($data as $row) {
                    $settings[$row->name] = $row->value;
                }
            }

            self::$adminSetting = $settings;
        }

        return self::$adminSetting;
    }


    public static function getCompanyPaymentSetting($user_id)
    {
        $data = \DB::table('company_payment_settings');
        $settings = [];
        $data = $data->where('created_by', '=', $user_id);
        $data = $data->get();
        // dd($data);
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function getCompanyPayment()
    {

        $data = \DB::table('company_payment_settings');
        $settings = [];
        if (\Auth::check()) {
            $user_id = \Auth::user()->creatorId();
            $data = $data->where('created_by', '=', $user_id);
        }
        $data = $data->get();
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function getNonAuthCompanyPaymentSetting($id)
    {

        $data = \DB::table('company_payment_settings');
        $settings = [];
        $data = $data->where('created_by', '=', $id);

        $data = $data->get();
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function error_res($msg = "", $args = array())
    {
        $msg = $msg == "" ? "error" : $msg;
        $msg_id = 'error.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg = $msg_id == $converted ? $msg : $converted;
        $json = array(
            'flag' => 0,
            'msg' => $msg,
        );

        return $json;
    }

    public static function success_res($msg = "", $args = array())
    {
        $msg = $msg == "" ? "success" : $msg;
        $msg_id = 'success.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg = $msg_id == $converted ? $msg : $converted;
        $json = array(
            'flag' => 1,
            'msg' => $msg,
        );

        return $json;
    }

    // get date format
    public static function getDateFormated($date, $time = false)
    {
        if (!empty($date) && $date != '0000-00-00') {
            if ($time == true) {
                return date("d M Y H:i A", strtotime($date));
            } else {
                return date("d M Y", strtotime($date));
            }
        } else {
            return '';
        }
    }


    public static function invoice_payment_settings($id)
    {
        $data = [];

        $user = User::where(['id' => $id])->first();
        if (!is_null($user)) {
            $data = DB::table('admin_payment_settings');
            $data->where('created_by', '=', $id);
            $data = $data->get();
            //dd($data);
        }

        $res = [];

        foreach ($data as $key => $value) {
            $res[$value->name] = $value->value;
        }

        return $res;
    }

    public static function bill_payment_settings($id)
    {
        $data = [];

        $user = User::where(['id' => $id])->first();
        if (!is_null($user)) {
            $data = DB::table('admin_payment_settings');
            $data->where('created_by', '=', $id);
            $data = $data->get();
            //dd($data);
        }

        $res = [];

        foreach ($data as $key => $value) {
            $res[$value->name] = $value->value;
        }

        return $res;
    }


    public static function settingById($id)
    {
        $data = DB::table('settings')->where('created_by', '=', $id)->get();
        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "#INVO",
            "journal_prefix" => "#JUR",
            "invoice_color" => "ffffff",
            "proposal_prefix" => "#PROP",
            "proposal_color" => "ffffff",
            "proposal_font" => "Lato",
            "proposal_logo" => " ",
            "retainer_logo" => " ",
            "invoice_logo" => " ",
            "bill_logo" => " ",
            "retainer_color" => "ffffff",
            "retainer_font" => "Inter",
            "bill_prefix" => "#BILL",
            "bill_color" => "ffffff",
            "customer_prefix" => "#CUST",
            "vender_prefix" => "#VEND",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "retainer_template" => "template1",
            "registration_number" => "",
            "vat_number" => "",
            "default_language" => "en",
            "enable_stripe" => "",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_number" => "on",
            "tax_type" => "",
            "shipping_display" => "on",
            "journal_prefix" => "#JUR",
            "display_landing_page" => "on",
            "title_text" => "",
            // 'gdpr_cookie' => "off",
            'cookie_text' => "",
            "invoice_starting_number" => "1",
            "proposal_starting_number" => "1",
            "retainer_starting_number" => "1",
            "bill_starting_number" => "1",
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }



    public static function addNewData()
    {
        \Artisan::call('cache:forget spatie.permission.cache');
        \Artisan::call('cache:clear');
        $usr = \Auth::user();

        $arrPermissions = [
            'manage budget planner',
            'create budget planner',
            'edit budget planner',
            'delete budget planner',
            'view budget planner',
            'stock report',
            'manage contract',
            'manage customer contract',
            'create contract',
            'edit contract',
            'delete contract',
            'show contract',
            'duplicate contract',
            'delete attachment',
            'delete comment',
            'delete notes',
            'contract description',
            'upload attachment',
            'add comment',
            'add notes',
            'send contract mail',
            'manage retainer',


        ];
        foreach ($arrPermissions as $ap) {
            // check if permission is not created then create it.
            $permission = Permission::where('name', 'LIKE', $ap)->first();
            if (empty($permission)) {
                Permission::create(['name' => $ap]);
            }
        }
        $companyRole = Role::where('name', 'LIKE', 'company')->first();

        $companyPermissions = $companyRole->getPermissionNames()->toArray();
        $companyNewPermission = [
            'manage budget planner',
            'create budget planner',
            'edit budget planner',
            'delete budget planner',
            'view budget planner',
            'stock report',
            'manage contract',
            'manage customer contract',
            'create contract',
            'edit contract',
            'delete contract',
            'show contract',
            'duplicate contract',
            'delete attachment',
            'delete comment',
            'delete notes',
            'contract description',
            'upload attachment',
            'add comment',
            'add notes',
            'send contract mail',
            'manage retainer',
        ];
        foreach ($companyNewPermission as $op) {
            // check if permission is not assign to owner then assign.
            if (!in_array($op, $companyPermissions)) {
                $permission = Permission::findByName($op);
                $companyRole->givePermissionTo($permission);
            }
        }
    }

    // Twilio Notification
    public static function send_twilio_msg($to, $slug, $obj, $user_id = null)
    {
        // dd($user_id);
        $notification_template = NotificationTemplates::where('slug', $slug)->first();

        if (!empty($notification_template) && !empty($obj)) {
            if (!empty($user_id)) {
                $user = User::find($user_id);
            } else {
                $user = \Auth::user();
            }
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->where('created_by', '=', $user->id)->first();

            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->first();
            }
            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
            }
            if (!empty($curr_noti_tempLang) && !empty($curr_noti_tempLang->content)) {
                $msg = self::replaceVariable($curr_noti_tempLang->content, $obj);
            }
        }
        // dd($msg);
        if (isset($msg)) {
            $settings = Utility::settings($user->id);
            $account_sid = $settings['twilio_sid'];
            $auth_token = $settings['twilio_token'];
            $twilio_number = $settings['twilio_from'];
            try {
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($to, [
                    'from' => $twilio_number,
                    'body' => $msg,
                ]);
            } catch (\Exception $e) {
            }
            //  dd('SMS Sent Successfully.');
        }
    }

    // inventory management (Quantity)

    public static function total_quantity($type, $quantity, $product_id)
    {

        $product = ProductService::find($product_id);

        if (($product->type == 'Product')) {
            $pro_quantity = $product->quantity;

            if ($type == 'minus') {
                $product->quantity = $pro_quantity - $quantity;
            } else {
                $product->quantity = $pro_quantity + $quantity;
            }

            $product->save();
        }
    }

    public static function starting_number($id, $type)
    {

        if ($type == 'invoice') {
            $data = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->where('name', 'invoice_starting_number')->update(array('value' => $id));
        } elseif ($type == 'proposal') {
            $data = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->where('name', 'proposal_starting_number')->update(array('value' => $id));
        } elseif ($type == 'retainer') {
            $data = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->where('name', 'retainer_starting_number')->update(array('value' => $id));
        } elseif ($type == 'bill') {
            $data = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->where('name', 'bill_starting_number')->update(array('value' => $id));
        }


        return $data;
    }


    //add quantity in product stock
    public static function addProductStock($product_id, $quantity, $type, $description, $type_id)
    {

        $stocks = new StockReport();
        $stocks->product_id = $product_id;
        $stocks->quantity = $quantity;
        $stocks->type = $type;
        $stocks->type_id = $type_id;
        $stocks->description = $description;
        $stocks->created_by = \Auth::user()->creatorId();
        $stocks->save();
    }

    public static function mode_layout()
    {
        $data = DB::table('settings');
        $data = $data->where('created_by', '=', 1);
        $data = $data->get();
        $settings = [
            "cust_darklayout" => "off",
            "cust_theme_bg" => "off",
            "color" => 'theme-3'
        ];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function colorset()
    {
        if (\Auth::user()) {
            if (\Auth::user()->type == 'super admin') {
                $user = \Auth::user();
                $setting = DB::table('settings')->where('created_by', $user->id)->pluck('value', 'name')->toArray();
            } else {
                $setting = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->pluck('value', 'name')->toArray();
            }
        } else {
            $user = User::where('type', 'super admin')->first();
            $setting = DB::table('settings')->where('created_by', $user->id)->pluck('value', 'name')->toArray();
        }
        if (!isset($setting['color'])) {
            $setting = Utility::settings();
        }
        return $setting;
    }

    public static function admin_color()
    {
        if (\Auth::user()) {
            if (\Auth::user()->type == 'super admin') {
                $user = \Auth::user();
                $setting = DB::table('settings')->where('created_by', $user->id)->pluck('value', 'name')->toArray();
            } else {
                $setting = DB::table('settings')->where('created_by', \Auth::user()->created_by)->pluck('value', 'name')->toArray();
            }
        } else {
            $user = User::where('type', 'super admin')->first();
            $setting = DB::table('settings')->where('created_by', $user->id)->pluck('value', 'name')->toArray();
        }
        if (!isset($setting['color'])) {
            $setting = Utility::settings();
        }
        return $setting;
    }

    public static function get_superadmin_logo()
    {
        $is_dark_mode = self::getValByName('cust_darklayout');
        $setting = DB::table('settings')->where('created_by', '1')->pluck('value', 'name')->toArray();
        $is_dark_mode = isset($setting['cust_darklayout']) ? $setting['cust_darklayout'] : $is_dark_mode;

        if (\Auth::user() && \Auth::user()->type != 'super admin') {
            if ($is_dark_mode == 'on') {
                return Utility::getValByName('company_logo_light');
            } else {
                return Utility::getValByName('company_logo_dark');
            }
        } else {
            if ($is_dark_mode == 'on') {
                return 'logo-light.png';
            } else {
                return 'logo-dark.png';
            }
        }
    }

    public static function get_company_logo()
    {
        $is_dark_mode = self::getValByName('cust_darklayout');
        if ($is_dark_mode == 'on') {
            $logo = self::getValByName('cust_darklayout');
            return Utility::getValByName('company_logo_light');
        } else {
            return Utility::getValByName('company_logo_dark');
        }
    }

    public static function GetLogo()
    {
        $setting = Utility::colorset();
        if (\Auth::user() && \Auth::user()->type != 'super admin') {
            if ($setting['cust_darklayout'] == 'on') {
                return Utility::getValByName('company_logo_light');
            } else {
                return Utility::getValByName('company_logo_dark');
            }
        } else {
            if ($setting['cust_darklayout'] == 'on') {
                return Utility::getValByName('logo_light');
            } else {
                return Utility::getValByName('logo_dark');
            }
        }
    }


    public static function getLayoutsSetting()
    {
        $data = DB::table('settings');

        if (\Auth::check()) {

            $data = $data->where('created_by', '=', \Auth::user()->creatorId())->get();
            // dd($data);
            if (count($data) == 0) {
                $data = DB::table('settings')->where('created_by', '=', 1)->get();
            }
        } else {
            $data = $data->where('created_by', '=', 1)->get();
        }
        $settings = [
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "color" => "theme-3",
            "SITE_RTL" => "off",
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    // used for replace email variable (parameter 'template_name','id(get particular record by id for data)')
    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{payment_name}',
            '{payment_bill}',
            '{payment_amount}',
            '{payment_date}',
            '{payment_method}',
            '{invoice_name}',
            '{invoice_number}',
            '{invoice_url}',
            '{bill_name}',
            '{bill_number}',
            '{bill_url}',
            '{payment_dueAmount}',
            '{proposal_name}',
            '{proposal_number}',
            '{proposal_url}',
            '{app_name}',
            '{company_name}',
            '{app_url}',
            '{email}',
            '{password}',
            '{contract_customer}',
            '{contract_subject}',
            '{contract_start_date}',
            '{contract_end_date}',
            '{contract_type}',
            '{contract_value}',
            '{retainer_name}',
            '{retainer_number}',
            '{retainer_url}',
            '{customer_name}',
            '{due_amount}',
            '{invoice_category}',
            '{vender_name}',
            '{user_name}',
            '{name}',
            '{type}',
            '{company_email}',

            '{payslip_name}',
            '{payslip_salary_month}',
            '{payslip_url}',



        ];
        $arrValue = [
            'payment_name' => '-',
            'payment_bill' => '-',
            'payment_amount' => '-',
            'payment_date' => '-',
            'payment_method' => '-',
            'invoice_name' => '-',
            'invoice_number' => '-',
            'invoice_url' => '-',
            'bill_name' => '-',
            'bill_number' => '-',
            'bill_url' => '-',
            'payment_dueAmount' => '-',
            'proposal_name' => '-',
            'proposal_number' => '-',
            'proposal_url' => '-',
            'app_name' => '-',
            'company_name' => '-',
            'app_url' => '-',
            'email' => '-',
            'password' => '-',
            'contract_customer' => '-',
            'contract_subject' => '-',
            'contract_start_date' => '-',
            'contract_end_date' => '-',
            'contract_type' => '-',
            'contract_value' => '-',
            'retainer_name' => '-',
            'retainer_number' => '-',
            'retainer_url' => '-',
            'customer_name' => '-',
            'due_amount' => '-',
            'invoice_category' => '-',
            'retainer_url' => '-',
            'vender_name' => '-',
            'user_name' => '-',
            'name' => '-',
            'type' => '-',
            "company_email" => '-',

            'payslip_name' => '',
            'payslip_salary_month' => '',
            'payslip_url' => '',



        ];

        foreach ($obj as $key => $val) {
            $arrValue[$key] = $val;
        }

        $settings = Utility::settings();
        $company_name = $settings['company_name'];

        $arrValue['app_name'] = env('APP_NAME');
        $arrValue['company_name'] = self::settings()['company_name'];
        $arrValue['app_url'] = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }


    public static function getSMTPDetails($user_id)
    {
        $settings = self::settingsById($user_id);
        
        // Get port and encryption settings
        $mail_port = !empty($settings['mail_port']) ? $settings['mail_port'] : env('MAIL_PORT');
        $mail_encryption = !empty($settings['mail_encryption']) ? $settings['mail_encryption'] : env('MAIL_ENCRYPTION');
        
        // Auto-correct encryption based on port if needed
        if ($mail_port == 465 && strtolower($mail_encryption) == 'tls') {
            $mail_encryption = 'ssl';
        } elseif ($mail_port == 587 && strtolower($mail_encryption) == 'ssl') {
            $mail_encryption = 'tls';
        }
        
        // Use database settings if available, otherwise fall back to .env settings
        $smtpDetail = config(
            [
                'mail.driver' => !empty($settings['mail_driver']) ? $settings['mail_driver'] : env('MAIL_DRIVER', 'smtp'),
                'mail.host' => !empty($settings['mail_host']) ? $settings['mail_host'] : env('MAIL_HOST'),
                'mail.port' => $mail_port,
                'mail.encryption' => $mail_encryption,
                'mail.username' => !empty($settings['mail_username']) ? $settings['mail_username'] : env('MAIL_USERNAME'),
                'mail.password' => !empty($settings['mail_password']) ? $settings['mail_password'] : env('MAIL_PASSWORD'),
                'mail.from.address' => !empty($settings['mail_from_address']) ? $settings['mail_from_address'] : env('MAIL_FROM_ADDRESS'),
                'mail.from.name' => !empty($settings['mail_from_name']) ? $settings['mail_from_name'] : env('MAIL_FROM_NAME'),
            ]
        );


        return $smtpDetail;
    }

    public static function isValidSMTPSettings($user_id = 1)
    {
        try {
            $settings = self::settingsById($user_id);
            
            // Check if SMTP settings exist
            $mail_host = !empty($settings['mail_host']) ? $settings['mail_host'] : env('MAIL_HOST');
            $mail_port = !empty($settings['mail_port']) ? $settings['mail_port'] : env('MAIL_PORT');
            $mail_username = !empty($settings['mail_username']) ? $settings['mail_username'] : env('MAIL_USERNAME');
            $mail_password = !empty($settings['mail_password']) ? $settings['mail_password'] : env('MAIL_PASSWORD');
            
            // If essential SMTP settings are missing, return false
            if (empty($mail_host) || empty($mail_port) || empty($mail_username) || empty($mail_password)) {
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public static function testSMTPConnection($user_id = 1)
    {
        try {
            $settings = self::settingsById($user_id);
            
            // Get SMTP settings
            $mail_host = !empty($settings['mail_host']) ? $settings['mail_host'] : env('MAIL_HOST');
            $mail_port = !empty($settings['mail_port']) ? $settings['mail_port'] : env('MAIL_PORT');
            $mail_encryption = !empty($settings['mail_encryption']) ? $settings['mail_encryption'] : env('MAIL_ENCRYPTION');
            
            // Auto-correct encryption based on port
            if ($mail_port == 465 && strtolower($mail_encryption) == 'tls') {
                $mail_encryption = 'ssl';
            } elseif ($mail_port == 587 && strtolower($mail_encryption) == 'ssl') {
                $mail_encryption = 'tls';
            }
            
            // First test DNS resolution
            $ip = gethostbyname($mail_host);
            if ($ip == $mail_host && !filter_var($mail_host, FILTER_VALIDATE_IP)) {
                return ['success' => false, 'message' => 'DNS Error: Cannot resolve hostname ' . $mail_host . '. Please check your mail host setting.'];
            }
            
            // Test connection
            $timeout = 10;
            $protocol = ($mail_encryption == 'ssl') ? 'ssl://' : '';
            
            $errno = 0;
            $errstr = '';
            $connection = @stream_socket_client(
                $protocol . $mail_host . ':' . $mail_port,
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT
            );
            
            if (!$connection) {
                return ['success' => false, 'message' => 'Connection Error: ' . $errstr . ' (Error code: ' . $errno . ')'];
            }
            
            fclose($connection);
            return ['success' => true, 'message' => 'SMTP connection successful! Mail server is reachable.'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
public static function sendEmailTemplate($emailTemplate, $mailTo, $obj)
{
    $usr = \Auth::user();

    // Normalize recipients to a flat list of emails
    $mailTo = array_values($mailTo);

    // Find template by slug
    $template = EmailTemplate::where('slug', $emailTemplate)->first();
    if (!$template) {
        return ['is_success' => false, 'error' => __('Mail not send, email not found')];
    }

    // Determine if active (keep your existing logic but do NOT touch mail config)
    $is_active = UserEmailTemplate::where('template_id', $template->id)->first();
    if ($template->id == 1 && $is_active) {
        $is_active->is_active = 1;
    }
    if ($is_active && (int)$is_active->is_active !== 1) {
        return ['is_success' => true, 'error' => false];
    }

    // Load localized content
    $lang = $usr->lang ?? config('app.locale', 'en');
    $content = EmailTemplateLang::where('parent_id', $template->id)->where('lang', $lang)->first();
    if (!$content || empty($content->content)) {
        return ['is_success' => false, 'error' => __('Mail not send, email is empty')];
    }

    // Replace variables in content
    $content->content = self::replaceVariable($content->content, $obj);
    // Ensure subject exists (fallback)
    if (empty($content->subject)) {
        $content->subject = $template->name ?? (config('app.name') . ' Notification');
    }

    $settings = [
        'mail_from_address' => config('mail.from.address'),
        'mail_from_name'    => "config('mail.from.name')",
    ];

    try {
        Mail::to($mailTo)->send(new \App\Mail\CommonEmailTemplate($content, $settings));
        return ['is_success' => true, 'error' => false];
    } catch (\Exception $e) {
        return ['is_success' => false, 'error' => $e->getMessage() ?: __('E-Mail has been not sent due to SMTP configuration')];
    }
}

public static function sendUserEmailTemplate($emailTemplate, $mailTo, $obj)
{
    $usr = Auth::user();

    // Normalize recipients
    $mailTo = array_values($mailTo);

    // Find template by name
    $template = EmailTemplate::where('name', 'LIKE', $emailTemplate)->first();
    if (!$template) {
        return ['is_success' => false, 'error' => __('Mail not send, email not found')];
    }

    // Active flag (no mail config changes)
    $creatorId = method_exists($usr, 'creatorId') ? $usr->creatorId() : ($usr->id ?? null);
    $is_active = UserEmailTemplate::where('template_id', $template->id)
        ->when($creatorId, fn($q) => $q->where('user_id', $creatorId))
        ->first();
    if ($is_active && (int)$is_active->is_active !== 1) {
        return ['is_success' => true, 'error' => false];
    }

    // Localized content
    $lang = $usr->lang ?? config('app.locale', 'en');
    $content = EmailTemplateLang::where('parent_id', $template->id)->where('lang', $lang)->first();
    if (!$content || empty($content->content)) {
        return ['is_success' => false, 'error' => __('Mail not send, email is empty')];
    }

    $content->content = self::replaceVariable($content->content, $obj);
    if (empty($content->subject)) {
        $content->subject = $template->name ?? (config('app.name') . ' Notification');
    }

    // From settings strictly from .env
    $settings = [
        'mail_from_address' => config('mail.from.address'),
        'mail_from_name'    => config('mail.from.name'),
    ];

    try {
        Mail::to($mailTo)->send(new \App\Mail\CommonEmailTemplate($content, $settings));
        return ['is_success' => true, 'error' => false];
    } catch (\Exception $e) {
        return ['is_success' => false, 'error' => $e->getMessage()];
    }
}

    // public static function sendEmailTemplate($emailTemplate, $mailTo, $obj)
    // {
    //     $usr = \Auth::user();
    //     //Remove Current Login user Email don't send mail to them
    //     if ($usr->user_type != 'super admin') {
    //         unset($mailTo[$usr->id]);

    //         $mailTo = array_values($mailTo);

    //         if ($usr->user_type != 'super admin') {
    //             // find template is exist or not in our record
    //             $template = EmailTemplate::where('slug', $emailTemplate)->first();
    //             if (isset($template) && !empty($template)) {
    //                 // check template is active or not by company

    //                 $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->first();

    //                 if ($template->id == 1) {
    //                     $is_active->is_active = 1;
    //                 }

    //                 if ($is_active->is_active == 1) {

    //                     // get email content language base
    //                     $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $usr->lang)->first();

    //                     $content->from = $template->from;


    //                     if ($usr->user_type == 'super admin') {
    //                         $settings = Utility::settings();
    //                     } else {
    //                         $setting = self::settings();
    //                         if (empty($setting['mail_driver'])) {
    //                             $setting = self::settingsById(1);
    //                         }
    //                         $settings = $setting;
    //                     }

    //                     config([
    //                         'mail.default' => isset($settings['mail_driver']) ? $settings['mail_driver'] : '',
    //                         'mail.mailers.smtp.host' => isset($settings['mail_host']) ? $settings['mail_host'] : '',
    //                         'mail.mailers.smtp.port' => isset($settings['mail_port']) ? $settings['mail_port'] : '',
    //                         'mail.mailers.smtp.encryption' => isset($settings['mail_encryption']) ? $settings['mail_encryption'] : '',
    //                         'mail.mailers.smtp.username' => isset($settings['mail_username']) ? $settings['mail_username'] : '',
    //                         'mail.mailers.smtp.password' => isset($settings['mail_password']) ? $settings['mail_password'] : '',
    //                         'mail.from.address' => isset($settings['mail_from_address']) ? $settings['mail_from_address'] : '',
    //                         'mail.from.name' => isset($settings['mail_from_name']) ? $settings['mail_from_name'] : '',
    //                     ]);
    //                     if (!empty($content->content)) {
    //                         $content->content = self::replaceVariable($content->content, $obj);
    //                         // send email
    //                         try {
    //                             Mail::to($mailTo)->send(new CommonEmailTemplate($content, $settings, $mailTo[0]));
    //                         } catch (\Exception $e) {
    //                             $error = __('E-Mail has been not sent due to SMTP configuration');
    //                         }

    //                         if (isset($error)) {
    //                             $arReturn = [
    //                                 'is_success' => false,
    //                                 'error' => $error,
    //                             ];
    //                         } else {
    //                             $arReturn = [
    //                                 'is_success' => true,
    //                                 'error' => false,
    //                             ];
    //                         }
    //                     } else {
    //                         $arReturn = [
    //                             'is_success' => false,
    //                             'error' => __('Mail not send, email is empty'),
    //                         ];
    //                     }

    //                     return $arReturn;
    //                 } else {
    //                     return [
    //                         'is_success' => true,
    //                         'error' => false,
    //                     ];
    //                 }
    //             } else {
    //                 return [
    //                     'is_success' => false,
    //                     'error' => __('Mail not send, email not found'),
    //                 ];
    //             }
    //         }
    //     } else {
    //         $mailTo = array_values($mailTo);

    //         $template = EmailTemplate::where('slug', $emailTemplate)->first();

    //         $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', 'en')->first();

    //         $content->from = $template->from;
    //         $settings = Utility::settings();

    //         config(
    //             [
    //                 'mail.driver' => isset($settings['mail_driver']) ? $settings['mail_driver'] : '',
    //                 'mail.host' => isset($settings['mail_host']) ? $settings['mail_host'] : '',
    //                 'mail.port' => isset($settings['mail_port']) ? $settings['mail_port'] : '',
    //                 'mail.encryption' => isset($settings['mail_encryption']) ? $settings['mail_encryption'] : '',
    //                 'mail.username' => isset($settings['mail_username']) ? $settings['mail_username'] : '',
    //                 'mail.password' => isset($settings['mail_password']) ? $settings['mail_password'] : '',
    //                 'mail.from.address' => isset($settings['mail_from_address']) ? $settings['mail_from_address'] : '',
    //                 'mail.from.name' => isset($settings['mail_from_name']) ? $settings['mail_from_name'] : '',
    //             ]
    //         );

    //         if (!empty($content->content)) {

    //             $content->content = self::replaceVariable($content->content, $obj);

    //             try {
    //                 Mail::to($mailTo)->send(new CommonEmailTemplate($content, $settings, $mailTo[0]));
    //             } catch (\Exception $e) {


    //                 $error = __('E-Mail has been not sent due to SMTP configuration');
    //             }
    //         }
    //     }
    // }

    // public static function sendUserEmailTemplate($emailTemplate, $mailTo, $obj)
    // {
    //     $usr = Auth::user();
    //     //Remove Current Login user Email don't send mail to them
    //     // unset($mailTo[$usr->id]);
    //     $mailTo = array_values($mailTo);

    //     // find template is exist or not in our record
    //     $template = EmailTemplate::where('name', 'LIKE', $emailTemplate)->first();
    //     if (isset($template) && !empty($template)) {
    //         // check template is active or not by company

    //         $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->where('user_id', '=', $usr->creatorId())->first();

    //         if ($is_active->is_active == 1) {

    //             $settings = self::settingsById(1);

    //             // get email content language base
    //             $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $usr->lang)->first();
    //             $content->from = $template->from;
    //             if (!empty($content->content)) {
    //                 $content->content = self::replaceVariable($content->content, $obj);
    //                 // send email
    //                 try {
    //                     config(
    //                         [
    //                             'mail.driver' => $settings['mail_driver'],
    //                             'mail.host' => $settings['mail_host'],
    //                             'mail.port' => $settings['mail_port'],
    //                             'mail.encryption' => $settings['mail_encryption'],
    //                             'mail.username' => $settings['mail_username'],
    //                             'mail.password' => $settings['mail_password'],
    //                             'mail.from.address' => $settings['mail_from_address'],
    //                             'mail.from.name' => $settings['mail_from_name'],
    //                         ]
    //                     );
    //                     Mail::to($mailTo)->send(new CommonEmailTemplate($content, $settings));
    //                 } catch (\Exception $e) {
    //                     $error = $e->getMessage();
    //                 }

    //                 if (isset($error)) {
    //                     $arReturn = [
    //                         'is_success' => false,
    //                         'error' => $error,
    //                     ];
    //                 } else {
    //                     $arReturn = [
    //                         'is_success' => true,
    //                         'error' => false,
    //                     ];
    //                 }
    //             } else {
    //                 $arReturn = [
    //                     'is_success' => false,
    //                     'error' => __('Mail not send, email is empty'),
    //                 ];
    //             }

    //             return $arReturn;
    //         } else {
    //             return [
    //                 'is_success' => true,
    //                 'error' => false,
    //             ];
    //         }
    //     } else {
    //         return [
    //             'is_success' => false,
    //             'error' => __('Mail not send, email not found'),
    //         ];
    //     }
    // }

    // Make Entry in email_tempalte_lang table when create new language
    // makeEmailLang

    public static function newLangEmailTemp($lang)
    {
        $template = EmailTemplate::all();
        foreach ($template as $t) {
            $default_lang = EmailTemplateLang::where('parent_id', '=', $t->id)->where('lang', 'LIKE', 'en')->first();
            $emailTemplateLang = new EmailTemplateLang();
            $emailTemplateLang->parent_id = $t->id;
            $emailTemplateLang->lang = $lang;
            $emailTemplateLang->subject = $default_lang->subject;
            $emailTemplateLang->content = $default_lang->content;
            $emailTemplateLang->save();
        }
    }

    // Email Template Modules Function END

public static function upload_file($request, $key_name, $name, $path, $custom_validation = [])
{
    try {
        $settings = Utility::getStorageSetting();
        if (empty($settings['storage_setting'])) {
            return ['flag' => 0, 'msg' => __('Please set proper configuration for storage.')];
        }

        // Resolve limits + mime rules
        $storage  = $settings['storage_setting'];
        $max_size = $storage === 'wasabi'
            ? ($settings['wasabi_max_upload_size'] ?? '2048')
            : ($storage === 's3'
                ? ($settings['s3_max_upload_size'] ?? '2048')
                : ($settings['local_storage_max_upload_size'] ?? '2048'));

        $mimes = $storage === 'wasabi'
            ? ($settings['wasabi_storage_validation'] ?? '')
            : ($storage === 's3'
                ? ($settings['s3_storage_validation'] ?? '')
                : ($settings['local_storage_validation'] ?? ''));

        // Build validation rules
        $rules = $custom_validation ?: array_filter([
            $mimes ? ('mimes:' . $mimes) : null,
            'max:' . $max_size,
            'nullable',
        ]);

        $validator = \Validator::make($request->all(), [$key_name => $rules]);
        if ($validator->fails()) {
            return ['flag' => 0, 'msg' => $validator->messages()->first()];
        }

        // No file present
        if (!$request->hasFile($key_name)) {
            return ['flag' => 0, 'msg' => __('No file uploaded.')];
        }

        $file = $request->file($key_name);
        $name = (string) $name;

        // Normalize path once (no leading/trailing slashes)
        $path = trim($path, '/');

        if ($storage === 'local') {
            \Storage::disk('public')->putFileAs($path, $file, $name);
            $stored = $path . '/' . $name; // relative to public disk => public/storage
            return ['flag' => 1, 'msg' => 'success', 'url' => $stored];
        }

        if ($storage === 'wasabi') {
            config([
                'filesystems.disks.wasabi.key'      => $settings['wasabi_key'] ?? null,
                'filesystems.disks.wasabi.secret'   => $settings['wasabi_secret'] ?? null,
                'filesystems.disks.wasabi.region'   => $settings['wasabi_region'] ?? null,
                'filesystems.disks.wasabi.bucket'   => $settings['wasabi_bucket'] ?? null,
                'filesystems.disks.wasabi.endpoint' => isset($settings['wasabi_region']) ? ('https://s3.' . $settings['wasabi_region'] . '.wasabisys.com') : null,
            ]);
            $stored = \Storage::disk('wasabi')->putFileAs($path, $file, $name);
            return ['flag' => 1, 'msg' => 'success', 'url' => $stored];
        }

        if ($storage === 's3') {
            config([
                'filesystems.disks.s3.key'                     => $settings['s3_key'] ?? null,
                'filesystems.disks.s3.secret'                  => $settings['s3_secret'] ?? null,
                'filesystems.disks.s3.region'                  => $settings['s3_region'] ?? null,
                'filesystems.disks.s3.bucket'                  => $settings['s3_bucket'] ?? null,
                'filesystems.disks.s3.use_path_style_endpoint' => false,
            ]);
            $stored = \Storage::disk('s3')->putFileAs($path, $file, $name);
            return ['flag' => 1, 'msg' => 'success', 'url' => $stored];
        }

        return ['flag' => 0, 'msg' => __('Please set proper configuration for storage.')];
    } catch (\Exception $e) {
        return ['flag' => 0, 'msg' => $e->getMessage()];
    }
}


    public static function file_validate()
    {
        try {
            $settings = Utility::getStorageSetting();
            // dd($settings);
            if (!empty($settings['storage_setting'])) {
                if ($settings['storage_setting'] == 'wasabi') {
                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes = !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';
                } else if ($settings['storage_setting'] == 's3') {
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes = !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';
                } else {
                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '2048';
                    $mimes = !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }

                $res = [
                    'types' => $mimes,
                    'max_size' => $max_size,
                ];
                return $res;

            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;

            }
        } catch (\Exception $e) {
            // dd($e);
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }


    public static function get_file($path)
    {
        $settings = Utility::getStorageSetting();
        try {
            if ($settings['storage_setting'] == 'wasabi') {
                config(
                    [
                        'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                        'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                        'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                        'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                        'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com'
                    ]
                );
                // return \Storage::disk($settings['storage_setting'])->url($path);
            } elseif ($settings['storage_setting'] == 's3') {
                config(
                    [
                        'filesystems.disks.s3.key' => $settings['s3_key'],
                        'filesystems.disks.s3.secret' => $settings['s3_secret'],
                        'filesystems.disks.s3.region' => $settings['s3_region'],
                        'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                        'filesystems.disks.s3.use_path_style_endpoint' => false,
                    ]
                );
                // return \Storage::disk($settings['storage_setting'])->url($path);
            }

            return url('/') . \Storage::disk($settings['storage_setting'])->url($path);
        } catch (\Throwable $th) {
            return '';
        }
    }

    public static function getData()
    {
        $data = DB::table('settings')->where('created_by', 1)->get();

        return $data;
    }

    private static $seoSetting = null;

    public static function getSeoSetting()
    {
        if (is_null(self::$seoSetting)) {
            $data = DB::table('settings');
            $data = $data->where('created_by', '=', 1);

            $data = $data->get();
            self::$seoSetting = $data;
        }
        $data = self::$seoSetting;
        $settings = [
            "meta_keywords" => "",
            "meta_image" => "",
            "meta_description" => ""
        ];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function webhookSetting($module, $user_id = null)
    {

        if (!empty($user_id)) {
            $user = User::find($user_id);
        } else {
            $user = \Auth::user();
        }
        $webhook = Webhook::where('module', $module)->where('created_by', '=', $user->id)->first();
        if (!empty($webhook)) {
            $url = $webhook->url;
            $method = $webhook->method;
            $reference_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $data['method'] = $method;
            $data['reference_url'] = $reference_url;
            $data['url'] = $url;
            return $data;
        }
        return false;
    }



    public static function WebhookCall($url = null, $parameter = null, $method = 'POST')
    {

        if (!empty($url) && !empty($parameter)) {
            try {

                $curlHandle = curl_init($url);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $parameter);
                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                $curlResponse = curl_exec($curlHandle);
                curl_close($curlHandle);
                if (empty($curlResponse)) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                return false;
            }
        } else {
            return false;
        }
    }

    public static $rates;
    public static $data;


    public static function getTaxData()
    {
        $data = [];
        if (self::$rates == null) {
            $rates = Tax::get();
            self::$rates = $rates;
            foreach (self::$rates as $rate) {
                $data[$rate->id]['id'] = $rate->id;
                $data[$rate->id]['name'] = $rate->name;
                $data[$rate->id]['rate'] = $rate->rate;
                $data[$rate->id]['created_by'] = $rate->created_by;
            }
            self::$data = $data;
        }
        return self::$data;
    }

    public static function getAccountData($account_id, $start_date = null, $end_date = null)
    {

        if (!empty($start_date) && !empty($end_date)) {
            $start = $start_date;
            $end = $end_date;
        } else {
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }

        $transactionData = DB::table('transaction_lines')
            ->where('transaction_lines.created_by', \Auth::user()->creatorId())
            ->where('transaction_lines.account_id', $account_id)
            ->whereBetween('transaction_lines.date', [$start, $end])
            ->leftJoin('invoices', function ($join) {
                $join->on('transaction_lines.reference_id', '=', 'invoices.id')
                    ->whereIn('transaction_lines.reference', ['Invoice Payment', 'Invoice']);
            })
            ->leftJoin('bills', function ($join) {
                $join->on('transaction_lines.reference_id', '=', 'bills.id')
                    ->whereIn('transaction_lines.reference', ['Bill', 'Bill Payment', 'Bill Account', 'Expense', 'Expense Account', 'Expense Payment']);
            })
            ->leftJoin('revenues', function ($join) {
                $join->on('transaction_lines.reference_id', '=', 'revenues.id')
                    ->whereIn('transaction_lines.reference', ['Revenue']);
            })
            ->leftJoin('payments', function ($join) {
                $join->on('transaction_lines.reference_id', '=', 'payments.id')
                    ->whereIn('transaction_lines.reference', ['Payment']);
            })
            ->leftJoin('bank_accounts', function ($join) {
                $join->on('transaction_lines.reference_id', '=', 'bank_accounts.id')
                    ->whereIn('transaction_lines.reference', ['Bank Account']);
            })
            ->leftJoin('customers as revenues_customers', 'revenues.customer_id', '=', 'revenues_customers.id')
            ->leftJoin('venders as payments_venders', 'payments.vender_id', '=', 'payments_venders.id')
            ->leftJoin('customers', 'invoices.customer_id', '=', 'customers.id')
            ->leftJoin('venders', 'bills.vender_id', '=', 'venders.id')
            ->leftJoin('chart_of_accounts', 'transaction_lines.account_id', '=', 'chart_of_accounts.id')
            ->select(
                'transaction_lines.*',
                'invoices.customer_id as customer_id',
                'bills.vender_id as vendor_id',
                'chart_of_accounts.name as account_name',
                DB::raw("COALESCE(customers.name, venders.name , revenues_customers.name , payments_venders.name, bank_accounts.holder_name) as user_name"),
                DB::raw("COALESCE(invoices.invoice_id, bills.bill_id) as ids"),
            )
            ->get();

        return $transactionData;
    }

    public static function get_device_type($user_agent)
    {
        $mobile_regex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tablet_regex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';

        if (preg_match_all($mobile_regex, $user_agent)) {
            return 'mobile';
        } else {

            if (preg_match_all($tablet_regex, $user_agent)) {
                return 'tablet';
            } else {
                return 'desktop';
            }
        }
    }

    public static function GetCacheSize()
    {
        $file_size = 0;
        foreach (\File::allFiles(storage_path('/framework')) as $file) {
            $file_size += $file->getSize();
        }
        $file_size = number_format($file_size / 1000000, 4);
        return $file_size;
    }


    public static function updateStorageLimit($company_id, $image_size)
    {
        $image_size = number_format($image_size / 1048576, 2);
        $user = User::find($company_id);
        $plan = Plan::find($user->plan);
        $total_storage = $user->storage_limit + $image_size;


        if ($plan->storage_limit <= $total_storage && $plan->storage_limit != -1) {
            $error = __('Plan storage limit is over so please upgrade the plan.');
            return $error;
        } else {
            $user->storage_limit = $total_storage;
        }

        $user->save();
        return 1;
    }

    public static function changeStorageLimit($company_id, $file_path)
    {

        $files = \File::glob(storage_path($file_path));
        $fileSize = 0;
        foreach ($files as $file) {
            $fileSize += \File::size($file);
        }

        $image_size = number_format($fileSize / 1048576, 2);
        $user = User::find($company_id);
        $plan = Plan::find($user->plan);
        $total_storage = $user->storage_limit - $image_size;
        $user->storage_limit = $total_storage;
        $user->save();

        $status = false;
        foreach ($files as $key => $file) {
            if (\File::exists($file)) {
                $status = \File::delete($file);
            }
        }

        return true;
    }

    public static function getChatGPTSettings()
    {
        $user = User::find(\Auth::user()->creatorId());
        $plan = \App\Models\Plan::find($user->plan);
            if (!$plan) {
        $plan = \App\Models\Plan::first();
    }
    return $plan;
    }

    public static function getAccountBalance($account_id, $start_date = null, $end_date = null)
    {
        if (!empty($start_date) && !empty($end_date)) {
            $start = $start_date;
            $end = $end_date;
        } else {
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }

        // foreach ($types as $type) {
        $total = TransactionLines::select(
            'chart_of_accounts.id',
            'chart_of_accounts.code',
            'chart_of_accounts.name',
            \DB::raw('sum(transaction_lines.debit) as totalDebit'),
            \DB::raw('sum(transaction_lines.credit) as totalCredit')
        );
        $total->leftjoin('chart_of_accounts', 'transaction_lines.account_id', 'chart_of_accounts.id');
        $total->leftjoin('chart_of_account_types', 'chart_of_accounts.type', 'chart_of_account_types.id');
        $total->where('transaction_lines.created_by', \Auth::user()->creatorId());
        $total->where('transaction_lines.account_id', $account_id);
        $total->where('transaction_lines.date', '>=', $start);
        $total->where('transaction_lines.date', '<=', $end);
        $total->groupBy('account_id');
        $total = $total->get()->toArray();

        $balance = 0;
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($total as $key => $record) {
            $totalDebit = $record['totalDebit'];
            $totalCredit = $record['totalCredit'];
        }

        $balance += $totalCredit - $totalDebit;

        return $balance;
    }

    public static function getBankOpeningBalance($account_id, $start_date = null)
    {
        $bankOpeningBalance = BankAccount::where('created_by', \Auth::user()->creatorId())
            ->where('chart_account_id', $account_id)
            ->value('opening_balance');

        $transactionsBeforeStart = TransactionLines::where('created_by', \Auth::user()->creatorId())
            ->where('account_id', $account_id)
            ->where('date', '<', $start_date)
            ->selectRaw('SUM(credit) as totalCredit, SUM(debit) as totalDebit')
            ->first();


        $openingBalanceAdjustment = ($transactionsBeforeStart->totalCredit ?? 0) - ($transactionsBeforeStart->totalDebit ?? 0);


        return $bankOpeningBalance - $openingBalanceAdjustment;
    }

    public static function addTransactionLines($data)
    {
        $existingTransaction = TransactionLines::where('reference_id', $data['reference_id'])
            ->where('reference_sub_id', $data['reference_sub_id'])->where('reference', $data['reference'])
            ->first();
        if ($existingTransaction) {
            $transactionLines = $existingTransaction;
        } else {
            $transactionLines = new TransactionLines();
        }
        $transactionLines->account_id = $data['account_id'];
        $transactionLines->reference = $data['reference'];
        $transactionLines->reference_id = $data['reference_id'];
        $transactionLines->reference_sub_id = $data['reference_sub_id'];
        $transactionLines->date = $data['date'];
        if ($data['transaction_type'] == "Credit") {
            $transactionLines->credit = $data['transaction_amount'];
            $transactionLines->debit = 0;
        } else {
            $transactionLines->credit = 0;
            $transactionLines->debit = $data['transaction_amount'];
        }
        $transactionLines->created_by = \Auth::user()->creatorId();
        $transactionLines->save();
    }

    public static function billInvoiceData($array, $request, $yearList)
    {
        $billsum = [];
        foreach ($array as $category => $categoryData) {
            $billchartArr = [];
            foreach ($yearList as $key => $value) {

                if ($request->period === 'quarterly') {
                    for ($i = 0; $i < 12; $i += 3) {
                        $invoicequarterArr = array_slice($categoryData[$key], $i, 3);
                        $billchartArr[] = array_sum($invoicequarterArr);
                    }
                } elseif ($request->period === 'half-yearly') {
                    for ($i = 0; $i < 12; $i += 6) {
                        $InvoicehalfYearArr = array_slice($categoryData[$key], $i, 6);
                        $billchartArr[] = array_sum($InvoicehalfYearArr);
                    }
                } elseif ($request->period === 'yearly') {
                    for ($i = 0; $i < 12; $i += 12) {
                        $invoiceyearArr = array_slice($categoryData[$key], $i, 12);
                        $billchartArr[] = array_sum($invoiceyearArr);
                    }
                } else {
                    // Monthly
                    $billchartArr = $categoryData[$key];
                }
            }

            $billdata = [
                "category" => $category,
                "data" => $billchartArr,
            ];

            $billsum[] = $billdata;
        }
        return $billsum;
    }

    public static function revenuePaymentData($category, $categoryData, $request, $yearList)
    {

        $chartArr = [];
        foreach ($yearList as $key => $value) {
            if ($request->period === 'quarterly') {
                for ($i = 0; $i < 12; $i += 3) {
                    $quarterArr = array_slice($categoryData[$key], $i, 3);
                    $chartArr[] = array_sum($quarterArr);
                }
            } elseif ($request->period === 'half-yearly') {
                for ($i = 0; $i < 12; $i += 6) {
                    $halfYearArr = array_slice($categoryData[$key], $i, 6);
                    $chartArr[] = array_sum($halfYearArr);
                }
            } elseif ($request->period === 'yearly') {

                for ($i = 0; $i < 12; $i += 12) {
                    $yearArr = array_slice($categoryData[$key], $i, 12);
                    $chartArr[] = array_sum($yearArr);
                }
            } else {
                $chartArr = $categoryData[$key];
                $billchartArr = $categoryData[$key];
            }
        }

        $chartdata = [
            "category" => $category,
            "data" => $chartArr,
        ];

        return $chartdata;
    }

    public static function billData($billArray, $request, $yearList)
    {
        $billsum = [];
        foreach ($billArray as $category => $categoryData) {
            $billchartArr = [];
            foreach ($yearList as $key => $value) {
                if ($request->period === 'quarterly') {
                    for ($i = 0; $i < 12; $i += 3) {
                        $invoicequarterArr = array_slice($categoryData[$key], $i, 3);
                        $billchartArr[] = array_sum($invoicequarterArr);
                    }
                } elseif ($request->period === 'half-yearly') {
                    for ($i = 0; $i < 12; $i += 6) {
                        $InvoicehalfYearArr = array_slice($categoryData[$key], $i, 6);
                        $billchartArr[] = array_sum($InvoicehalfYearArr);
                    }
                } elseif ($request->period === 'yearly') {
                    for ($i = 0; $i < 12; $i += 12) {
                        $invoiceyearArr = array_slice($categoryData[$key], $i, 12);
                        $billchartArr[] = array_sum($invoiceyearArr);
                    }
                } else {
                    // Monthly
                    $billchartArr = $categoryData[$key];
                }
            }
            $billdata = [
                "category" => $category,
                "data" => $billchartArr,
            ];
            $billsum[] = $billdata;
        }
        return $billsum;
    }
    public static function expenseData($category, $categoryData, $request, $yearList)
    {
        $chartArr = [];
        foreach ($yearList as $key => $value) {
            if ($request->period === 'quarterly') {
                for ($i = 0; $i < 12; $i += 3) {
                    $quarterArr = array_slice($categoryData[$key], $i, 3);
                    $chartArr[] = array_sum($quarterArr);
                }
            } elseif ($request->period === 'half-yearly') {
                for ($i = 0; $i < 12; $i += 6) {
                    $halfYearArr = array_slice($categoryData[$key], $i, 6);
                    $chartArr[] = array_sum($halfYearArr);
                }
            } elseif ($request->period === 'yearly') {
                for ($i = 0; $i < 12; $i += 12) {
                    $yearArr = array_slice($categoryData[$key], $i, 12);
                    $chartArr[] = array_sum($yearArr);
                }
            } else {
                $chartArr = $categoryData[$key];
            }
        }
        $chartdata = [
            "category" => $category,
            "data" => $chartArr,
        ];
        return $chartdata;
    }
    public static function totalData($billArr, $expenseArr, $request, $yearList)
    {
        $chartExpenseArr = [];
        foreach ($yearList as $year) {
            if ($request->period === 'quarterly') {
                for ($i = 0; $i < 12; $i += 3) {
                    $quarterbillArr = array_slice($billArr[$year], $i, 3);
                    $quarterexpenseArr = array_slice($expenseArr[$year], $i, 3);
                    $chartbillArr[$year][$i] = array_sum($quarterbillArr);
                    $chartexpenseArr[$year][$i] = array_sum($quarterexpenseArr);
                }
            } elseif ($request->period === 'half-yearly') {
                for ($i = 0; $i < 12; $i += 6) {
                    $halfYearBillArr = array_slice($billArr[$year], $i, 6);
                    $halfYearExpenseArr = array_slice($expenseArr[$year], $i, 6);
                    $chartbillArr[$year][$i] = array_sum($halfYearBillArr);
                    $chartexpenseArr[$year][$i] = array_sum($halfYearExpenseArr);
                }
            } elseif ($request->period === 'yearly') {
                for ($i = 0; $i < 12; $i += 12) {
                    $YearBillArr = array_slice($billArr[$year], $i, 12);
                    $YearExpenseArr = array_slice($expenseArr[$year], $i, 12);
                    $chartbillArr[$year][$i] = array_sum($YearBillArr);
                    $chartexpenseArr[$year][$i] = array_sum($YearExpenseArr);
                }
            } else {
                for ($i = 1; $i <= 12; $i++) {
                    $chartbillArr[$year][] = $billArr[$year][$i];
                    $chartexpenseArr[$year][] = $expenseArr[$year][$i];
                }
            }
        }
        if (isset($chartexpenseArr) && isset($chartbillArr)) {
            foreach ($chartexpenseArr as $year => $values) {
                if (isset($chartbillArr[$year])) {
                    $chartExpenseArr[] = array_map(function ($a, $b) {
                        return $a + $b;
                    }, $chartexpenseArr[$year], $chartbillArr[$year]);
                } else {
                    $chartExpenseArr[$year] = $values;
                }
            }
        }
        return $chartExpenseArr;
    }

    public static function totalSum($array, $request, $yearList)
    {

        $totalArr = [];
        foreach ($yearList as $year) {
            if ($request->period === 'quarterly') {
                for ($i = 0; $i < 12; $i += 3) {
                    $quarterArr = array_slice($array[$year], $i, 3);
                    $totalArr[$year][$i] = array_sum($quarterArr);
                }
            } elseif ($request->period === 'half-yearly') {
                for ($i = 0; $i < 12; $i += 6) {
                    $halfYearArr = array_slice($array[$year], $i, 6);
                    $totalArr[$year][$i] = array_sum($halfYearArr);
                }
            } elseif ($request->period === 'yearly') {
                for ($i = 0; $i < 12; $i += 12) {
                    $YearArr = array_slice($array[$year], $i, 12);
                    $totalArr[$year][$i] = array_sum($YearArr);
                }
            } else {
                for ($i = 1; $i <= 12; $i++) {
                    $totalArr[] = $array[$year][$i];
                }
            }
        }
        return $totalArr;
    }

    public static $chartOfAccountType = [
        'assets' => 'Assets',
        'liabilities' => 'Liabilities',
        'equity' => 'Equity',
        'income' => 'Income',
        'costs of goods sold' => 'Costs of Goods Sold',
        'expenses' => 'Expenses',

    ];

    public static $chartOfAccountSubType = array(
        "assets" => array(
            '1' => 'Current Asset',
            '2' => 'Inventory Asset',
            '3' => 'Non-current Asset',
        ),
        "liabilities" => array(
            '1' => 'Current Liabilities',
            '2' => 'Long Term Liabilities',
            '3' => 'Share Capital',
            '4' => 'Retained Earnings',
        ),
        "equity" => array(
            '1' => 'Owners Equity',
        ),
        "income" => array(
            '1' => 'Sales Revenue',
            '2' => 'Other Revenue',
        ),
        "costs of goods sold" => array(
            '1' => 'Costs of Goods Sold',
        ),
        "expenses" => array(
            '1' => 'Payroll Expenses',
            '2' => 'General and Administrative expenses',
        ),

    );

    public static function chartOfAccountTypeData($company_id)
    {
        $chartOfAccountTypes = self::$chartOfAccountType;
        foreach ($chartOfAccountTypes as $k => $type) {

            $accountType = ChartOfAccountType::create(
                [
                    'name' => $type,
                    'created_by' => $company_id,
                ]
            );

            $chartOfAccountSubTypes = self::$chartOfAccountSubType;

            foreach ($chartOfAccountSubTypes[$k] as $subType) {
                ChartOfAccountSubType::create(
                    [
                        'name' => $subType,
                        'type' => $accountType->id,
                        'created_by' => $company_id,
                    ]
                );
            }
        }
    }

    public static $chartOfAccount = array(

        [
            'code' => '1060',
            'name' => 'Checking Account',
            'type' => 1,
            'sub_type' => 1,
        ],
        [
            'code' => '1065',
            'name' => 'Petty Cash',
            'type' => 1,
            'sub_type' => 1,
        ],
        [
            'code' => '1200',
            'name' => 'Account Receivables',
            'type' => 1,
            'sub_type' => 1,
        ],
        [
            'code' => '1205',
            'name' => 'Allowance for doubtful accounts',
            'type' => 1,
            'sub_type' => 1,
        ],
        [
            'code' => '1510',
            'name' => 'Inventory',
            'type' => 1,
            'sub_type' => 2,
        ],
        [
            'code' => '1520',
            'name' => 'Stock of Raw Materials',
            'type' => 1,
            'sub_type' => 2,
        ],
        [
            'code' => '1530',
            'name' => 'Stock of Work In Progress',
            'type' => 1,
            'sub_type' => 2,
        ],
        [
            'code' => '1540',
            'name' => 'Stock of Finished Goods',
            'type' => 1,
            'sub_type' => 2,
        ],
        [
            'code' => '1550',
            'name' => 'Goods Received Clearing account',
            'type' => 1,
            'sub_type' => 2,
        ],
        [
            'code' => '1810',
            'name' => 'Land and Buildings',
            'type' => 1,
            'sub_type' => 3,
        ],
        [
            'code' => '1820',
            'name' => 'Office Furniture and Equipement',
            'type' => 1,
            'sub_type' => 3,
        ],
        [
            'code' => '1825',
            'name' => 'Accum.depreciation-Furn. and Equip',
            'type' => 1,
            'sub_type' => 3,
        ],
        [
            'code' => '1840',
            'name' => 'Motor Vehicle',
            'type' => 1,
            'sub_type' => 3,
        ],
        [
            'code' => '1845',
            'name' => 'Accum.depreciation-Motor Vehicle',
            'type' => 1,
            'sub_type' => 3,
        ],
        [
            'code' => '2100',
            'name' => 'Account Payable',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2105',
            'name' => 'Deferred Income',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2110',
            'name' => 'Accrued Income Tax-Central',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2120',
            'name' => 'Income Tax Payable',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2130',
            'name' => 'Accrued Franchise Tax',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2140',
            'name' => 'Vat Provision',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2145',
            'name' => 'Purchase Tax',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2150',
            'name' => 'VAT Pay / Refund',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2151',
            'name' => 'Zero Rated',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2152',
            'name' => 'Capital import',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2153',
            'name' => 'Standard Import',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2154',
            'name' => 'Capital Standard',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2155',
            'name' => 'Vat Exempt',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2160',
            'name' => 'Accrued Use Tax Payable',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2210',
            'name' => 'Accrued Wages',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2220',
            'name' => 'Accrued Comp Time',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2230',
            'name' => 'Accrued Holiday Pay',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2240',
            'name' => 'Accrued Vacation Pay',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2310',
            'name' => 'Accr. Benefits - Central Provident Fund',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2320',
            'name' => 'Accr. Benefits - Stock Purchase',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2330',
            'name' => 'Accr. Benefits - Med, Den',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2340',
            'name' => 'Accr. Benefits - Payroll Taxes',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2350',
            'name' => 'Accr. Benefits - Credit Union',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2360',
            'name' => 'Accr. Benefits - Savings Bond',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2370',
            'name' => 'Accr. Benefits - Group Insurance',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2380',
            'name' => 'Accr. Benefits - Charity Cont.',
            'type' => 2,
            'sub_type' => 4,
        ],
        [
            'code' => '2620',
            'name' => 'Bank Loans',
            'type' => 2,
            'sub_type' => 5,
        ],
        [
            'code' => '2680',
            'name' => 'Loans from Shareholders',
            'type' => 2,
            'sub_type' => 5,
        ],
        [
            'code' => '3350',
            'name' => 'Common Shares',
            'type' => 2,
            'sub_type' => 6,
        ],
        [
            'code' => '3590',
            'name' => 'Reserves and Surplus',
            'type' => 2,
            'sub_type' => 7,
        ],
        [
            'code' => '3595',
            'name' => 'Owners Drawings',
            'type' => 2,
            'sub_type' => 7,
        ],
        [
            'code' => '3020',
            'name' => 'Opening Balances and adjustments',
            'type' => 3,
            'sub_type' => 8,
        ],
        [
            'code' => '3025',
            'name' => 'Owners Contribution',
            'type' => 3,
            'sub_type' => 8,
        ],
        [
            'code' => '3030',
            'name' => 'Profit and Loss ( current Year)',
            'type' => 3,
            'sub_type' => 8,
        ],
        [
            'code' => '3035',
            'name' => 'Retained income',
            'type' => 3,
            'sub_type' => 8,
        ],
        [
            'code' => '4010',
            'name' => 'Sales Income',
            'type' => 4,
            'sub_type' => 9,
        ],
        [
            'code' => '4020',
            'name' => 'Service Income',
            'type' => 4,
            'sub_type' => 9,
        ],
        [
            'code' => '4430',
            'name' => 'Shipping and Handling',
            'type' => 4,
            'sub_type' => 10,
        ],
        [
            'code' => '4435',
            'name' => 'Sundry Income',
            'type' => 4,
            'sub_type' => 10,
        ],
        [
            'code' => '4440',
            'name' => 'Interest Received',
            'type' => 4,
            'sub_type' => 10,
        ],
        [
            'code' => '4450',
            'name' => 'Foreign Exchange Gain',
            'type' => 4,
            'sub_type' => 10,
        ],
        [
            'code' => '4500',
            'name' => 'Unallocated Income',
            'type' => 4,
            'sub_type' => 10,
        ],
        [
            'code' => '4510',
            'name' => 'Discounts Received',
            'type' => 4,
            'sub_type' => 10,
        ],
        [
            'code' => '5005',
            'name' => 'Cost of Sales- On Services',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5010',
            'name' => 'Cost of Sales - Purchases',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5015',
            'name' => 'Operating Costs',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5020',
            'name' => 'Material Usage Varaiance',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5025',
            'name' => 'Breakage and Replacement Costs',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5030',
            'name' => 'Consumable Materials',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5035',
            'name' => 'Sub-contractor Costs',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5040',
            'name' => 'Purchase Price Variance',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5045',
            'name' => 'Direct Labour - COS',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5050',
            'name' => 'Purchases of Materials',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5060',
            'name' => 'Discounts Received',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5100',
            'name' => 'Freight Costs',
            'type' => 5,
            'sub_type' => 11,
        ],
        [
            'code' => '5410',
            'name' => 'Salaries and Wages',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5415',
            'name' => 'Directors Fees & Remuneration',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5420',
            'name' => 'Wages - Overtime',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5425',
            'name' => 'Members Salaries',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5430',
            'name' => 'UIF Payments',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5440',
            'name' => 'Payroll Taxes',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5450',
            'name' => 'Workers Compensation ( Coida )',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5460',
            'name' => 'Normal Taxation Paid',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5470',
            'name' => 'General Benefits',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5510',
            'name' => 'Provisional Tax Paid',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5520',
            'name' => 'Inc Tax Exp - State',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5530',
            'name' => 'Taxes - Real Estate',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5540',
            'name' => 'Taxes - Personal Property',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5550',
            'name' => 'Taxes - Franchise',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5560',
            'name' => 'Taxes - Foreign Withholding',
            'type' => 6,
            'sub_type' => 12,
        ],
        [
            'code' => '5610',
            'name' => 'Accounting Fees',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5615',
            'name' => 'Advertising and Promotions',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5620',
            'name' => 'Bad Debts',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5625',
            'name' => 'Courier and Postage',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5660',
            'name' => 'Depreciation Expense',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5685',
            'name' => 'Insurance Expense',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5690',
            'name' => 'Bank Charges',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5695',
            'name' => 'Interest Paid',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5700',
            'name' => 'Office Expenses - Consumables',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5705',
            'name' => 'Printing and Stationary',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5710',
            'name' => 'Security Expenses',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5715',
            'name' => 'Subscription - Membership Fees',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5755',
            'name' => 'Electricity, Gas and Water',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5760',
            'name' => 'Rent Paid',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5765',
            'name' => 'Repairs and Maintenance',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5770',
            'name' => 'Motor Vehicle Expenses',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5771',
            'name' => 'Petrol and Oil',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5775',
            'name' => 'Equipment Hire - Rental',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5780',
            'name' => 'Telephone and Internet',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5785',
            'name' => 'Travel and Accommodation',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5786',
            'name' => 'Meals and Entertainment',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5787',
            'name' => 'Staff Training',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5790',
            'name' => 'Utilities',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5791',
            'name' => 'Computer Expenses',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5795',
            'name' => 'Registrations',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5800',
            'name' => 'Licenses',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '5810',
            'name' => 'Foreign Exchange Loss',
            'type' => 6,
            'sub_type' => 13,
        ],
        [
            'code' => '9990',
            'name' => 'Profit and Loss',
            'type' => 6,
            'sub_type' => 13,
        ],

    );

    public static $chartOfAccount1 = array(

        [
            'code' => '1060',
            'name' => 'Checking Account',
            'type' => 'Assets',
            'sub_type' => 'Current Asset',
        ],
        [
            'code' => '1065',
            'name' => 'Petty Cash',
            'type' => 'Assets',
            'sub_type' => 'Current Asset',
        ],
        [
            'code' => '1200',
            'name' => 'Account Receivables',
            'type' => 'Assets',
            'sub_type' => 'Current Asset',
        ],
        [
            'code' => '1205',
            'name' => 'Allowance for doubtful accounts',
            'type' => 'Assets',
            'sub_type' => 'Current Asset',
        ],
        [
            'code' => '1510',
            'name' => 'Inventory',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1520',
            'name' => 'Stock of Raw Materials',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1530',
            'name' => 'Stock of Work In Progress',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1540',
            'name' => 'Stock of Finished Goods',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1550',
            'name' => 'Goods Received Clearing account',
            'type' => 'Assets',
            'sub_type' => 'Inventory Asset',
        ],
        [
            'code' => '1810',
            'name' => 'Land and Buildings',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '1820',
            'name' => 'Office Furniture and Equipement',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '1825',
            'name' => 'Accum.depreciation-Furn. and Equip',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '1840',
            'name' => 'Motor Vehicle',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '1845',
            'name' => 'Accum.depreciation-Motor Vehicle',
            'type' => 'Assets',
            'sub_type' => 'Non-current Asset',
        ],
        [
            'code' => '2100',
            'name' => 'Account Payable',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2105',
            'name' => 'Deferred Income',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2110',
            'name' => 'Accrued Income Tax-Central',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2120',
            'name' => 'Income Tax Payable',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2130',
            'name' => 'Accrued Franchise Tax',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2140',
            'name' => 'Vat Provision',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2145',
            'name' => 'Purchase Tax',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2150',
            'name' => 'VAT Pay / Refund',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2151',
            'name' => 'Zero Rated',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2152',
            'name' => 'Capital import',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2153',
            'name' => 'Standard Import',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2154',
            'name' => 'Capital Standard',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2155',
            'name' => 'Vat Exempt',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2160',
            'name' => 'Accrued Use Tax Payable',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2210',
            'name' => 'Accrued Wages',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2220',
            'name' => 'Accrued Comp Time',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2230',
            'name' => 'Accrued Holiday Pay',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2240',
            'name' => 'Accrued Vacation Pay',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2310',
            'name' => 'Accr. Benefits - Central Provident Fund',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2320',
            'name' => 'Accr. Benefits - Stock Purchase',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2330',
            'name' => 'Accr. Benefits - Med, Den',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2340',
            'name' => 'Accr. Benefits - Payroll Taxes',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2350',
            'name' => 'Accr. Benefits - Credit Union',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2360',
            'name' => 'Accr. Benefits - Savings Bond',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2370',
            'name' => 'Accr. Benefits - Group Insurance',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2380',
            'name' => 'Accr. Benefits - Charity Cont.',
            'type' => 'Liabilities',
            'sub_type' => 'Current Liabilities',
        ],
        [
            'code' => '2620',
            'name' => 'Bank Loans',
            'type' => 'Liabilities',
            'sub_type' => 'Long Term Liabilities',
        ],
        [
            'code' => '2680',
            'name' => 'Loans from Shareholders',
            'type' => 'Liabilities',
            'sub_type' => 'Long Term Liabilities',
        ],
        [
            'code' => '3350',
            'name' => 'Common Shares',
            'type' => 'Liabilities',
            'sub_type' => 'Share Capital',
        ],
        [
            'code' => '3590',
            'name' => 'Reserves and Surplus',
            'type' => 'Liabilities',
            'sub_type' => 'Retained Earnings',
        ],
        [
            'code' => '3595',
            'name' => 'Owners Drawings',
            'type' => 'Liabilities',
            'sub_type' => 'Retained Earnings',
        ],
        [
            'code' => '3020',
            'name' => 'Opening Balances and adjustments',
            'type' => 'Equity',
            'sub_type' => 'Owners Equity',
        ],
        [
            'code' => '3025',
            'name' => 'Owners Contribution',
            'type' => 'Equity',
            'sub_type' => 'Owners Equity',
        ],
        [
            'code' => '3030',
            'name' => 'Profit and Loss ( current Year)',
            'type' => 'Equity',
            'sub_type' => 'Owners Equity',
        ],
        [
            'code' => '3035',
            'name' => 'Retained income',
            'type' => 'Equity',
            'sub_type' => 'Owners Equity',
        ],
        [
            'code' => '4010',
            'name' => 'Sales Income',
            'type' => 'Income',
            'sub_type' => 'Sales Revenue',
        ],
        [
            'code' => '4020',
            'name' => 'Service Income',
            'type' => 'Income',
            'sub_type' => 'Sales Revenue',
        ],
        [
            'code' => '4430',
            'name' => 'Shipping and Handling',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4435',
            'name' => 'Sundry Income',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4440',
            'name' => 'Interest Received',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4450',
            'name' => 'Foreign Exchange Gain',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4500',
            'name' => 'Unallocated Income',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '4510',
            'name' => 'Discounts Received',
            'type' => 'Income',
            'sub_type' => 'Other Revenue',
        ],
        [
            'code' => '5005',
            'name' => 'Cost of Sales- On Services',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5010',
            'name' => 'Cost of Sales - Purchases',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5015',
            'name' => 'Operating Costs',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5020',
            'name' => 'Material Usage Varaiance',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5025',
            'name' => 'Breakage and Replacement Costs',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5030',
            'name' => 'Consumable Materials',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5035',
            'name' => 'Sub-contractor Costs',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5040',
            'name' => 'Purchase Price Variance',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5045',
            'name' => 'Direct Labour - COS',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5050',
            'name' => 'Purchases of Materials',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5060',
            'name' => 'Discounts Received',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5100',
            'name' => 'Freight Costs',
            'type' => 'Costs of Goods Sold',
            'sub_type' => 'Costs of Goods Sold',
        ],
        [
            'code' => '5410',
            'name' => 'Salaries and Wages',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5415',
            'name' => 'Directors Fees & Remuneration',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5420',
            'name' => 'Wages - Overtime',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5425',
            'name' => 'Members Salaries',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5430',
            'name' => 'UIF Payments',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5440',
            'name' => 'Payroll Taxes',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5450',
            'name' => 'Workers Compensation ( Coida )',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5460',
            'name' => 'Normal Taxation Paid',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5470',
            'name' => 'General Benefits',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5510',
            'name' => 'Provisional Tax Paid',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5520',
            'name' => 'Inc Tax Exp - State',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5530',
            'name' => 'Taxes - Real Estate',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5540',
            'name' => 'Taxes - Personal Property',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5550',
            'name' => 'Taxes - Franchise',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5560',
            'name' => 'Taxes - Foreign Withholding',
            'type' => 'Expenses',
            'sub_type' => 'Payroll Expenses',
        ],
        [
            'code' => '5610',
            'name' => 'Accounting Fees',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5615',
            'name' => 'Advertising and Promotions',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5620',
            'name' => 'Bad Debts',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5625',
            'name' => 'Courier and Postage',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5660',
            'name' => 'Depreciation Expense',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5685',
            'name' => 'Insurance Expense',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5690',
            'name' => 'Bank Charges',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5695',
            'name' => 'Interest Paid',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5700',
            'name' => 'Office Expenses - Consumables',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5705',
            'name' => 'Printing and Stationary',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5710',
            'name' => 'Security Expenses',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5715',
            'name' => 'Subscription - Membership Fees',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5755',
            'name' => 'Electricity, Gas and Water',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5760',
            'name' => 'Rent Paid',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5765',
            'name' => 'Repairs and Maintenance',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5770',
            'name' => 'Motor Vehicle Expenses',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5771',
            'name' => 'Petrol and Oil',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5775',
            'name' => 'Equipment Hire - Rental',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5780',
            'name' => 'Telephone and Internet',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5785',
            'name' => 'Travel and Accommodation',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5786',
            'name' => 'Meals and Entertainment',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5787',
            'name' => 'Staff Training',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5790',
            'name' => 'Utilities',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5791',
            'name' => 'Computer Expenses',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5795',
            'name' => 'Registrations',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5800',
            'name' => 'Licenses',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '5810',
            'name' => 'Foreign Exchange Loss',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],
        [
            'code' => '9990',
            'name' => 'Profit and Loss',
            'type' => 'Expenses',
            'sub_type' => 'General and Administrative expenses',
        ],

    );


    // chart of account for new company
    public static function chartOfAccountData1($user)
    {
        $chartOfAccounts = self::$chartOfAccount1;

        foreach ($chartOfAccounts as $account) {

            $type = ChartOfAccountType::where('created_by', $user)->where('name', $account['type'])->first();
            $sub_type = ChartOfAccountSubType::where('type', $type->id)->where('name', $account['sub_type'])->first();

            ChartOfAccount::create(
                [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'type' => $type->id,
                    'sub_type' => $sub_type->id,
                    'is_enabled' => 1,
                    'created_by' => $user,
                ]
            );
        }
    }

    public static function chartOfAccountData($user)
    {
        $chartOfAccounts = self::$chartOfAccount;
        // dd($user, $chartOfAccounts);
        foreach ($chartOfAccounts as $account) {
            ChartOfAccount::create(
                [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'sub_type' => $account['sub_type'],
                    'is_enabled' => 1,
                    'created_by' => $user->id,
                ]
            );
        }
    }

    public static function check_file($path)
    {
        if (!empty($path)) {

            $settings = Utility::settings();
            if ($settings['storage_setting'] == 'local' || $settings['storage_setting'] == null) {

                return Storage::disk($settings['storage_setting'])->exists($path);
            } else {

                if ($settings['storage_setting'] == 's3') {
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.url' => $settings['s3_url'],
                            'filesystems.disks.s3.endpoint' => $settings['s3_endpoint'],
                        ]
                    );
                } else if ($settings['storage_setting'] == 'wasabi') {
                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.root' => $settings['wasabi_root'],
                            'filesystems.disks.wasabi.endpoint' => $settings['wasabi_url'],
                            'filesystems.disks.wasabi.use_path_style_endpoint' => false
                        ]
                    );
                }

                try {
                    return Storage::disk($settings['storage_setting'])->exists($path);
                } catch (\Exception $e) {
                    return 0;
                }

            }
        } else {
            return 0;
        }
    }

    public static function getCurrencySymbol($key)
    {
        $data = DB::table('admin_payment_settings');

        if (Auth::check()) {
            $data->where('name', $key)->where('created_by', '=', Auth::user()->creatorId());
        } else {
            $data->where('name', $key)->where('created_by', '=', 1);
        }
        return $data->pluck('value')->first();
    }

    public static function generateReferralCode()
    {
        do {
            $referralCode = rand(100000, 999999);
        } while (User::where('referral_code', $referralCode)->exists());

        return $referralCode;
    }

    public static function referralTransaction($plan, $company = '')
    {
        if ($company != '') {
            $objUser = $company;
        } else {
            $objUser = \Auth::user();
        }

        $user = ReferralTransaction::where('company_id', $objUser->id)->first();

        $referralSetting = ReferralSetting::where('created_by', 1)->first();

        if ($objUser->used_referral_code != 0 && $user == null && (isset($referralSetting) && $referralSetting->is_enable == 1)) {
            $transaction = new ReferralTransaction();
            $transaction->company_id = $objUser->id;
            $transaction->plan_id = $plan->id;
            $transaction->plan_price = $plan->price;
            $transaction->commission = $referralSetting->percentage;
            $transaction->referral_code = $objUser->used_referral_code;
            $transaction->save();

        }
    }

    public static function formatPrice($price)
    {
        return number_format($price, 2);
    }

    public static function sendPayslipNotification($employee, $payroll, $payrollMonth)
    {
        if (!$employee || !$employee->email) {
            return [
                'is_success' => false,
                'error' => 'Employee email not found'
            ];
        }

        $mailTo = [$employee->email];
        $obj = [
            'employee_name' => $employee->name,
            'month' => \Carbon\Carbon::parse($payrollMonth)->format('F'),
            'year' => \Carbon\Carbon::parse($payrollMonth)->format('Y'),
            'basic_salary' => $payroll->basic_salary,
            'net_salary' => $payroll->net_salary,
        ];
        
        try {
            return self::sendEmailTemplate('payslip_generated', $mailTo, $obj);
        } catch (\Exception $e) {
            \Log::error('Payslip notification error: ' . $e->getMessage());
            return [
                'is_success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
  public static function employeePayslipDetail($employeeId, $month)
    {
        // allowance
        $earning['allowance'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totalAllowance = 0;

        $arrayJson = json_decode($earning['allowance']);
        foreach ($arrayJson as $earn) {
            // dd($earn->basic_salary);
            $allowancejson = json_decode($earn->allowance);
            foreach ($allowancejson as $allowances) {
                if ($allowances->type == 'percentage') {
                    $empall = $allowances->amount * $earn->basic_salary / 100;
                } else {
                    $empall = $allowances->amount;
                }
                $totalAllowance += $empall;
            }
        }

        // commission
        $earning['commission'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totalCommission = 0;

        $arrayJson = json_decode($earning['commission']);

        foreach ($arrayJson as $earn) {
            $commissionjson = json_decode($earn->commission);

            foreach ($commissionjson as $commissions) {

                if ($commissions->type == 'percentage') {
                    $empcom = $commissions->amount * $earn->basic_salary / 100;
                } else {
                    $empcom = $commissions->amount;
                }
                $totalCommission += $empcom;
            }
        }

        // otherpayment
        $earning['otherPayment'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totalotherpayment = 0;

        $arrayJson = json_decode($earning['otherPayment']);

        foreach ($arrayJson as $earn) {
            $otherpaymentjson = json_decode($earn->other_payment);

            foreach ($otherpaymentjson as $otherpay) {
                if ($otherpay->type == 'percentage') {
                    $empotherpay = $otherpay->amount * $earn->basic_salary / 100;
                } else {
                    $empotherpay = $otherpay->amount;
                }
                $totalotherpayment += $empotherpay;
            }
        }

        //overtime
        $earning['overTime'] = Payslip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $ot = 0;

        $arrayJson = json_decode($earning['overTime']);
        foreach ($arrayJson as $overtime) {
            $overtimes = json_decode($overtime->overtime);
            foreach ($overtimes as $overt) {
                $OverTime = $overt->number_of_days * $overt->hours * $overt->rate;
                $ot += $OverTime;
            }
        }

        // loan
        $deduction['loan'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totalloan = 0;

        $arrayJson = json_decode($deduction['loan']);

        foreach ($arrayJson as $loan) {
            $loans = json_decode($loan->loan);

            foreach ($loans as $emploans) {

                if ($emploans->type == 'percentage') {
                    $emploan = $emploans->amount * $loan->basic_salary / 100;
                } else {
                    $emploan = $emploans->amount;
                }
                $totalloan += $emploan;
            }
        }

        // saturation_deduction
        $deduction['deduction'] = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->get();

        $employess = Employee::find($employeeId);

        $totaldeduction = 0;

        $arrayJson = json_decode($deduction['deduction']);

        foreach ($arrayJson as $deductions) {
            // dd($deductions->basic_salary);
            $deduc = json_decode($deductions->saturation_deduction);
            foreach ($deduc as $deduction_option) {
                if ($deduction_option->type == 'percentage') {
                    $empdeduction = $deduction_option->amount * $deductions->basic_salary / 100;
                } else {
                    $empdeduction = $deduction_option->amount;
                }
                $totaldeduction += $empdeduction;
            }
        }

        $payslip['earning'] = $earning;
        $payslip['totalEarning'] = $totalAllowance + $totalCommission + $totalotherpayment + $ot;
        $payslip['deduction'] = $deduction;
        $payslip['totalDeduction'] = $totalloan + $totaldeduction;

        return $payslip;
    }

}
