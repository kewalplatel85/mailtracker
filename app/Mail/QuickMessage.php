<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuickMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $mailboxNumber;
    public $messageContent;
    public $companyName;

    /**
     * Create a new message instance.
     */
    public function __construct($customerName, $mailboxNumber, $message, $messageType = 'general', $companyName = 'Mail Center')
    {
        $this->customerName = $customerName;
        $this->mailboxNumber = $mailboxNumber;
        $this->messageContent = $message; // Renamed to avoid conflict
        $this->companyName = $companyName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.quick-message-simple')
                    ->subject('Message from ' . $this->companyName);
    }
}
