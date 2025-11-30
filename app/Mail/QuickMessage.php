<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuickMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $mailboxNumber;
    public $message;
    public $messageType;
    public $companyName;

    /**
     * Create a new message instance.
     */
    public function __construct($customerName, $mailboxNumber, $message, $messageType = 'general', $companyName = 'Mail All Center')
    {
        $this->customerName = $customerName;
        $this->mailboxNumber = $mailboxNumber;
        $this->message = $message;
        $this->messageType = $messageType;
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
        $subject = $this->getSubjectByType();

        return new Content(
            view: 'emails.quick-message',
            with: [
                'customerName' => $this->customerName,
                'mailboxNumber' => $this->mailboxNumber,
                'message' => $this->message,
                'messageType' => $this->messageType,
                'companyName' => $this->companyName,
                'subject' => $subject
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
     * Get subject based on message type
     */
    private function getSubjectByType(): string
    {
        switch ($this->messageType) {
            case 'package_ready':
                return '📦 Package Ready for Pickup - ' . $this->companyName;
            case 'payment_reminder':
                return '💰 Payment Reminder - ' . $this->companyName;
            case 'account_update':
                return '📋 Account Update Required - ' . $this->companyName;
            case 'office_hours':
                return '🕒 Office Hours Notice - ' . $this->companyName;
            default:
                return 'Message from ' . $this->companyName;
        }
    }
}
