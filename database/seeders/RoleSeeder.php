<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',   'display_name' => 'Administrator', 'color' => 'danger',  'description' => 'Full system access'],
            ['name' => 'staff',   'display_name' => 'Staff',         'color' => 'primary', 'description' => 'Pet and application management'],
            ['name' => 'vet',     'display_name' => 'Veterinarian',  'color' => 'info',    'description' => 'Medical records management'],
            ['name' => 'adopter', 'display_name' => 'Adopter',       'color' => 'success', 'description' => 'Pet adoption applicant'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}