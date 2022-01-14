<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        try{
            \App\Models\Roles::insert([ ['name'=>'lender', 'active'=> true],['name'=>'borrower', 'active'=> true]] );
        }catch (\Exception $e){
            report ($e->getMessage());
        }

    }
}
