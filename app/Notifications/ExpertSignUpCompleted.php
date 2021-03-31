<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpertSignUpCompleted extends Notification
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
           ->subject('Welcome to CyberPal, '.$this->name)
            ->line('Hi '.$this->name)
            ->line('Give yourself a pat on the back towards taking the first step in finding quality cyber security projects. Excellent decision in joining the new generation platform of virtual cyber workforce.')
            ->line('Finding quality projects is a challenge that cyber security experts often face. Whether you are new to this world or a veteran, if you are looking for an online platform to list your skills and services or want to better position your profile to a cyber security focused audience, then your search ends here.')
            ->line('We will help connect you with credible businesses who are looking to hire qualified and credible experts like you. Our users are purely cyber focused and therefore more qualified and relevant. We will help you connect with these customers. Leave the legwork to us whilst you focus on your career and income!')
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
