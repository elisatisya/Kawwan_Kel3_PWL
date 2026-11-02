<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
     use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Tes Email Kawwan')
            ->html('<h2>Email Kawwan berhasil dikirim!</h2><p>Konfigurasi SMTP Gmail sudah berhasil.</p>');
    }
}
