<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
          'id' => 1,
          'email' => 'admin@admin.com',
          'name' => 'Admin',
          'password' => Hash::make('password'),
        ]);
    }
}