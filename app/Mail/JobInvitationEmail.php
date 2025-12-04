<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobInvitationEmail extends Mailable
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
        $mail = $this->subject(config('app.name').' would like to add you to the worker pool for a job')
            ->view('clients.emails.job_invitation');

        if (file_exists($this->attachmentPath)) {
            $mail->attach($this->attachmentPath);
        }

        return $mail;
    }
}