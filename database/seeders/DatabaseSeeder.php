<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		$user = User::create([
            'name' => 'aspire finance',
			'username' => 'aspire finance',
            'email' => 'aspirefinance@gmail.com',
			'mobile'=> '8290027579',
            'password' => Hash::make('admin@123'),
			'is_active'=>1,
			'is_admin'=>1
        ]);
    }
}
