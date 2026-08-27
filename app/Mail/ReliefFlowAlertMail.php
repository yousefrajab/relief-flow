<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReliefFlowAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public string $bodyMessage,
        public string $actionUrl = '',
        public string $actionLabel = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->title);
    }

    public function content(): Content
    {
        $safeTitle = e($this->title);
        $safeBody = nl2br(e($this->bodyMessage));
        $safeActionUrl = $this->actionUrl !== '' ? e($this->actionUrl) : '';
        $safeActionLabel = $this->actionLabel !== '' ? e($this->actionLabel) : '';

        $actionHtml = $safeActionUrl !== ''
            ? "<p style=\"margin-top:24px;\"><a href=\"{$safeActionUrl}\" style=\"background:#147e63;color:#ffffff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:bold;\">{$safeActionLabel}</a></p>"
            : '';

        $html = <<<HTML
            <div style="font-family: sans-serif; max-width: 480px; margin: 0 auto; padding: 24px;">
                <p style="font-size: 12px; font-weight: bold; color: #147e63; text-transform: uppercase; letter-spacing: 1px;">ReliefFlow</p>
                <h2 style="color: #16211f;">{$safeTitle}</h2>
                <p style="color: #384946; line-height: 1.6;">{$safeBody}</p>
                {$actionHtml}
            </div>
        HTML;

        return new Content(htmlString: $html);
    }
}
