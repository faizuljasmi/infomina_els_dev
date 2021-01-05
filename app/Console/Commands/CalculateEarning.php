<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\LeaveEarning;
use App\LeaveBalance;
use App\TakenLeave;
use App\BroughtForwardLeave;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\Notifications\EarningUpdate;

class CalculateEarning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:earning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To calculate annual leave earning.';

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
        $employees = User::get();

        $today = Carbon::now();
        $currentMonth = $today->month;
        $currentYear = $today->year;

        foreach($employees as $emp) {
            if ($emp->emp_type_id != 3 || $emp->emp_type_id != 4 || $emp->emp_type_id != 5) // Executive and Non-Executive Staff Only.
            {
                $carryForw = 0;
                $annualEnt = 0;
                $annualBal = 0;
                $mcEnt = 0;

                $annualLeave = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 1)->first();

                if ($annualLeave != null) {
                    $annualBal = $annualLeave->no_of_days;
                    if ($annualBal >= 0 && $annualBal <= 5) {
                        $carryForw = $annualBal;
                    } else if ($annualBal > 5) {
                        $carryForw = 5; // Max carry forward is only 5.
                    }

                    // Record carry forward leaves.
                    $newCarryForw = BroughtForwardLeave::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                    $newCarryForw->no_of_days = $carryForw;
                    $newCarryForw->update();
                
                    $joinDate = Carbon::parse($emp->join_date);
                    $to = Carbon::parse($currentYear.'-'.$currentMonth);
                    $diff = $joinDate->diffInMonths($to);
                    
                    // Annual Leave
                    if (($diff + 1) < 36) {
                        if ($emp->emp_type_id == 1 || $emp->emp_type_id == 6 || $emp->emp_type_id == 7 || $emp->emp_type_id == 8) { // Executive
                            $annualEnt = 14;
                        } else if ($emp->emp_type_id == 2) { // Non Executive
                            $annualEnt = 12;
                        }
                    } else if (($diff + 1) >= 36 && ($diff + 1) < 60) {
                        if ($emp->emp_type_id == 1 || $emp->emp_type_id == 6 || $emp->emp_type_id == 7 || $emp->emp_type_id == 8) { // Executive
                            $annualEnt = 16;
                        } else if ($emp->emp_type_id == 2) { // Non Executive
                            $annualEnt = 14;
                        }
                    } else if (($diff + 1) >= 60) {
                        if ($emp->emp_type_id == 1 || $emp->emp_type_id == 6 || $emp->emp_type_id == 7 || $emp->emp_type_id == 8) { // Executive
                            $annualEnt = 18;
                        } else if ($emp->emp_type_id == 2) { // Non Executive
                            $annualEnt = 16;
                        }
                    }
    
                    // Medical Leave
                    if (($diff + 1) < 24) {
                        $mcEnt = 14;
                    } else if (($diff + 1) >= 24 && ($diff + 1) < 60) {
                        $mcEnt = 18;
                    } else if (($diff + 1) >= 60) {
                        $mcEnt = 22;
                    }
                    
                    for($leaveType = 1; $leaveType <= 13; $leaveType++) // Total 13 leave types
                    { 
                        $empEarning = LeaveEarning::where('user_id', $emp->id)->where('leave_type_id', $leaveType)->first();
                        $empBal = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', $leaveType)->first();

                        if ($empEarning != null) {
                            if ($leaveType == 1) {
                                $empEarning->no_of_days = $carryForw + $annualEnt; // Annual
                                $empBal->no_of_days = $carryForw + $annualEnt;
                            } else if ($leaveType == 2) {
                                $empEarning->no_of_days = 1; // Calamity
                                $empBal->no_of_days = 1;
                            } else if ($leaveType == 3) { 
                                $empEarning->no_of_days = $mcEnt; // Sick
                                $empBal->no_of_days = $mcEnt;
                            } else if ($leaveType == 4) {
                                $empEarning->no_of_days = 60; // Hospitalization
                                $empBal->no_of_days = 60;
                            } else if ($leaveType == 5) {
                                $empEarning->no_of_days = 2; // Compassionate
                                $empBal->no_of_days = 2;
                            } else if ($leaveType == 6) {
                                $empEarning->no_of_days = 5; // Emergency
                                $empBal->no_of_days = 5;
                            } else if ($leaveType == 7) {
                                $empEarning->no_of_days = 2; // Marriage
                                $empBal->no_of_days = 2;
                            } else if ($leaveType == 8) {
                                $empEarning->no_of_days = 0; // Maternity
                                $empBal->no_of_days = 0;
                            } else if ($leaveType == 9) {
                                $empEarning->no_of_days = 0; // Paternity
                                $empBal->no_of_days = 0;
                            } else if ($leaveType == 10) {
                                $empEarning->no_of_days = 10; // Training
                                $empBal->no_of_days = 10;
                            } else if ($leaveType == 11) {
                                $empEarning->no_of_days = 0; // Unpaid
                                $empBal->no_of_days = 0;
                            } else if ($leaveType == 12) {
                                $empEarning->no_of_days = 0; // Replacement
                                $empBal->no_of_days = 0;
                            } else if ($leaveType == 13){
                                $empEarning->no_of_days = 0; // Wedding
                                $empBal->no_of_days = 0;
                            }
                            $empEarning->update();
                            $empBal->update();
                        }

                        $empTaken = TakenLeave::where('user_id', $emp->id)->where('leave_type_id', $leaveType)->first();

                        if ($empBal != null) {
                            $empTaken->no_of_days = 0;
                            $empTaken->update();
                        }
                    }
                    
                    // if ($emp->id == 102) {
                        $emp->notify(new EarningUpdate($emp));
                    // }
                }
            }
        }
    }
}
