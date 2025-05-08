<?php

namespace App\Notifications;

use App\Mail\CustomResetPassword;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    protected $code;

    /**
     * Create a new notification instance.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new CustomResetPassword($notifiable, $this->code));
    }
}
