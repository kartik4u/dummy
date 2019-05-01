<?php

namespace App\Jobs;

use App\Mail\SignupEmail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mail;

class SignupJob implements ShouldQueue
{

    use Dispatchable,
    InteractsWithQueue,
    Queueable,
    SerializesModels;

    public $user;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        //
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        if($this->user->secondary_email=='')
        {
          return  Mail::to($this->user->email)->send(new SignupEmail($this->user));
        }
        else{
            Mail::to($this->user->secondary_email)->send(new SignupEmail($this->user));

        }
        if (count(Mail::failures()) > 0) {
            return false;
        }
        return true;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
        Log::info($exception);
    }

}
