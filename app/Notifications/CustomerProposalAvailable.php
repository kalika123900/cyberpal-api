<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerProposalAvailable extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $projectId)
    {
        $this->name = $user->name;
        $this->projectId = $projectId;
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
            ->subject('You have received a proposal from an expert.')
            ->line('Dear '.$this->name)
            ->line('We are happy to inform you that one of the experts have submitted a proposal to work with you on your recently posted project - #'.$this->projectId)
            ->line('Kindly login to your account and review the proposals. In case you need to discuss the project in detail with any of the assigned experts, you can use our chat feature to do so.')
            ->line('To avoid any breach of services, conflict of interest and dispute, we suggest not to discuss and do business with experts outside CyberPal’s platform. Please adhere with our terms and conditions.')
            ->action('Take a look at the proposal now!', url($url))
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
