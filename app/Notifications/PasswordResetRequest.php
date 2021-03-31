<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetRequest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $email, $user)
    {
        $this->token = $token;
        $this->email = $email;
        $this->name = $user->name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = env("CLIENT_BASE_URL", "https://cyberpal.io/").'auth/reset-password/'.$this->token.'?email='.$this->email; 

        return (new MailMessage)
            ->subject($this->name.', Here is your password reset link.')
            ->line('Hi '.$this->name)
            ->line('We received a request to reset your CyberPal password. Please click below to reset your password. This link will expire in 24 hrs.')
            ->action('Reset Password', url($url))
            ->line('If you didn’t request this password reset, contact support@cyberpal.io and report it.')
            ->line('Thanks');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
