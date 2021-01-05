<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HRUpdate extends Notification
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

        $count = 1;
        $nameList = $employee['name_list'];

        $message = new MailMessage;
        $message->subject("[INFOMINA ELS] - Employee's Prorated ".$employee['leave']." Leave Update");
        $message->greeting('Hi '.$employee['admin'].',');
        $message->line('Below would be the list of employee(s) that have been affected by '.$employee['leave'].' leave prorate calculation for this month.');
        foreach($nameList as $name) {
            $message->line(nl2br($count.'. '.$name));
            $count++;
        }
        $message->line('');
        $message->line('Thank you.');

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
