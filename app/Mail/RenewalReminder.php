<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RenewalReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $mailboxNumber;
    public $dueDate;
    public $messageContent;  // Renamed to avoid conflict with Laravel's $message
    public $reminderType;
    public $companyName;

    /**
     * Create a new message instance.
     */
    public function __construct($customerName, $mailboxNumber, $dueDate, $message, $reminderType = 'gentle', $companyName = 'Mail Center')
    {
        $this->customerName = $customerName;
        $this->mailboxNumber = $mailboxNumber;
        $this->dueDate = $dueDate;
        $this->messageContent = $message;  // Store in renamed variable
        $this->reminderType = $reminderType;
        $this->companyName = $companyName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->getSubjectByType();

        return $this->view('emails.renewal-reminder')
                    ->subject($subject);
    }

    /**
     * Get subject based on reminder type
     */
    private function getSubjectByType(): string
    {
        switch ($this->reminderType) {
            case 'gentle':
                return '🙂 Friendly Renewal Reminder - ' . $this->companyName;
            case 'standard':
                return '📋 Mailbox Renewal Notice - ' . $this->companyName;
            case 'urgent':
                return '⚠️ URGENT: Mailbox Renewal Due - ' . $this->companyName;
            case 'final':
                return '🚨 FINAL NOTICE: Mailbox Renewal - ' . $this->companyName;
            default:
                return 'Mailbox Renewal Reminder - ' . $this->companyName;
        }
    }

    /**
     * Get urgency level for styling
     */
    private function getUrgencyLevel(): string
    {
        switch ($this->reminderType) {
            case 'urgent':
            case 'final':
                return 'high';
            case 'standard':
                return 'medium';
            default:
                return 'low';
        }
    }
}
