<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $user,
        public string $status,
        public ?string $reason = null
    ) {}

    public function build()
    {
        $subject = $this->status === 'approved'
            ? 'Pendaftaran Akun Kawwan Disetujui'
            : 'Informasi Pendaftaran Akun Kawwan';

        return $this->subject($subject)
            ->view('emails.account-status');
    }
}
