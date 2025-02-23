<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class Permissions extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $createPermission = Permission::create(['name' => 'job-create']);
        $viewPermission = Permission::create(['name' => 'job-view']);
        $editPermission = Permission::create(['name' => 'job-edit']);
        $deletePermission = Permission::create(['name' => 'job-delete']);

           }
}
