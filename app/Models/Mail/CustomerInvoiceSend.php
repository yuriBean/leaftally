<?php

namespace App\Models\Mail;

use App\Models\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerInvoiceSend extends Mailable
{
    use Queueable, SerializesModels;
    public $invoice;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        if(\Auth::user()->type == 'super admin')
        {
            return $this->view('email.customer_invoice_send')->with('invoice', $this->invoice)->subject('Ragarding to send invoice');
        }
        else
        {
            return $this->from(Utility::getValByName('company_email'), Utility::getValByName('company_email_from_name'))->view('email.customer_invoice_send')->with('invoice', $this->invoice)->subject('Ragarding to send invoice');
        }

    }
}
