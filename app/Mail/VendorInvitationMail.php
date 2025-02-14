<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vendorName;
    public $inviteLink;

    /**
     * Create a new message instance.
     */
    public function __construct($vendorName, $inviteLink)
    {
        $this->vendorName = $vendorName;
        $this->inviteLink = $inviteLink;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Invitation to Join as a Vendor')
            ->markdown('emails.vendor.invitation');
    }
}
