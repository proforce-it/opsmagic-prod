<?php

namespace App\Jobs;

use App\Http\Controllers\Clients\ClientController;
use App\Mail\JobDirectPlacementEmail;
use App\Mail\JobInvitationEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class JobWorkerInvitationMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $invitation, $worker_email, $attachmentPath, $invitation_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invitation, $worker_email, $attachmentPath, $invitation_type) {
        $this->invitation = $invitation;
        $this->worker_email = $worker_email;
        $this->attachmentPath = $attachmentPath;
        $this->invitation_type = $invitation_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        if ($this->invitation_type == '1') {
            Mail::to($this->worker_email)->send(new JobInvitationEmail($this->invitation, $this->attachmentPath));
        } else {
            Mail::to($this->worker_email)->send(new JobDirectPlacementEmail($this->invitation, $this->attachmentPath));
        }
    }
}
