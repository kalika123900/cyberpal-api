<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MerchantProjectCompletedAccepted extends Notification
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
        return (new MailMessage)
            ->subject(' Well done '. $this->name .'! You’ve just completed a project on CyberPal')
            ->line('Hi '.$this->name)
            ->line('Thanks for putting in your best efforts and completing the project - #'.$this->projectId.' We have notified the client about the same, you can expect an email from us shortly about the acceptance of the work.')
            ->line('Once the client accepts the completion of this project, the funds will be released immediately to your preferred bank account.')
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
