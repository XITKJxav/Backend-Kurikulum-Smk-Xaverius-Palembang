<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;
    protected $resetCode;

    public function __construct($resetCode)
    {
        $this->resetCode = $resetCode;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello, ' . ($notifiable->name ?? 'User'))
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('Kode Verification')
            ->markdown('vendor.mail.html.reset-password', [
                'resetCode' => $this->resetCode,
                'header' => view('vendor.mail.html.header', [
                    'url' => config('app.url'),
                    'slot' => "logo",
                ])->render(),
                'footer' => view('vendor.mail.html.footer', [
                    'slot' => $this->footer(),
                ])->render(),
            ])
            ->salutation('Regards, SMK Xaverius Palembang');
    }

    protected function footer()
    {
        return '©2025 SMK Xaverius Palembang powered by Universitas Multi Data Palembang';
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
