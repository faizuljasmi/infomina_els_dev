<?php

use Illuminate\Database\Seeder;

class LeaveTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('leave_types')->insert(array(
            array(
              'name' => 'Annual',
            ),
            array(
                'name' => 'Calamity',
              ),
              array(
                'name' => 'Sick',
              ),
              array(
                'name' => 'Hospitalization',
              ),
              array(
                'name' => 'Compassionate',
              ),
              array(
                'name' => 'Emergency',
              ),
              array(
                'name' => 'Marriage',
              ),
              array(
                'name' => 'Maternity',
              ),
              array(
                'name' => 'Paternity',
              ),
              array(
                'name' => 'Training',
              ),
              array(
                'name' => 'Unpaid',
              ),
              array(
                'name' => 'Replacement',
              ),
            
          ));
    }
}
