<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;

    class DefaultUserSeeder extends Seeder
    {
        public function run()
        {
            // Clear old data (optional)
            DB::table('users')->where('employeeID', 'E072501')->delete();

            DB::table('users')->insert([
                'employeeID' => 'E072501',
                'password' => Hash::make('09192519300'),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'Warehouse Supervisor',
                'firstname' => 'Archuverjun',
                'lastname' => 'Bartolome',
                'email' => 'ajbuttowski26@gmail.com',
                'status' => 'Active',
            ]);
        }
    }
