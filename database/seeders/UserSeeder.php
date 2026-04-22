<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Staff User
        User::create([
            'name' => 'Staff Officer',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // Create Borrower/Member User
        User::create([
            'name' => 'John Borrower',
            'email' => 'borrower@example.com',
            'password' => Hash::make('password'),
            'role' => 'borrower',
        ]);

        // Optional: Create additional sample borrowers
        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'borrower',
        ]);

        User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike@example.com',
            'password' => Hash::make('password'),
            'role' => 'borrower',
        ]);

        $this->command->info('✅ Users seeded successfully!');
        $this->command->newLine();
        $this->command->info('📋 Login Credentials:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@example.com', 'password'],
                ['Staff', 'staff@example.com', 'password'],
                ['Borrower', 'borrower@example.com', 'password'],
            ]
        );
    }
}