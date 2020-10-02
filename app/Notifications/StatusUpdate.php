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
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusUpdate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leaveApplication)
    {
        $this->leaveApplication = $leaveApplication;
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
        $la = $this->leaveApplication;

        if($la->status == 'PENDING_1'|| $la->status == 'PENDING_2'|| $la->status == 'PENDING_3' ){

            $subject = 'Leave Application Status Update';
            $desc = 'Your leave application status has been updated';
            if($la->leave_type_id == "12"){
                $subject = 'Leave Claim Application Status Update';
                $desc = 'Your replacement leave claim application status has been updated';
            }
            if($la->status == 'PENDING_1'){
                $currAuth = $la->approver_one->name;
            }
            else if($la->status == 'PENDING_2'){
                $currAuth = $la->approver_two->name;
            }
            else if($la->status == 'PENDING_3'){
                $currAuth = $la->approver_three->name;
            }
            $stat = 'Waiting approval by '.$currAuth;
        }
        else if($la->status == 'DENIED_1'|| $la->status == 'DENIED_2'|| $la->status == 'DENIED_3' ){

            $subject = 'Leave Application Denied';
            $desc = 'Your leave application has been denied';

            if($la->leave_type_id == "12"){
                $subject = 'Leave Claim Application Denied';
                $desc = 'Your replacement leave claim application has been denied';
            }
            if($la->status == 'DENIED_1'){
                $currAuth = $la->approver_one->name;
            }
            else if($la->status == 'DENIED_2'){
                $currAuth = $la->approver_two->name;
            }
            else if($la->status == 'DENIED_3'){
                $currAuth = $la->approver_three->name;
            }
            $stat = 'Denied by '.$currAuth;
        }
        else{
            $subject = 'Leave Application Approved';
            $desc = 'Your leave application has been approved';
            if($la->leave_type_id == "12"){
                $subject = 'Leave Claim Application Approved';
                $desc = 'Your replacement leave claim application has been approved';
            }
            $stat = 'Approved';
        }

        if($la->leave_type_id == "12"){
            return (new MailMessage)
            ->subject('[INFOMINA ELS] '.$subject)
            ->greeting('Hi,'.$la->user->name)
            ->line($desc)
            ->line('Status: '.$stat)
            ->line('Leave type: '.$la->leaveType->name)
            ->line('From: '.Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YYYY'))
            ->line('To: '.Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YYYY'))
            ->line('Total day(s): '.$la->total_days)
            ->line('Reason: '.$la->reason)
            // ->action('View application', $url)
            ->line('Note: Once approved, your claimed replacement leave will be added to your total balance of Annual Leave')
            ->line('Have a nice day!');
        }

        return (new MailMessage)
        ->subject('[INFOMINA ELS] '.$subject)
        ->greeting('Hi,'.$la->user->name)
        ->line($desc)
        ->line('Status: '.$stat)
        ->line('Leave type: '.$la->leaveType->name)
        ->line('From: '.Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YYYY'))
        ->line('To: '.Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YYYY'))
        ->line('Total day(s): '.$la->total_days)
        ->line('Resume date: '.Carbon::parse($la->date_resume)->isoFormat('ddd, D MMM YYYY'))
        ->line('Reason: '.$la->reason)
        ->line('Relief Personnel: '.$la->relief_personnel->name)
        ->line('Emergency Contact Name: '.$la->emergency_contact_name)
        ->line('Emergency Contact No: '.$la->emergency_contact_no)
        // ->action('View application', $url)
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
