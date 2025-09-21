<?php

namespace App\Http\Controllers;

use App\Exports\PayslipExport;
use App\Models\Allowance;
use App\Models\Commission;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\OtherPayment;
use App\Models\Overtime;
use App\Models\PaySlip;
use App\Models\SaturationDeduction;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PaySlipController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage pay slip') || \Auth::user()->type != 'client' || \Auth::user()->type != 'company')
        {
            $employees = Employee::where(
                [
                    'created_by' => \Auth::user()->creatorId(),
                ]
            )->first();

            $month = [
                '01' => 'JAN',
                '02' => 'FEB',
                '03' => 'MAR',
                '04' => 'APR',
                '05' => 'MAY',
                '06' => 'JUN',
                '07' => 'JUL',
                '08' => 'AUG',
                '09' => 'SEP',
                '10' => 'OCT',
                '11' => 'NOV',
                '12' => 'DEC',
            ];

            $year = [

                '2023' => '2023',
                '2024' => '2024',
                '2025' => '2025',
                '2026' => '2026',
                '2027' => '2027',
                '2028' => '2028',
                '2029' => '2029',
                '2030' => '2030',
            ];
            return view('payslip.index', compact('employees', 'month', 'year'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'month' => 'required',
                               'year' => 'required',

                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $month = $request->month;
        $year  = $request->year;


        $formate_month_year = $year . '-' . $month;
        $validatePaysilp    = PaySlip::where('salary_month', '=', $formate_month_year)->where('created_by', \Auth::user()->creatorId())->pluck('employee_id');
        $payslip_employee   = Employee::where('created_by', \Auth::user()->creatorId())->where('company_doj', '<=', date($year . '-' . $month . '-t'))->count();
        if($payslip_employee > count($validatePaysilp))
        {
            $employees = Employee::where('created_by', \Auth::user()->creatorId())->where('company_doj', '<=', date($year . '-' . $month . '-t'))->whereNotIn('employee_id', $validatePaysilp)->whereNot('salary', '<=', 0)->get();
            foreach($employees as $employee)
            {
                $chek = PaySlip::where(['employee_id' => $employee->id, 'salary_month' => $formate_month_year])->first();
                if (!$chek && $chek == null) {
                    $payslipEmployee                       = new PaySlip();
                    $payslipEmployee->employee_id          = $employee->id;
                    $payslipEmployee->net_payble           = $employee->get_net_salary();
                    $payslipEmployee->salary_month         = $formate_month_year;
                    $payslipEmployee->status               = 0;
                    $payslipEmployee->basic_salary         = !empty($employee->salary) ? $employee->salary : 0;
                    $payslipEmployee->allowance            = Employee::allowance($employee->id);
                    $payslipEmployee->commission           = Employee::commission($employee->id);
                    $payslipEmployee->loan                 = Employee::loan($employee->id);
                    $payslipEmployee->saturation_deduction = Employee::saturation_deduction($employee->id);
                    $payslipEmployee->other_payment        = Employee::other_payment($employee->id);
                    $payslipEmployee->overtime             = Employee::overtime($employee->id);
                    $payslipEmployee->created_by           = \Auth::user()->creatorId();
                    $payslipEmployee->save();

                    //For Notification
                    $setting  = Utility::settings(\Auth::user()->creatorId());
                    $payslipNotificationArr = [
                        'year' =>  $formate_month_year,
                    ];
                    //Slack Notification
                    if(isset($setting['payslip_notification']) && $setting['payslip_notification'] ==1)
                    {
                        Utility::send_slack_msg('new_monthly_payslip', $payslipNotificationArr);
                    }

                    //Telegram Notification
                    if(isset($setting['telegram_payslip_notification']) && $setting['telegram_payslip_notification'] ==1)
                    {
                        Utility::send_telegram_msg('new_monthly_payslip', $payslipNotificationArr);
                    }

                    //webhook
                    $module ='New Monthly Payslip';
                    $webhook=  Utility::webhookSetting($module);
                    if($webhook)
                    {
                        $parameter = json_encode($payslipEmployee);
                        $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);

                        if($status == true)
                        {
                            return redirect()->back()->with('success', __('Payslip successfully created.'));
                        }
                        else
                        {
                            return redirect()->back()->with('error', __('Webhook call failed.'));
                        }
                    }
                }

            }

            return redirect()->route('payslip.index')->with('success', __('Payslip successfully created.'));
        }
        else
        {
            return redirect()->route('payslip.index')->with('error', __('Payslip Already created.'));
        }

    }

    public function destroy($id)
    {
        $payslip = PaySlip::find($id);
        $payslip->delete();

        return true;
    }

    public function showemployee($paySlip)
    {
        $payslip = PaySlip::find($paySlip);

        return view('payslip.show', compact('payslip'));
    }


    public function search_json(Request $request)
    {

        $formate_month_year = $request->datePicker;
        $validatePaysilp    = PaySlip::where('salary_month', '=', $formate_month_year)->where('created_by', \Auth::user()->creatorId())->get()->toarray();
        $data=[];
        if (empty($validatePaysilp))
        {
            return response()->json(['data' => $data]);
        } else {
            $paylip_employee = PaySlip::select(
                [
                    'employees.id',
                    'employees.employee_id',
                    'employees.name',
                    'payslip_types.name as payroll_type',
                    'pay_slips.basic_salary',
                    'pay_slips.net_payble',
                    'pay_slips.id as pay_slip_id',
                    'pay_slips.status',
                    'employees.user_id',
                ]
            )->leftjoin(
                'employees',
                function ($join) use ($formate_month_year) {
                    $join->on('employees.id', '=', 'pay_slips.employee_id');
                    $join->on('pay_slips.salary_month', '=', \DB::raw("'" . $formate_month_year . "'"));
                    $join->leftjoin('payslip_types', 'payslip_types.id', '=', 'employees.salary_type');
                }
            )->where('employees.created_by', \Auth::user()->creatorId())->get();


            foreach ($paylip_employee as $employee) {

                if (Auth::user()->type == 'Employee') {
                    if (Auth::user()->id == $employee->user_id) {
                        $tmp   = [];
                        $tmp[] = $employee->id;
                        $tmp[] = $employee->name;
                        $tmp[] = $employee->payroll_type;
                        $tmp[] = $employee->pay_slip_id;
                        $tmp[] = !empty($employee->basic_salary) ? \Auth::user()->priceFormat($employee->basic_salary) : '-';
                        $tmp[] = !empty($employee->net_payble) ? \Auth::user()->priceFormat($employee->net_payble) : '-';
                        if ($employee->status == 1) {
                            $tmp[] = 'paid';
                        } else {
                            $tmp[] = 'unpaid';
                        }
                        $tmp[]  = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                        $tmp['url']  = route('employee.show', Crypt::encrypt($employee->id));
                        $data[] = $tmp;
                    }
                } else {

                    $tmp   = [];
                    $tmp[] = $employee->id;
                    $tmp[] = \Auth::user()->employeeIdFormat($employee->employee_id);
                    $tmp[] = $employee->name;
                    $tmp[] = $employee->payroll_type;
                    $tmp[] = !empty($employee->basic_salary) ? \Auth::user()->priceFormat($employee->basic_salary) : '-';
                    $tmp[] = !empty($employee->net_payble) ? \Auth::user()->priceFormat($employee->net_payble) : '-';
                    if ($employee->status == 1) {
                        $tmp[] = 'Paid';
                    } else {
                        $tmp[] = 'UnPaid';
                    }
                    $tmp[]  = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                    $tmp['url']  = route('employee.show', Crypt::encrypt($employee->id));
                    $data[] = $tmp;
                }
            }

            return $data;
        }
    }

    public function paysalary($id, $date)
    {
        $employeePayslip = PaySlip::where('employee_id', '=', $id)->where('created_by', \Auth::user()->creatorId())->where('salary_month', '=', $date)->first();

        $account = Employee::find($id);
        Utility::bankAccountBalance($account->account, $employeePayslip->net_payble, 'debit');

        if(!empty($employeePayslip))
        {
            $employeePayslip->status = 1;
            $employeePayslip->save();

            return redirect()->route('payslip.index')->with('success', __('Payslip Payment successfully.'));
        }
        else
        {
            return redirect()->route('payslip.index')->with('error', __('Payslip Payment failed.'));
        }

    }

public function bulk_pay_create($date)
{
    // Require selected payslip ids
    $ids = array_filter((array) request()->input('ids', []));
    if (empty($ids)) {
        return response()->json(['error' => __('Please select at least one payslip.')], 422);
    }

    // Validate tenant + month + selection
    $Employees = PaySlip::where('salary_month', $date)
        ->where('created_by', \Auth::user()->creatorId())
        ->whereIn('id', $ids)
        ->get();

    $unpaidEmployees = PaySlip::where('salary_month', $date)
        ->where('created_by', \Auth::user()->creatorId())
        ->where('status', 0)
        ->whereIn('id', $ids)
        ->get();

    if ($Employees->isEmpty()) {
        return response()->json(['error' => __('Selected payslips not found.')], 422);
    }

    if ($unpaidEmployees->isEmpty()) {
        return response()->json(['error' => __('Selected payslips are already paid.')], 422);
    }

    return view('payslip.bulkcreate', compact('Employees', 'unpaidEmployees', 'date'));
}


public function bulkpayment(Request $request, $date)
{
    $ids = array_filter((array) $request->input('ids', []));
    if (empty($ids)) {
        return redirect()->route('payslip.index')
            ->with('error', __('Please select at least one payslip.'));
    }

    $unpaid = PaySlip::where('salary_month', $date)
        ->where('created_by', \Auth::user()->creatorId())
        ->where('status', 0)
        ->whereIn('id', $ids)
        ->get();

    if ($unpaid->isEmpty()) {
        return redirect()->route('payslip.index')
            ->with('error', __('Selected payslips are already paid or invalid.'));
    }

    foreach ($unpaid as $p) {
        $p->status = 1;      // mark paid
        $p->save();
    }

    return redirect()->route('payslip.index')
        ->with('success', __('Selected payslips have been paid successfully.'));
}


    public function employeepayslip()
    {
        $employees = Employee::where(
            [
                'user_id' => \Auth::user()->id,
            ]
        )->first();

        $payslip = PaySlip::where('employee_id', '=', $employees->id)->get();

        return view('payslip.employeepayslip', compact('payslip'));

    }

    public function pdf($id, $month)
    {
        $payslip  = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);

       // dd($employee);

        $payslipDetail = Utility::employeePayslipDetail($id,$month);


        return view('payslip.pdf', compact('payslip', 'employee', 'payslipDetail'));
    }

    public function send($id, $month)
    {
        $setings = Utility::settings();
//        dd($setings);
        // if($setings['payslip_sent'] == 1)
        // {
            $payslip  = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
            $employee = Employee::find($payslip->employee_id);

            $payslip->name  = $employee->name;
            $payslip->email = $employee->email;

            $payslipId    = Crypt::encrypt($payslip->id);
            $payslip->url = route('payslip.payslipPdf', $payslipId);
//            dd($payslip->url);

            $payslipArr = [

                'employee_name'=> $employee->name,
                'employee_email' => $employee->email,
                'payslip_name' =>   $payslip->name,
                'payslip_salary_month' => $payslip->salary_month,
                'payslip_url' =>$payslip->url,

            ];
            $resp = Utility::sendEmailTemplate('payslip_sent', [$employee->id => $employee->email], $payslipArr);



            return redirect()->back()->with('success', __('Payslip successfully sent.') .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        // }

        // return redirect()->back()->with('success', __('Payslip successfully sent.'));

    }

public function payslipPdf($id)
{
    try {
        $payslipId = Crypt::decrypt($id);
    } catch (\Throwable $e) {
        abort(404, 'Invalid payslip link.');
    }

    $payslip = PaySlip::query()
        ->where('id', $payslipId)
        ->where('created_by', \Auth::user()->creatorId())
        ->firstOrFail();

    $employee = Employee::findOrFail($payslip->employee_id);

    $payslipDetail = Utility::employeePayslipDetail($payslip->employee_id, $payslip->salary_month);

    return view('payslip.payslipPdf', compact('payslip', 'employee', 'payslipDetail'));
}

    public function editEmployee($paySlip)
    {
        $payslip = PaySlip::find($paySlip);

        return view('payslip.salaryEdit', compact('payslip'));
    }

    public function updateEmployee(Request $request, $id)
    {


        if(isset($request->allowance) && !empty($request->allowance))
        {
            $allowances   = $request->allowance;
            $allowanceIds = $request->allowance_id;
            foreach($allowances as $k => $allownace)
            {
                $allowanceData         = Allowance::find($allowanceIds[$k]);
                $allowanceData->amount = $allownace;
                $allowanceData->save();
            }
        }


        if(isset($request->commission) && !empty($request->commission))
        {
            $commissions   = $request->commission;
            $commissionIds = $request->commission_id;
            foreach($commissions as $k => $commission)
            {
                $commissionData         = Commission::find($commissionIds[$k]);
                $commissionData->amount = $commission;
                $commissionData->save();
            }
        }

        if(isset($request->loan) && !empty($request->loan))
        {
            $loans   = $request->loan;
            $loanIds = $request->loan_id;
            foreach($loans as $k => $loan)
            {
                $loanData         = Loan::find($loanIds[$k]);
                $loanData->amount = $loan;
                $loanData->save();
            }
        }


        if(isset($request->saturation_deductions) && !empty($request->saturation_deductions))
        {
            $saturation_deductionss   = $request->saturation_deductions;
            $saturation_deductionsIds = $request->saturation_deductions_id;
            foreach($saturation_deductionss as $k => $saturation_deductions)
            {

                $saturation_deductionsData         = SaturationDeduction::find($saturation_deductionsIds[$k]);
                $saturation_deductionsData->amount = $saturation_deductions;
                $saturation_deductionsData->save();
            }
        }


        if(isset($request->other_payment) && !empty($request->other_payment))
        {
            $other_payments   = $request->other_payment;
            $other_paymentIds = $request->other_payment_id;
            foreach($other_payments as $k => $other_payment)
            {
                $other_paymentData         = OtherPayment::find($other_paymentIds[$k]);
                $other_paymentData->amount = $other_payment;
                $other_paymentData->save();
            }
        }


        if(isset($request->rate) && !empty($request->rate))
        {
            $rates   = $request->rate;
            $rateIds = $request->rate_id;
            $hourses = $request->hours;

            foreach($rates as $k => $rate)
            {
                $overtime        = Overtime::find($rateIds[$k]);
                $overtime->rate  = $rate;
                $overtime->hours = $hourses[$k];
                $overtime->save();
            }
        }


        $payslipEmployee                       = PaySlip::find($request->payslip_id);
        $payslipEmployee->allowance            = Employee::allowance($payslipEmployee->employee_id);
        $payslipEmployee->commission           = Employee::commission($payslipEmployee->employee_id);
        $payslipEmployee->loan                 = Employee::loan($payslipEmployee->employee_id);
        $payslipEmployee->saturation_deduction = Employee::saturation_deduction($payslipEmployee->employee_id);
        $payslipEmployee->other_payment        = Employee::other_payment($payslipEmployee->employee_id);
        $payslipEmployee->overtime             = Employee::overtime($payslipEmployee->employee_id);
        $payslipEmployee->net_payble           = Employee::find($payslipEmployee->employee_id)->get_net_salary();
        $payslipEmployee->save();

        return redirect()->route('payslip.index')->with('success', __('Employee payroll successfully updated.'));
    }

public function export(Request $request)
{
    $ids = array_filter((array) $request->input('ids', []));
    if (empty($ids)) {
        return redirect()->route('payslip.index')
            ->with('error', __('Please select at least one payslip to export.'));
    }

    $name = 'payslip_' . date('Y-m-d H:i:s');
    $data = Excel::download(new PayslipExport($request), $name . '.xlsx'); ob_end_clean();
    return $data;
}
public function ytdTotals(Request $request)
{
    $date = $request->input('datePicker'); // "YYYY-MM"
    if (empty($date) || !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $date)) {
        return response()->json(['error' => __('Invalid date.')], 422);
    }

    [$year, $month] = explode('-', $date);
    $start = $year . '-01';
    $end   = $year . '-' . $month;

    $base = PaySlip::where('created_by', \Auth::user()->creatorId())
        ->where('salary_month', '>=', $start)
        ->where('salary_month', '<=', $end);

    // clone builder for separate aggregates
    $total_basic = (clone $base)->sum('basic_salary');
    $total_net   = (clone $base)->sum('net_payble');
    $paid_count  = (clone $base)->where('status', 1)->count();
    $unpaid_count= (clone $base)->where('status', 0)->count();

    // label like "Jan–Aug 2025"
    $endMonthName = strtoupper(date('M', strtotime("$year-$month-01")));
    $label = 'Jan–' . $endMonthName . ' ' . $year;

    return response()->json([
        'label'                 => $label,
        'total_basic'           => $total_basic,
        'total_net'             => $total_net,
        'total_basic_formatted' => \Auth::user()->priceFormat($total_basic),
        'total_net_formatted'   => \Auth::user()->priceFormat($total_net),
        'paid_count'            => $paid_count,
        'unpaid_count'          => $unpaid_count,
    ]);
}

}
