<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmEmailAddress extends Mailable
{
    use Queueable, SerializesModels;

    public $confirmData;

    public function __construct($confirmData)
    {
        $this->confirmData = $confirmData;
    }

    public function build()
    {
        return $this->subject('Action required: confirm your '.config('app.name').' account')
            ->view('workers.emails.confirm_mail');
    }
}
