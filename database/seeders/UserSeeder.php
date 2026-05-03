<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole   = Role::where('name', 'admin')->first();
        $staffRole   = Role::where('name', 'staff')->first();
        $vetRole     = Role::where('name', 'vet')->first();
        $adopterRole = Role::where('name', 'adopter')->first();

        // Admin
        $admin = User::updateOrCreate(['email' => 'admin@paws.local'], [
            'name'     => 'System Administrator',
            'password' => Hash::make('password'),
            'phone'    => '+63 912 000 0001',
            'status'   => 'active',
        ]);
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // Staff
        $staff = User::updateOrCreate(['email' => 'staff@paws.local'], [
            'name'     => 'Maria Santos',
            'password' => Hash::make('password'),
            'phone'    => '+63 912 000 0002',
            'status'   => 'active',
        ]);
        $staff->roles()->syncWithoutDetaching([$staffRole->id]);

        // Vet
        $vet = User::updateOrCreate(['email' => 'vet@paws.local'], [
            'name'     => 'Dr. Jose Reyes',
            'password' => Hash::make('password'),
            'phone'    => '+63 912 000 0003',
            'status'   => 'active',
        ]);
        $vet->roles()->syncWithoutDetaching([$vetRole->id]);

        // Adopter
        $adopter = User::updateOrCreate(['email' => 'adopter@paws.local'], [
            'name'     => 'Ana Cruz',
            'password' => Hash::make('password'),
            'phone'    => '+63 912 000 0004',
            'status'   => 'active',
        ]);
        $adopter->roles()->syncWithoutDetaching([$adopterRole->id]);
    }
}