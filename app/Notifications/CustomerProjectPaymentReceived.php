<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerProjectPaymentReceived extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $projectId, $amount)
    {
        $this->name = $user->name;
        $this->projectId = $projectId;
        $this->amount = $amount;
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
        $url = env("CLIENT_BASE_URL", "https://cyberpal.io/").'auth/login'; 

        return (new MailMessage)
                    ->subject('Payment Confirmation - We have received your project fee.')
                    ->line('Dear '.$this->name)
                    ->line('Thanks for making a payment of £'. $this->amount .' for the project - #'. $this->projectId .'. You can consider this email as acknowledgement of payment from CyberPal.')
                    ->line('We have duly informed the expert about this payment. The expert will start working on it soon.')
                    ->action('Track your project', url($url))
                    ->line('Thank you!');
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
