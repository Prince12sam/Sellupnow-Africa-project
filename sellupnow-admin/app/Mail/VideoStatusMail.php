<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VideoStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $sellerName,
        public string $listingTitle,
        public bool   $approved,
        public ?string $rejectReason = null
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->approved
            ? config('app.name') . ' — Your listing video is now live!'
            : config('app.name') . ' — Your listing video was not approved';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.video-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
