<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendResetCodePassword extends Notification
{
    use Queueable;

    public function __construct(public $code)
    {
        
    }

    
    public function via($notifiable)
    {
        return ['mail'];
    }

    
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!')
                    ->line($this->code);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
