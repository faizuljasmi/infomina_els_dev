<?php

/**
 * @author Faizul Jasmi
 * @email faizul.jasmi@infomina.com.my
 * @create date 2020-01-07 09:03:50
 * @modify date 2020-01-07 09:03:50
 * @desc [description]
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class NewApplication extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leaveApp)
    {
        $this->leaveApp = $leaveApp;
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
        if(isset($la->relief_personnel->name)){
            $reliefName = $la->relief_personnel->name;
        }
        else{
            $reliefName = 'NA';
        }
        $url = url('/leave/apply/view/'.$la->id);
        //dd($la->user->name);

        if($la->leave_type_id == '12'){

            return (new MailMessage)
                    ->subject('[INFOMINA ELS] New Leave Application- '.$la->user->name)
                    ->greeting('Hi,')
                    ->line('Leave application by '.$la->user->name.' is waiting for your approval:')
                    ->line('Leave type: '.$la->leaveType->name)
                    ->line('From: '.Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YYYY'))
                    ->line('To: '.Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YYYY'))
                    ->line('Total day(s): '.$la->total_days)
                    ->line('Reason: '.$la->reason)
                    ->line('Approval Authority 1: '.(isset($la->approver_one->name) ? $la->approver_one->name:'NA'))
                    ->line('Approval Authority 2: '.(isset($la->approver_two->name) ? $la->approver_two->name:'NA'))
                    ->line('Approval Authority 3: '.(isset($la->approver_three->name) ? $la->approver_three->name:'NA'))
                    ->action('View application', $url)
                    ->line('Have a nice day!');
        }
        return (new MailMessage)
                    ->subject('[INFOMINA ELS] New Leave Application- '.$la->user->name)
                    ->greeting('Hi,')
                    ->line('Leave application by '.$la->user->name.' is waiting for your approval:')
                    ->line('Leave type: '.$la->leaveType->name)
                    ->line('From: '.Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YYYY'))
                    ->line('To: '.Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YYYY'))
                    ->line('Total day(s): '.$la->total_days)
                    ->line('Resume date: '.Carbon::parse($la->date_resume)->isoFormat('ddd, D MMM YYYY'))
                    ->line('Reason: '.$la->reason)
                    ->line('Relief Personnel: '.$reliefName)
                    ->line('Emergency Contact Name: '.$la->emergency_contact_name)
                    ->line('Emergency Contact No: '.$la->emergency_contact_no)
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
