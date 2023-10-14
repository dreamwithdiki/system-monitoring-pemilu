<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobOrderEmailPartner extends Mailable
{
    use Queueable, SerializesModels;

    public $subject; // Subject email
    public $partnerName; // Nama partner
    public $job_order_number; // Visitor order number
    public $merchant_name; // Nama merchant
    public $location; // Location
    public $due_date; // Due date

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $partnerName, $job_order_number, $merchant_name, $location, $due_date)
    {
        $this->subject            = $subject;
        $this->partnerName        = $partnerName;
        $this->job_order_number = $job_order_number;
        $this->merchant_name        = $merchant_name;
        $this->location           = $location;
        $this->due_date           = $due_date;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'content.job-order.email.job_order_partner', 
            with: [
                'partnerName'        => $this->partnerName,
                'job_order_number' => $this->job_order_number,
                'merchant_name'        => $this->merchant_name,
                'location'           => $this->location,
                'due_date'           => $this->due_date,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    // public function attachments()
    // {
    //     return [];
    // }
}
