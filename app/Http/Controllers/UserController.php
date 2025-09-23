<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\Mail\UserCreate;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCompany;
use App\Models\Utility;
use Auth;
use File;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Session;
use Spatie\Permission\Models\Role;
use Lab404\Impersonate\Impersonate;

class UserController extends Controller
{

    public function index()
    {
        $user = \Auth::user();
        if (\Auth::user()->can('manage user')) {
            if (\Auth::user()->type == 'super admin') {
                $users = User::where('created_by', '=', $user->creatorId())->where('type', '=', 'company')->get();
            } else {
                $users = User::where('created_by', '=', $user->creatorId())->where('type', '!=', 'client')->get();
            }

            return view('user.index')->with('users', $users);
        } else {
            return redirect()->back();
        }
    }

    public function create()
    {
        $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();

        $user  = \Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->get()->pluck('name', 'id');
        if (\Auth::user()->can('create user')) {
            return view('user.create', compact('roles', 'customFields'));
        } else {
            return redirect()->back();
        }
    }

public function store(Request $request)
{
    if (!\Auth::user()->can('create user')) {
        return redirect()->back();
    }

    $default_language        = DB::table('settings')->select('value')->where('name', 'default_language')->first();
    $company_default_language= DB::table('settings')->select('value')->where('name', 'company_default_language')->first();
    $userpassword            = $request->input('password');

    $avatarRule = ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'];

    if (\Auth::user()->type == 'super admin') {
        $validator = \Validator::make(
            $request->all(),
            [
                'name'  => 'required|max:120',
                'email' => 'required|email|unique:users',
                'avatar'=> $avatarRule,
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $enableLogin = 0;
        if (!empty($request->password_switch) && $request->password_switch == 'on') {
            $enableLogin = 1;
            $validator = \Validator::make($request->all(), ['password' => 'required|min:6']);
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }
        }

        $user                       = new User();
        $user['name']               = $request->name;
        $user['email']              = $request->email;
        $user['email_verified_at']  = now();
        $psw                        = $request->password;
        $user['password']           = !empty($userpassword) ? \Hash::make($userpassword) : null;
        $user['type']               = 'company';
        $user['lang']               = !empty($default_language) ? $default_language->value : '';
        $user['created_by']         = \Auth::user()->creatorId();
        $user['plan']               = Plan::first()->id;
        $user['is_enable_login']    = $enableLogin;
        $user['referral_code']      = Utility::generateReferralCode();

        $avatarFilename = null;
        if ($request->hasFile('avatar')) {
            $filenameWithExt = $request->file('avatar')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('avatar')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            $dir = $settings['storage_setting'] == 'local' ? 'uploads/avatar/' : 'uploads/avatar';

            $upload = Utility::upload_file($request, 'avatar', $fileNameToStore, $dir, []);
            if (!($upload['flag'] ?? 0)) {
                return redirect()->back()->with('error', __($upload['msg'] ?? 'Avatar upload failed.'));
            }
            $avatarFilename = $fileNameToStore;
        }
        if ($avatarFilename) {
            $user['avatar'] = $avatarFilename;
        }

        $user->save();
        CustomField::saveData($user, $request->customField);

        $role_r = Role::findByName('company');
        $user->assignRole($role_r);

        $user->userDefaultDataRegister($user->id);
        Utility::chartOfAccountTypeData($user->id);
        Utility::chartOfAccountData1($user->id);
        $settings = Utility::settings();
            $company_name = $settings['title_text'];
        $uArr = ['email' => $user->email, 'password' => $psw,'company_name' => $company_name,'name'=> $customer->name];
        try { Utility::sendEmailTemplate('user_created', [$user->id => $user->email], $uArr); }
        catch (\Exception $e) { $smtp_error = __('E-Mail has been not sent due to SMTP configuration'); }

    } else {
        $validator = \Validator::make(
            $request->all(),
            [
                'name'   => 'required|max:120',
                'email'  => 'required|unique:users',
                'role'   => 'required',
                'avatar' => $avatarRule,
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $enableLogin = 0;
        if (!empty($request->password_switch) && $request->password_switch == 'on') {
            $enableLogin = 1;
            $validator = \Validator::make($request->all(), ['password' => 'required|min:6']);
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }
        }

        $objUser     = \Auth::user();
        $total_user  = $objUser->countUsers();
        $plan        = Plan::find($objUser->plan);

        if (!($total_user < $plan->max_users || $plan->max_users == -1)) {
            return redirect()->back()->with('error', __('Your user limit is over, Please change plan.'));
        }

        $user                      = new User();
        $user['name']              = $request->name;
        $user['email']             = $request->email;
        $role_r                    = Role::findById($request->role);
        $psw                       = $request->password;
        $user['password']          = !empty($userpassword) ? Hash::make($userpassword) : null;
        $user['type']              = $role_r->name;
        $user['lang']              = !empty($company_default_language) ? $company_default_language->value : 'en';
        $user['created_by']        = \Auth::user()->creatorId();
        $user['email_verified_at'] = now();
        $user['plan']              = Plan::first()->id;
        $user['is_enable_login']   = $enableLogin;

        $avatarFilename = null;
        if ($request->hasFile('avatar')) {
            $image_size = $request->file('avatar')->getSize();
            $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
            if ($result != 1) {
                return redirect()->back()->with('error', $result);
            }

            $filenameWithExt = $request->file('avatar')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('avatar')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            $dir = $settings['storage_setting'] == 'local' ? 'uploads/avatar/' : 'uploads/avatar';

            $upload = Utility::upload_file($request, 'avatar', $fileNameToStore, $dir, []);
            if (!($upload['flag'] ?? 0)) {
                return redirect()->back()->with('error', __($upload['msg'] ?? 'Avatar upload failed.'));
            }

            $avatarFilename = $fileNameToStore;
        }
        if ($avatarFilename) {
            $user['avatar'] = $avatarFilename;
        }

        $user->save();
        CustomField::saveData($user, $request->customField);
        $user->assignRole($role_r);
    }

    $uArr = ['email' => $user->email, 'password' => $psw ?? null];
    try { Utility::sendEmailTemplate('user_created', [$user->id => $user->email], $uArr); }
    catch (\Exception $e) { $smtp_error = __('E-Mail has been not sent due to SMTP configuration'); }

    return redirect()->route('users.index')->with(
        'success',
        __('User successfully added.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : '')
    );
}

    public function edit($id)
    {

        $user  = \Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->get()->pluck('name', 'id');
        if (\Auth::user()->can('edit user')) {
            $user              = User::findOrFail($id);
            $user->customField = CustomField::getData($user, 'user');
            $customFields      = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();

            return view('user.edit', compact('user', 'roles', 'customFields'));
        } else {
            return redirect()->back();
        }
    }

public function update(Request $request, $id)
{
    if (!\Auth::user()->can('edit user')) {
        return redirect()->back();
    }

    $avatarRule = ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'];

    if (\Auth::user()->type == 'super admin') {
        $user = User::findOrFail($id);

        $validator = \Validator::make(
            $request->all(),
            [
                'name'   => 'required|max:120',
                'email'  => 'required|email|unique:users,email,' . $id,
                'avatar' => $avatarRule,
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $input = $request->only(['name','email']);
        $user->fill($input);

        if ($request->hasFile('avatar')) {
            $filenameWithExt = $request->file('avatar')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('avatar')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            $dir = ($settings['storage_setting'] ?? 'local') === 'local' ? 'uploads/avatar/' : 'uploads/avatar';

            $upload = Utility::upload_file($request, 'avatar', $fileNameToStore, $dir, []);
            if (!($upload['flag'] ?? 0)) {
                return redirect()->back()->with('error', __($upload['msg'] ?? 'Avatar upload failed.'));
            }

            $user->avatar = $fileNameToStore;
        }

        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->route('users.index')->with('success', 'User successfully updated.');
    } else {
        $user = User::findOrFail($id);

        $this->validate(
            $request,
            [
                'name'   => 'required|max:120',
                'email'  => 'required|email|unique:users,email,' . $id,
                'role'   => 'required',
                'avatar' => $avatarRule,
            ]
        );

        $role          = Role::findById($request->role);
        $user->name    = $request->name;
        $user->email   = $request->email;
        $user->type    = $role->name;

        if ($request->hasFile('avatar')) {
            $image_size = $request->file('avatar')->getSize();
            $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
            if ($result != 1) {
                return redirect()->back()->with('error', $result);
            }

            $previousFileName = $user->avatar;
            if (!empty($previousFileName)) {
                Utility::changeStorageLimit(\Auth::user()->creatorId(), $previousFileName);
            }

            $filenameWithExt = $request->file('avatar')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('avatar')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            $dir = ($settings['storage_setting'] ?? 'local') === 'local' ? 'uploads/avatar/' : 'uploads/avatar';

            $upload = Utility::upload_file($request, 'avatar', $fileNameToStore, $dir, []);
            if (!($upload['flag'] ?? 0)) {
                return redirect()->back()->with('error', __($upload['msg'] ?? 'Avatar upload failed.'));
            }

            $user->avatar = $fileNameToStore;
        }

        $user->save();

        CustomField::saveData($user, $request->customField);

        $roles = [$request->role];
        $user->roles()->sync($roles);

        return redirect()->route('users.index')->with('success', 'User successfully updated.');
    }
}

    public function destroy($id)
    {
        if (\Auth::user()->can('delete user')) {
            $user = User::find($id);

            if ($user) {
                if (\Auth::user()->type == 'super admin') {

                    User::where('created_by', $user->id)->delete();

                    $user->delete();

                    return redirect()->back()->with('success' , __('Company Successfully deleted'));

                } else {
                    $user->delete();
                }

                return redirect()->route('users.index')->with('success', __('User successfully deleted .'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        } else {
            return redirect()->back();
        }
    }

public function profile()
{
    $userDetail              = \Auth::user();
    $userDetail->customField = CustomField::getData($userDetail, 'user');
    $customFields            = CustomField::where('created_by', \Auth::user()->creatorId())
                                ->where('module', '=', 'user')
                                ->get();

    $recoveryCodes = [];
    if ($userDetail->two_factor_recovery_codes) {
        try {
            $recoveryCodes = json_decode(
                \Illuminate\Support\Facades\Crypt::decryptString($userDetail->two_factor_recovery_codes),
                true
            );
        } catch (\Throwable $e) {
            $recoveryCodes = [];
        }
    }

    return view('user.profile', compact('userDetail', 'customFields', 'recoveryCodes'));
}

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = User::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:users,email,' . $userDetail['id'],
            ]
        );

        if ($request->hasFile('profile')) {
            if (\Auth::user()->type = 'super admin') {
                $file_path = $user['avatar'];
                $filenameWithExt = $request->file('profile')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('profile')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $settings = Utility::getStorageSetting();
                if ($settings['storage_setting'] == 'local') {
                    $dir        = 'uploads/avatar/';
                } else {
                    $dir        = 'uploads/avatar';
                }
                $image_path = $dir . $userDetail['avatar'];

                $url = '';
                $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
                if ($path['flag'] == 1) {
                    $url = $path['url'];
                } else {
                    return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
                }
            } else {
                $file_path = $user['avatar'];
                $image_size = $request->file('profile')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if ($result == 1) {

                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                    $filenameWithExt = $request->file('profile')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('profile')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $settings = Utility::getStorageSetting();

                    if ($settings['storage_setting'] == 'local') {
                        $dir        = 'uploads/avatar/';
                    } else {
                        $dir        = 'uploads/avatar';
                    }
                    $image_path = $dir . $userDetail['avatar'];

                    $url = '';
                    $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
                    if ($path['flag'] == 1) {
                        $url = $path['url'];
                    } else {
                        return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
                    }
                } else {
                    return redirect()->back()->with('error', $result);
                }
            }
        }

        if (!empty($request->profile)) {
           
            $user['avatar'] =  $url;
        }
        $user['name']  = $request['name'];
        $user['email'] = $request['email'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->back()->with(
            'success',
            __('Profile successfully updated.') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : '')
        );
    }

    public function updatePassword(Request $request)
    {
        if (Auth::Check()) {
            $request->validate(
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                ]
            );
            $objUser          = Auth::user();
            $request_data     = $request->All();
            $current_password = $objUser->password;
            if (Hash::check($request_data['current_password'], $current_password)) {
                $user_id            = Auth::User()->id;
                $obj_user           = User::find($user_id);
                $obj_user->password = Hash::make($request_data['new_password']);;
                $obj_user->save();

                return redirect()->route('profile', $objUser->id)->with('success', __('Password successfully updated.'));
            } else {
                return redirect()->route('profile', $objUser->id)->with('error', __('Please enter correct current password.'));
            }
        } else {
            return redirect()->route('profile', \Auth::user()->id)->with('error', __('Something is wrong.'));
        }
    }

    public function upgradePlan($user_id)
    {
        $user = User::find($user_id);

        $plans = Plan::get();

        return view('user.plan', compact('user', 'plans'));
    }

    public function activePlan($user_id, $plan_id)
    {

        $user       = User::find($user_id);
        $assignPlan = $user->assignPlan($plan_id);
        $plan       = Plan::find($plan_id);
        if ($assignPlan['is_success'] == true && !empty($plan)) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $orderID,
                    'name' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $plan->price,
                    'price_currency' => isset(\Auth::user()->planPrice()['currency']) ? \Auth::user()->planPrice()['currency'] : '',
                    'txn_id' => '',
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );

            return redirect()->back()->with('success', 'Plan successfully upgraded.');
        } else {
            return redirect()->back()->with('error', 'Plan fail to upgrade.');
        }
    }

    public function changeMode()
    {
        $usr = Auth::user();
        if ($usr->mode == 'light') {
            $usr->mode      = 'dark';
        } else {
            $usr->mode      = 'light';
        }
        $usr->save();
        return redirect()->back();
    }

    public function userPassword($id)
    {
        $eId        = \Crypt::decrypt($id);
        $user = User::find($eId);

        return view('user.reset', compact('user'));
    }

    public function userPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $user                 = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
            'is_enable_login' => 1,
        ])->save();

