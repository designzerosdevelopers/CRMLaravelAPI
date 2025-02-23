<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;



class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $role = Role::create(['name' => 'super-admin']);
        $role = Role::create(['name' => 'organization']);
        $role = Role::create(['name' => 'employee']);
        $role = Role::create(['name' => 'candidate']);


        
    }
}
