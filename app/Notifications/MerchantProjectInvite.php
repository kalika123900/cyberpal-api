<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MerchantProjectInvite extends Notification
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
        $url = env("MERCHANT_BASE_URL", "https://partners.cyberpal.io/").'login'; 

        return (new MailMessage)
            ->subject('Congratulations! You have been assigned to a new project on CyberPal')
            ->line('Hey '.$this->name)
            ->line('We are happy to inform you that you have been assigned to work on one of the recently posted projects on CyberPal.')
            ->line('The client will be reviewing profiles and proposals. We suggest you look at the project details intricately and send an outstanding proposal.')
            ->line('Maximize your chances of getting selected.')
            ->action('Send a proposal now!', url($url))
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
