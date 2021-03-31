<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientAcceptedProposal extends Notification
{ 
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $project)
    {
        $this->name = $user->name;
        $this->projectId = $project->reference_id;
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
            ->subject('You’ve accepted a proposal on CyberPal')
            ->line('Dear '.$this->name)
            ->line('Thanks for taking your project to the next level by accepting one of the proposals for the project #'.$this->projectId)
            ->line('Kindly make the payment for the agreed project fee. Once the payment has been made successfully, we will notify the expert about the escrowed funds.')
            ->line('The Expert will start working on the project after payment confirmation.')
            ->action('Make a payment now', url($url));
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
