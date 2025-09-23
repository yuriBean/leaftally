<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommonEmailTemplate extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $settings;

    public function __construct($template, $settings)
    {
        $this->template = $template;
        $this->settings = $settings;
    }

    public function build()
    {
        return $this->from($this->settings['mail_from_address'], "LeafTally")->markdown('email.common_email_template')->subject($this->template->subject)->with('content', $this->template->content);

    }
}
