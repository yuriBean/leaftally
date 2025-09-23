<?php

namespace App\Models\Mail;

use App\Models\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VenderBillSend extends Mailable
{
    use Queueable, SerializesModels;
    public $bill;

    public function __construct($bill)
    {
        $this->bill = $bill;
    }

    public function build()
    {
        if(\Auth::user()->type == 'super admin')
        {
            return $this->view('email.vender_bill_send')->with('bill', $this->bill)->subject('Ragarding to send bill');
        }
        else
        {
            return $this->from(Utility::getValByName('company_email'), Utility::getValByName('company_email_from_name'))->view('email.vender_bill_send')->with('bill', $this->bill)->subject('Ragarding to send bill');

        }

    }
}
