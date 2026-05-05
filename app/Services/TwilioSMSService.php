<?php
// app/Services/TwilioSMSService.php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioSMSService
{
    private $client;
    private $fromNumber;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->fromNumber = config('services.twilio.from');
    }

    /**
     * Send queue number SMS to walk-in client
     */
    public function sendQueueNotification($booking)
    {
        $phoneNumber = $this->formatPhoneNumber($booking->contact_number);

        $message = $this->buildQueueMessage($booking);

        return $this->sendSMS($phoneNumber, $message);
    }

    /**
     * Send appointment confirmation SMS
     */
    public function sendAppointmentConfirmation($booking)
    {
        $phoneNumber = $this->formatPhoneNumber($booking->contact_number);

        $message = $this->buildAppointmentMessage($booking);

        return $this->sendSMS($phoneNumber, $message);
    }

    /**
     * Send appointment reminder SMS
     */
    public function sendAppointmentReminder($booking)
    {
        $phoneNumber = $this->formatPhoneNumber($booking->contact_number);

        $message = "Hi {$booking->name}, reminder for your appointment on ";
        $message .= $booking->booking_date->format('M d, Y') . " at ";
        $message .= date('h:i A', strtotime($booking->time_slot)) . ". ";
        $message .= "Service: " . \App\Models\Booking::getServices()[$booking->service] . ". ";
        $message .= "See you soon!";

        return $this->sendSMS($phoneNumber, $message);
    }

    /**
     * Send generic SMS
     */
    private function sendSMS($to, $message)
    {
        try {
            $this->client->messages->create(
                $to,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            Log::info("SMS sent to {$to}");
            return true;

        } catch (\Exception $e) {
            Log::error("SMS failed to {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Build queue notification message
     */
    private function buildQueueMessage($booking)
    {
        $serviceName = \App\Models\Booking::getServices()[$booking->service];

        $message = "Hi {$booking->name}, ";
        $message .= "your queue number is #{$booking->queue_number} ";
        $message .= "for {$serviceName}. ";
        $message .= "Visit date: " . now()->format('M d, Y') . ". ";
        $message .= "Please wait for your number to be called.";

        return $message;
    }

    private function buildAppointmentMessage($booking)
    {
        $serviceName = \App\Models\Booking::getServices()[$booking->service];
        $timeSlot = date('h:i A', strtotime($booking->time_slot));
        $date = $booking->booking_date->format('M d, Y');

        $message = "Hi {$booking->name}, ";
        $message .= "your appointment is confirmed! ";
        $message .= "Date: {$date} at {$timeSlot}. ";
        $message .= "Service: {$serviceName}. ";
        $message .= "Location: [Your Address]. ";
        $message .= "Please arrive 5 minutes early. ";

        return $message;
    }
    /**
     * Format phone number to E.164 format
     */
    private function formatPhoneNumber($number)
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);

        // Add +1 for US numbers (adjust as needed)
        if (strlen($number) == 10) {
            $number = '+1' . $number;
        } elseif (strlen($number) > 10 && substr($number, 0, 1) != '+') {
            $number = '+' . $number;
        }

        return $number;
    }
}
