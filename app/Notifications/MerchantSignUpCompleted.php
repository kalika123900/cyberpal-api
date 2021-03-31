<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MerchantSignUpCompleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user->name;
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
            ->subject('Welcome to CyberPal, '.$this->name)
            ->line('Hi '.$this->name)
            ->line('Generating qualified prospects is a challenge that cyber security solution providers often face. Whether you are a startup or a Large Enterprise, if you are looking for an online platform to list your products & services or want to better position your service to a cyber focused audience, then your search ends here.')
            ->line('We will help connect you with your targeted audience by providing you insights and analytics of relevant end users enabling you to validate your offerings and influence more buyers. Our users are purely cyber focused and therefore more qualified and relevant. We will help you listen to the real voice of your customers. Now that’s value!')
            ->line('Get Started.')
            ->action('Complete your profile', url($url))
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
