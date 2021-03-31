<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MerchantLeadAssigned extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
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
        $url = env("MERCHANT_BASE_URL", "https://partners.cyberpal.io/"); 

        return (new MailMessage)
                ->subject('New enquiry received via CyberPal')
                ->line('Dear  '.$this->name)
                ->line('A potential prospect has shown interest in your solution. Login to your CyberPal partner account to find out more. Review their requirements and get in touch with them as early as possible.')
                ->line('Please remember to update the status of each enquiry on cyberpal to ensure you can analyze all your enquiries statistics accurately.')
                ->action('Login to dashboard', url($url))
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
