<?php

use Illuminate\Database\Seeder;

class EmpTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('emp_types')->insert(array(
            array(
              'name' => 'Executive',
            ),
            array(
                'name' => 'Non Executive',
              ),
              array(
                'name' => 'Trainee',
              ),
              array(
                'name' => 'Intern',
              ),
              array(
                'name' => 'Contract',
              ),
            
          ));
    }
}
