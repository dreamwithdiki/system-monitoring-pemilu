<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitOrderRevisitMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject; // Subject email
    public $visitOrder; // Nama client

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $visitOrder)
    {
        $this->subject = $subject;
        $this->visitOrder = $visitOrder;
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
            view: 'content.site-visit.email.visit_order_re_visit', 
            with: [
                'visitOrder' => $this->visitOrder,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
