<?php

namespace App\Jobs;

use App\Mail\JobShiftDirectPlacementEmail;
use App\Mail\JobShiftInvitationEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class JobShiftInvitationMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $invitation, $worker_email, $invitation_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invitation, $worker_email, $invitation_type) {
        $this->invitation = $invitation;
        $this->worker_email = $worker_email;
        $this->invitation_type = $invitation_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        if ($this->invitation_type == 'Invitation') {
            Mail::to($this->worker_email)->send(new JobShiftInvitationEmail($this->invitation));
        } else {
            Mail::to($this->worker_email)->send(new JobShiftDirectPlacementEmail($this->invitation));
        }
    }
}