        return redirect()->route('users.index')->with(
            'success',
            'User Password successfully updated.'
        );
    }

    public function LoginWithCompany(Request $request,   $id)
    {
        $user = User::find($id);
        if ($user && auth()->check()) {
            Impersonate::take($request->user(), $user);
            return redirect('/');
        }
    }

    public function ExitCompany(Request $request)
    {
        \Auth::user()->leaveImpersonation($request->user());
        return redirect('/');
    }

    public function CompnayInfo($id)
    {
        if (!empty($id)) {
            $data = $this->Counter($id);
            if ($data['is_success']) {
                $users_data = $data['response']['users_data'];
                return view('user.companyinfo', compact('id', 'users_data'));
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function UserUnable(Request $request)
    {
        if (!empty($request->id) && !empty($request->company_id)) {
            if ($request->name == 'user') {
                User::where('id', $request->id)->update(['is_disable' => $request->is_disable]);
                $data = $this->Counter($request->company_id);
            }

            if ($data['is_success']) {
                $users_data = $data['response']['users_data'];
            }
            if ($request->is_disable == 1) {

                return response()->json(['success' => __('Successfully Enable.'), 'users_data' => $users_data]);
            } else {
                return response()->json(['success' => __('Successfull Disable.'), 'users_data' => $users_data]);
            }
        }
        return response()->json('error');
    }

    public function Counter($id)
    {
        $response = [];
        if (!empty($id)) {

            $users = User::where('created_by', $id)->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_disable = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_disable = 1 THEN 1 ELSE 0 END) as active_users')->first();

            $users_data[$users->name] = [
                'total_users' => !empty($users->total_users) ? $users->total_users : 0,
                'disable_users' => !empty($users->disable_users) ? $users->disable_users : 0,
                'active_users' => !empty($users->active_users) ? $users->active_users : 0,
            ];

            $response['users_data'] = $users_data;

            return [
                'is_success' => true,
                'response' => $response,
            ];
        }
        return [
            'is_success' => false,
            'error' => 'Plan is deleted.',
        ];
    }

    public function LoginManage($id)
    {
        $eId        = \Crypt::decrypt($id);
        $user = User::find($eId);
        if($user->is_enable_login == 1)
        {
            $user->is_enable_login = 0;
            $user->save();
            return redirect()->back()->with('success', __('User login disable successfully.'));
        }
        else
        {
            $user->is_enable_login = 1;
            $user->save();
            return redirect()->back()->with('success', __('User login enable successfully.'));
        }
    }
}
