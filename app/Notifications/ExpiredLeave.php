<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpiredLeave extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leave)
    {
        $this->leave = $leave;
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
        $leave = $this->leave;

        $message = new MailMessage;
        $message->subject('[INFOMINA ELS] - Expired Replacement Leave');
        $message->greeting('Hi '.$leave['name'].',');
        $message->line('Your replacement leave claim dated from '.$leave['claim_from'].' to '.$leave['claim_to'].' ('.$leave['claim'].') has been expired.');
        $message->line('The unused '.$leave['burnt'].' day(s) from this claim has been burned.');
        $message->line('Your new balance for replacement leave would be '.$leave['balance'].' day(s).');
        $message->line('Please contact the HR if you need any further clarification.');

        return $message;
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
