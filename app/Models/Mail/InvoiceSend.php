<?php

namespace App\Models\Mail;

use App\Models\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceSend extends Mailable
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
            return $this->view('email.invoice_send')->with('invoice', $this->invoice)->subject('Ragarding to product/service invoice generator.');
        }
        else
        {
            return $this->from(Utility::getValByName('company_email'), Utility::getValByName('company_email_from_name'))->view('email.invoice_send')->with('invoice', $this->invoice)->subject('Ragarding to product/service invoice generator.');
        }
    }
}
