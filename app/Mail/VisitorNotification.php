<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VisitorNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $visitor;

    public function __construct($visitor)
    {
        $this->visitor = $visitor;
    }

    public function build()
    {
        return $this->subject('New Visitor Registration')
                    ->view('emails.visitor-notification')
                    ->with([
                        'visitor' => $this->visitor
                    ]);
    }
} 