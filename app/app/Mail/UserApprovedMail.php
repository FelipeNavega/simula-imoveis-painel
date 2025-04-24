<?php

namespace App\Mail;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public ?string $loginUrl;

    public function __construct(public User $user)
    {
        $this->loginUrl = Filament::getPanel('partner')->getLoginUrl();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Usuário aprovado',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.user-approved-mail',
        );
    }
}
