<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobDirectPlacementEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $attachmentPath;

    public function __construct($invitation, $attachmentPath)
    {
        $this->invitation = $invitation;
        $this->attachmentPath = $attachmentPath;
    }

    public function build()
    {
        $mail = $this->subject(config('app.name').' have added you to the worker pool for a job')
            ->view('clients.emails.job_direct_placement');

        if (file_exists($this->attachmentPath)) {
            $mail->attach($this->attachmentPath);
        }

        return $mail;
    }
}
