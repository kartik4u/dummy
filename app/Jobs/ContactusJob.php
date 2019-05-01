<?php

namespace App\Jobs;

use App\Mail\ContactusEmail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mail;

class ContactusJob implements ShouldQueue
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
    public function __construct(array $request_data)
    {
        $this->user = $request_data;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       $data = $this->user['email_array'];
       //Mail::to($this->user['email_array']["to"])->send(new ContactusEmail($data));

       Mail::send($data['view'], $data, function ($message) use ($data) {
        $message->to($data['to'])->from($data['from'], $data['from_name'])->subject($data['subject']);
    });
    
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
