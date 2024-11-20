<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendResetCodeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $subject;
    public $code;
    /**
     * Create a new job instance.
     */
    public function __construct($email,$subject,$code)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new ResetPasswordMail($this->code, $this->subject));
    }
}
