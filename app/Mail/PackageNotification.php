<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;


class PackageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $name, $trackingNumbers, $messageText, $imagePaths, $imageUrls;
    /**
     * Create a new message instance.
     */
    public function __construct($imagePaths, $name, $trackingNumbers, $messageText = null, $imageUrls = [])
    {
        //
        $this->imagePaths = $imagePaths;
        $this->name = $name;
        $this->trackingNumbers = $trackingNumbers;
        $this->messageText = $messageText;
        $this->imageUrls = $imageUrls;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Package Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.packages.notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $email = $this->subject("Package Notification for {$this->name}")
            ->view('emails.package')
            ->with([
                'customerName' => $this->name,
                'trackingNumbers' => $this->trackingNumbers,
                'imageUrls' => $this->imageUrls,
            ]);

            if (is_array($this->imagePaths) && count($this->imagePaths) > 0) {
                foreach ($this->imagePaths as $path) {
                    if (file_exists($path)) {
                        $email->attach($path);
                    }
                }
            }

        return $email;
    }
}
