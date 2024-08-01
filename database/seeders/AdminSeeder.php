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
          'id' => 'c90b87d0-571f-4931-8a60-5e682dc965ec',
          'email' => 'admin@admin.com',
          'name' => 'Admin',
          'password' => Hash::make('password'),
        ]);
    }
}