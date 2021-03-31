<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProposalAvailable extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
            ->subject('Your project status with cyberpal')
            ->line('A new proposal has been submitted by one of our expert.')
            ->line('Please login to your account and review the proposal. If it matches your requirement, feel free to get in touch with the expert.')
            ->line('Please note, only one proposal can be accepted for one project. So, first review all proposals and accept only once your 
            project\'s requirement have been meet.')
            ->action('Go to your dashboard', url($url))
            ->line('Feel free to get in touch with us for any queries.');
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
