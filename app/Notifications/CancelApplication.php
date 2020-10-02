<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancelApplication extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leaveApplication)
    {
        $this->leaveApp = $leaveApplication;
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
        $la = $this->leaveApp;
        $url = url('/leave/apply/view/'.$la->id);
        return (new MailMessage)
                    ->subject('[INFOMINA ELS] Leave Application Cancelled.')
                    ->greeting('Hi,')
                    ->line('Leave application by '.$la->user->name.' has been cancelled:')
                    ->line('Cancelled by: '.$la->remarker->name)
                    ->line('Cancellation Remarks: '.$la->remarks)
                    ->line('Leave type: '.$la->leaveType->name)
                    ->line('From: '.Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YYYY'))
                    ->line('To: '.Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YYYY'))
                    ->line('Total day(s): '.$la->total_days)
                    ->line('Resume date: '.Carbon::parse($la->date_resume)->isoFormat('ddd, D MMM YYYY'))
                    ->line('Reason: '.$la->reason)
                    ->action('View application', $url)
                    ->line('Have a nice day!');
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
