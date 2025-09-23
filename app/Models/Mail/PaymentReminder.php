<?php

namespace App\Models\Mail;

use App\Models\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        if(\Auth::user()->type == 'super admin')
        {
            return $this->view('email.payment_reminder', compact($this->payment))->subject('Ragarding to overdue payment reminder');
        }
        else
        {
            return $this->from(Utility::getValByName('company_email'), Utility::getValByName('company_email_from_name'))->view('email.payment_reminder', compact($this->payment))->subject('Ragarding to overdue payment reminder');

        }

    }
}
