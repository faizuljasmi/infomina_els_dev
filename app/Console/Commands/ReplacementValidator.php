<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use App\BurntLeave;
use App\LeaveApplication;
use App\LeaveBalance;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\Notifications\ExpiredLeave;
use Carbon\Carbon;

class ReplacementValidator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:replacement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check the validity of the available replacement leave.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = new DateTime();

        $claims = LeaveApplication::where('leave_type_id', 12)->where('remarks', 'Claim')->get();
        
        foreach($claims as $claim) {
            $start = new DateTime($claim->date_from);
            $diff = $start->diff($today)->format('%a');

            // If claim passed more than 30 days and not utilized fully.
            if ($diff >= 30 && $claim->status != 'TAKEN' && $claim->status != 'EXPIRED') {
                // Change claim status to Expired.
                $claim->status = 'EXPIRED';
                $claim->update();
                // Calculate taken days.
                $totalTaken = 0;
                
                foreach($claim->replacement_applications as $leaveApp) {
                    $leaveStatus = $leaveApp->application->status;
                    if ($leaveStatus == 'APPROVED' || $leaveStatus == 'PENDING_1' || $leaveStatus == 'PENDING_2' || $leaveStatus == 'PENDING_3') {
                        $totalTaken += $leaveApp->application->total_days;
                    }
                }
                
                $claimedDays = $claim->total_days;
                $balanceForThisClaim = $claimedDays - $totalTaken;
                // echo $balanceForThisClaim;
                
                $burntLeave = BurntLeave::where('user_id', $claim->user_id)->where('leave_type_id', 12)->first();
                if ($burntLeave) {
                    $tempBurn = $burntLeave->no_of_days;
                    $burntLeave->no_of_days = $tempBurn + $balanceForThisClaim;
                    $burntLeave->update();
                } else {
                    $burnt = new BurntLeave;
                    $burnt->leave_type_id = 12;
                    $burnt->user_id = $claim->user_id;
                    $burnt->no_of_days = $balanceForThisClaim;
                    $burnt->save();
                }

                $leaveBalance = LeaveBalance::where('user_id', $claim->user_id)->where('leave_type_id', 12)->first();
                $tempBal= $leaveBalance->no_of_days;
                if ($tempBal > 0) {
                    $leaveBalance->no_of_days = $tempBal - $balanceForThisClaim;
                    $leaveBalance->update();
                }

                $leave = [
                    'name' => $claim->user->name,
                    'claim' => $claim->reason, 
                    'claim_from' => $claim->date_from, 
                    'claim_to' => $claim->date_to, 
                    'burnt' => $balanceForThisClaim,
                    'balance' => $leaveBalance->no_of_days
                ];
                
                if ($balanceForThisClaim > 0) {
                    $claim->user->notify(new ExpiredLeave($leave));
                }

            }
        }
    }
}
