<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Organization;

class Credentials extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $superadmin = User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ])->assignRole('super-admin');

        $orgUser = User::create([
            'name' => 'org',
            'email' => 'org@gmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ])->assignRole('organization');

        Organization::create([
            'user_id' => $orgUser->id,
            'organization_name' => 'designzeros',
            'website' => 'designzeros.com',
        ]);

    
        $superadmin = Role::findByName('super-admin');
        $superadmin->givePermissionTo(Permission::all());
        $orgRole = Role::findByName('organization');
        $orgRole->givePermissionTo(['job-delete', 'job-edit', 'job-create', 'job-view']);
        $empRole = Role::findByName('employee');
        $empRole->givePermissionTo(['job-delete', 'job-edit', 'job-create', 'job-view']);
        
           }
}
