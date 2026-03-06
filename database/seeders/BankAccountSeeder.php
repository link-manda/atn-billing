<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\BankAccount::create([
            'bank_name' => 'Bank Central Asia (BCA)',
            'account_name' => 'PT Aplikasi Teknologi Nusantara',
            'account_number' => '1234567890',
            'is_default' => true,
            'is_active' => true,
        ]);
    }
}
