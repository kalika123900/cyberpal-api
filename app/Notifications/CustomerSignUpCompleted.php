<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerSignUpCompleted extends Notification
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
        $url = env("CLIENT_BASE_URL", "https://cyberpal.io/").'solutions'; 

        return (new MailMessage)
            ->subject('Congratulations '. $this->name .'!, You are now part of our growing CyberPal community.')
            ->line('Hi '.$this->name)
            ->line('Finding Your Cyber Security Solutions Just Got Easier Now!')
            ->line('CyberPal makes it possible to qualify, compare, review and ask peers for all your Cyber Security requirements all in one platform. Locate and connect with nearest Resellers for all Vendor solutions.')
            ->line('Get Started in three easy steps:')
            ->line('1 - Discover & Qualify | 2 - Validate & Review | 3 - Connect and Award')
            ->action('Find Solutions', url($url))
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
