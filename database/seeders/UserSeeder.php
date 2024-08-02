<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
          'email' => 'nguyenvana@admin.com',
          'name' => 'Nguyen Van A',
          'password' => Hash::make('password')
        ]);
    }
}