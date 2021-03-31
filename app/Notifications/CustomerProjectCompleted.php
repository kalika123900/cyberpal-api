<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerProjectCompleted extends Notification
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
            ->subject('The expert has marked the project “Completed”')
            ->line('Dear '.$this->name)
            ->line('We wish to inform you that the expert has marked status as “completed” for the project - #'.$this->projectId)
            ->line('If you are happy with the deliverables you can accept the completion of the project by updating the status to “completed” in your dashboard.')
            ->line('In case you feel the project has not been completed the way it was supposed to be, you can raise a dispute by sending an email with your project ID number to our support team at support@cyberpal.io.')
            ->action('I’m happy with the deliverables, let me accept it!', url($url))
            ->line('Thanks!');
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
