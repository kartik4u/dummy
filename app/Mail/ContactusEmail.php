<?php

namespace App\Mail;

use App\User;
use Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactusEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email_data)
    {
        //
        $this->data = $email_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->view('emails.users.verification')
        ->from(config('variable.ADMIN_EMAIL'), config('variable.SITE_NAME'))
        ->subject(config('variable.SITE_NAME') . ': ' . 'Contact us');

        // Mail::send($data['view'], $data, function ($message) use ($data) {
        //     $message->to($data['to'])->from($data['from'], $data['from_name'])->subject($data['subject']);
        // });
        // return $this->data;
    }
}
