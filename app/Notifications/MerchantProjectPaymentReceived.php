<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MerchantProjectPaymentReceived extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $projectId, $amount)
    {
        $this->name = $user->name;
        $this->projectId = $projectId;
        $this->amount = $amount;
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
            ->subject('Escrow payment received from client')
            ->line('Hi '.$this->name)
            ->line('We wish to inform you that the client has successfully made the payment (escrow) of £'.$this->amount.' for the project - #'.$this->projectId)
            ->line('This is a confirmation email that CyberPal have received the payment for the project on your behalf. The funds will be released as per your preferred payment options upon successful completion of the project.')
            ->line('It’s time to roll up your cuffs.')
            ->action('Get -> Set -> Go!', url($url))
            ->line('P.S. - Please remember to update the project status using appropriate options on your dashboard at each stage of your project.')
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
