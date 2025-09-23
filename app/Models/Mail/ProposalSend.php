<?php

namespace App\Models\Mail;

use App\Models\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProposalSend extends Mailable
{
    use Queueable, SerializesModels;

    public $proposal;

    public function __construct($proposal)
    {
        $this->proposal = $proposal;
    }

    public function build()
    {
        if(\Auth::user()->type == 'super admin')
        {
            return $this->view('email.proposal_send')->with('proposal', $this->proposal)->subject('Ragarding to product/service proposal generator.');
        }
        else
        {
            return $this->from(Utility::getValByName('company_email'), Utility::getValByName('company_email_from_name'))->view('email.proposal_send')->with('proposal', $this->proposal)->subject('Ragarding to product/service proposal generator.');
        }

    }
}
