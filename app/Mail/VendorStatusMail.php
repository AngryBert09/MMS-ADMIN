<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vendor;
    public $status;
    public $rejectionReason;

    /**
     * Create a new message instance.
     *
     * @param mixed $vendor Vendor data (can be an array or a model)
     * @param string $status The vendor's new status ("Approved" or "Rejected")
     * @param string|null $rejectionReason Optional reason for rejection
     */
    public function __construct($vendor, string $status, $rejectionReason = null)
    {
        $this->vendor = is_array($vendor) ? (object)$vendor : $vendor;
        $this->status = $status;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'Approved'
            ? 'Your Vendor Account Has Been Approved'
            : 'Your Vendor Account Has Been Rejected';

        return new Envelope(
            subject: $subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor.vendor-status'
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $logoPath = public_path('img/greatwall-logo.png');
        return $this->view('emails.vendor-status')
            ->subject($this->status === 'Approved' ? 'Your Account Has Been Approved' : 'Your Account Has Been Rejected')
            ->with([
                'vendor'          => $this->vendor,
                'status'          => $this->status,
                'rejectionReason' => $this->rejectionReason,
            ])
            ->withSwiftMessage(function ($message) use ($logoPath) {
                $message->embed($logoPath);
            });
    }
}
