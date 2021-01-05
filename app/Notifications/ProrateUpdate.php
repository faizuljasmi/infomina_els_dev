<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProrateUpdate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($employee)
    {
        $this->employee = $employee;
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
        $employee = $this->employee;

        return (new MailMessage)
        ->subject('[INFOMINA ELS] - Prorated '.$employee['leave'].' Leave')
        ->greeting('Hi '.$employee['name'].',')
        ->line('Your '.$employee['leave'].' leave have been adjusted/prorated according to the years of completed service with the company.')
        ->line('Thus, you have earned an additional '.$employee['gain'].' day(s), and your new balance for '.$employee['leave'].' leave would be '.$employee['balance'].' day(s).')
        ->line('Please contact the HR if you need any further clarification.');
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
