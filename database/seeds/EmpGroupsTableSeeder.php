<?php

use Illuminate\Database\Seeder;

class EmpGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('emp_groups')->insert(array(
            array(
              'name' => 'Front End',
            ),
            array(
                'name' => 'Back End',
              ),
              array(
                'name' => 'Top Management',
              ),
              array(
                'name' => 'Middle Management',
              ),
              array(
                'name' => 'Admin',
              ),
              array(
                'name' => 'Finance',
              ),
              array(
                'name' => 'Helpdesk',
              ),
              array(
                'name' => 'PMO',
              ),
              array(
                'name' => 'DBA',
              ),
              array(
                'name' => 'MyKAD ICSC',
              ),
              array(
                'name' => 'HOST & DR Support',
              ),
              array(
                'name' => 'Support Engineer',
              ),
              array(
                'name' => 'Sales',
              ),
              array(
                'name' => 'Business Analyst',
              ),
              array(
                'name' => 'MyKAD',
              ),
              array(
                'name' => 'Non MyKAD',
              ),
              array(
                'name' => 'HR',
              ),
              array(
                'name' => 'Testing Unit',
              ),
              array(
                'name' => 'Application Maintenance',
              ),
              array(
                'name' => 'Technical',
              ),
              array(
                'name' => 'Transformation',
              ),
              array(
                'name' => 'ICSC',
              ),
            
          ));
    }
}
