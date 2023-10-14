<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitOrderEmailPartner extends Mailable
{
    use Queueable, SerializesModels;

    public $subject; // Subject email
    public $partnerName; // Nama partner
    public $visit_order_number; // Visitor order number
    public $debtor_name; // Nama debtor
    public $location; // Location
    public $due_date; // Due date

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $partnerName, $visit_order_number, $debtor_name, $location, $due_date)
    {
        $this->subject            = $subject;
        $this->partnerName        = $partnerName;
        $this->visit_order_number = $visit_order_number;
        $this->debtor_name        = $debtor_name;
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
            view: 'content.site-visit.email.visit_order_partner', 
            with: [
                'partnerName'        => $this->partnerName,
                'visit_order_number' => $this->visit_order_number,
                'debtor_name'        => $this->debtor_name,
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
