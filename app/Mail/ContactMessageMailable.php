<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMailable extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $body;

    public function __construct(string $name, string $email, string $body)
    {
        $this->name  = $name;
        $this->email = $email;
        $this->body  = $body;
    }

    public function build(): self
    {
        return $this->subject('New contact message')
                    ->replyTo($this->email, $this->name);
    }
}
