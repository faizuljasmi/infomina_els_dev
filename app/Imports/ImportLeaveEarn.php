<?php

namespace App\Imports;

use App\User;
use App\LeaveEarning;
use App\TakenLeave;
use App\LeaveBalance;
use App\BroughtForwardLeave;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ImportLeaveEarn implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function headingRow(): int
    {
        return 2;
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $user = User::orderBy('id','ASC')->where('name', @$row['name'])->first();
            if($user == null){
                return null;
            }
            $user_id = $user->id;

            //Annual
            $le1 = new LeaveEarning;
            $le1->leave_type_id = '1';
            $le1->user_id = $user_id;
            $le1->no_of_days = @$row['totann'];
            $le1->save();
            $bf1 = new BroughtForwardLeave;
            $bf1->leave_type_id = '1';
            $bf1->user_id = $user_id;
            $bf1->no_of_days = @$row['c1'];
            $bf1->save();
            $tl1 = new TakenLeave;
            $tl1->leave_type_id = '1';
            $tl1->user_id = $user_id;
            $tl1->no_of_days = @$row['t1'];
            $tl1->save();
            $lb1 = new LeaveBalance;
            $lb1->leave_type_id = '1';
            $lb1->user_id = $user_id;
            $lb1->no_of_days = @$row['b1'];
            $lb1->save();

            //Calamity
            $le2 = new LeaveEarning;
            $le2->leave_type_id = '2';
            $le2->user_id = $user_id;
            $le2->no_of_days = @$row['e2'];
            $le2->save();
            $bf2 = new BroughtForwardLeave;
            $bf2->leave_type_id = '2';
            $bf2->user_id = $user_id;
            $bf2->no_of_days = @$row['a'];
            $bf2->save();
            $tl2 = new TakenLeave;
            $tl2->leave_type_id = '2';
            $tl2->user_id = $user_id;
            $tl2->no_of_days = @$row['t2'];
            $tl2->save();
            $lb2 = new LeaveBalance;
            $lb2->leave_type_id = '2';
            $lb2->user_id = $user_id;
            $lb2->no_of_days = @$row['b2'];
            $lb2->save();

            //Sick
            $le3 = new LeaveEarning;
            $le3->leave_type_id = '3';
            $le3->user_id = $user_id;
            $le3->no_of_days = @$row['e3'];
            $le3->save();
            $bf3 = new BroughtForwardLeave;
            $bf3->leave_type_id = '3';
            $bf3->user_id = $user_id;
            $bf3->no_of_days = @$row['b'];
            $bf3->save();
            $tl3 = new TakenLeave;
            $tl3->leave_type_id = '3';
            $tl3->user_id = $user_id;
            $tl3->no_of_days = @$row['t3'];
            $tl3->save();
            $lb3 = new LeaveBalance;
            $lb3->leave_type_id = '3';
            $lb3->user_id = $user_id;
            $lb3->no_of_days = @$row['b3'];
            $lb3->save();
       
            //Hospitalization
            $le4 = new LeaveEarning;
            $le4->leave_type_id = '4';
            $le4->user_id = $user_id;
            $le4->no_of_days = @$row['e4'];
            $le4->save();
            $bf4 = new BroughtForwardLeave;
            $bf4->leave_type_id = '4';
            $bf4->user_id = $user_id;
            $bf4->no_of_days = @$row['c'];
            $bf4->save();
            $tl4 = new TakenLeave;
            $tl4->leave_type_id = '4';
            $tl4->user_id = $user_id;
            $tl4->no_of_days = @$row['t4'];
            $tl4->save();
            $lb4 = new LeaveBalance;
            $lb4->leave_type_id = '4';
            $lb4->user_id = $user_id;
            $lb4->no_of_days = @$row['b4'];
            $lb4->save();

            //Compassionate
            $le5 = new LeaveEarning;
            $le5->leave_type_id = '5';
            $le5->user_id = $user_id;
            $le5->no_of_days = @$row['e5'];
            $le5->save();
            $bf5 = new BroughtForwardLeave;
            $bf5->leave_type_id = '5';
            $bf5->user_id = $user_id;
            $bf5->no_of_days = @$row['d'];
            $bf5->save();
            $tl5 = new TakenLeave;
            $tl5->leave_type_id = '5';
            $tl5->user_id = $user_id;
            $tl5->no_of_days = @$row['t5'];
            $tl5->save();
            $lb5 = new LeaveBalance;
            $lb5->leave_type_id = '5';
            $lb5->user_id = $user_id;
            $lb5->no_of_days = @$row['b5'];
            $lb5->save();

            //Emergency
            $le6 = new LeaveEarning;
            $le6->leave_type_id = '6';
            $le6->user_id = $user_id;
            $le6->no_of_days = @$row['e6'];
            $le6->save();
            $bf6 = new BroughtForwardLeave;
            $bf6->leave_type_id = '6';
            $bf6->user_id = $user_id;
            $bf6->no_of_days = @$row['e'];
            $bf6->save();
            $tl6 = new TakenLeave;
            $tl6->leave_type_id = '6';
            $tl6->user_id = $user_id;
            $tl6->no_of_days = @$row['t6'];
            $tl6->save();
            $lb6 = new LeaveBalance;
            $lb6->leave_type_id = '6';
            $lb6->user_id = $user_id;
            $lb6->no_of_days = @$row['b6'];
            $lb6->save();

            //Marriage
            $le9 = new LeaveEarning;
            $le9->leave_type_id = '7';
            $le9->user_id = $user_id;
            $le9->no_of_days = @$row['e9'];
            $le9->save();
            $bf9 = new BroughtForwardLeave;
            $bf9->leave_type_id = '7';
            $bf9->user_id = $user_id;
            $bf9->no_of_days = @$row['f'];
            $bf9->save();
            $tl9 = new TakenLeave;
            $tl9->leave_type_id = '7';
            $tl9->user_id = $user_id;
            $tl9->no_of_days = @$row['t9'];
            $tl9->save();
            $lb9 = new LeaveBalance;
            $lb9->leave_type_id = '7';
            $lb9->user_id = $user_id;
            $lb9->no_of_days = @$row['b9'];
            $lb9->save();

            //Maternity
            $le7 = new LeaveEarning;
            $le7->leave_type_id = '8';
            $le7->user_id = $user_id;
            $le7->no_of_days = @$row['e7'];
            $le7->save();
            $bf7 = new BroughtForwardLeave;
            $bf7->leave_type_id = '8';
            $bf7->user_id = $user_id;
            $bf7->no_of_days = @$row['g'];
            $bf7->save();
            $tl7 = new TakenLeave;
            $tl7->leave_type_id = '8';
            $tl7->user_id = $user_id;
            $tl7->no_of_days = @$row['t7'];
            $tl7->save();
            $lb7 = new LeaveBalance;
            $lb7->leave_type_id = '8';
            $lb7->user_id = $user_id;
            $lb7->no_of_days = @$row['e7'];
            $lb7->save();

            //Paternity
            $le8 = new LeaveEarning;
            $le8->leave_type_id = '9';
            $le8->user_id = $user_id;
            $le8->no_of_days = @$row['e8'];
            $le8->save();
            $bf8 = new BroughtForwardLeave;
            $bf8->leave_type_id = '9';
            $bf8->user_id = $user_id;
            $bf8->no_of_days = @$row['h'];
            $bf8->save();
            $tl8 = new TakenLeave;
            $tl8->leave_type_id = '9';
            $tl8->user_id = $user_id;
            $tl8->no_of_days = @$row['t8'];
            $tl8->save();
            $lb8 = new LeaveBalance;
            $lb8->leave_type_id = '9';
            $lb8->user_id = $user_id;
            $lb8->no_of_days = @$row['b8'];
            $lb8->save();

            //Traning
            $lea = new LeaveEarning;
            $lea->leave_type_id = '10';
            $lea->user_id = $user_id;
            $lea->no_of_days = @$row['e10'];
            $lea->save();
            $bf10 = new BroughtForwardLeave;
            $bf10->leave_type_id = '10';
            $bf10->user_id = $user_id;
            $bf10->no_of_days = @$row['i'];
            $bf10->save();
            $tla = new TakenLeave;
            $tla->leave_type_id = '10';
            $tla->user_id = $user_id;
            $tla->no_of_days = @$row['t10'];
            $tla->save();
            $lba = new LeaveBalance;
            $lba->leave_type_id = '10';
            $lba->user_id = $user_id;
            $lba->no_of_days = @$row['b10'];
            $lba->save();

            //Unpaid
            $leb = new LeaveEarning;
            $leb->leave_type_id = '11';
            $leb->user_id = $user_id;
            $leb->no_of_days = @$row['e11'];
            $leb->save();
            $bf11 = new BroughtForwardLeave;
            $bf11->leave_type_id = '11';
            $bf11->user_id = $user_id;
            $bf11->no_of_days = @$row['j'];
            $bf11->save();
            $tlb = new TakenLeave;
            $tlb->leave_type_id = '11';
            $tlb->user_id = $user_id;
            $tlb->no_of_days = @$row['t11'];
            $tlb->save();
            $lbb = new LeaveBalance;
            $lbb->leave_type_id = '11';
            $lbb->user_id = $user_id;
            $lbb->no_of_days = @$row['b11'];
            $lbb->save();

            //Replacement
            $lec = new LeaveEarning;
            $lec->leave_type_id = '12';
            $lec->user_id = $user_id;
            $lec->no_of_days = @$row['e12'];
            $bf12 = new BroughtForwardLeave;
            $bf12->leave_type_id = '12';
            $bf12->user_id = $user_id;
            $bf12->no_of_days = @$row['k'];
            $bf12->save();
            $lec->save();
            $tlc = new TakenLeave;
            $tlc->leave_type_id = '12';
            $tlc->user_id = $user_id;
            $tlc->no_of_days = @$row['t12'];
            $tlc->save();
            $lbc = new LeaveBalance;
            $lbc->leave_type_id = '12';
            $lbc->user_id = $user_id;
            $lbc->no_of_days = @$row['b12'];
            $lbc->save();
        }
    }
}