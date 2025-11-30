<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $mailboxNumber;
    public $dueDate;
    public $message;
    public $reminderType;
    public $companyName;

    /**
     * Create a new message instance.
     */
    public function __construct($customerName, $mailboxNumber, $dueDate, $message, $reminderType = 'gentle', $companyName = 'Mail All Center')
    {
        $this->customerName = $customerName;
        $this->mailboxNumber = $mailboxNumber;
        $this->dueDate = $dueDate;
        $this->message = $message;
        $this->reminderType = $reminderType;
        $this->companyName = $companyName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->getSubjectByType();

        return new Envelope(
            subject: $subject,
            from: config('mail.from.address', 'noreply@mailallcenter.com'),
            replyTo: config('mail.from.address', 'noreply@mailallcenter.com')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.renewal-reminder',
            with: [
                'customerName' => $this->customerName,
                'mailboxNumber' => $this->mailboxNumber,
                'dueDate' => $this->dueDate,
                'message' => $this->message,
                'reminderType' => $this->reminderType,
                'companyName' => $this->companyName,
                'subject' => $this->getSubjectByType(),
                'urgencyLevel' => $this->getUrgencyLevel()
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
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
