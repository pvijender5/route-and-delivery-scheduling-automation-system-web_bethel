<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Role::create([
            'name' => 'admin',
            'display_name' => 'Admin',
            'description' => 'This is a Admin ...',
            
        ]);
        Role::create([
            'name' => 'driver',
            'display_name' => 'Driver',
            'description' => 'This is Driver'
        ]);
        Role::create([
            'name' => 'carrier-owner',
            'display_name' => 'Carrier Owner',
            'description' => 'This is Carrier Owner.',

        ]);
        Role::create([
            'name' => 'sub-driver',
            'display_name' => 'Sub Driver',
            'description' => 'This is Sub driver',

        ]);
        
        Role::create([
            'name' => 'merchandiser',
            'display_name' => 'Merchandiser',
            'description' => 'This is  Merchandiser'
        ]);

        Role::create([
            'name' => 'dispatcher',
            'display_name' => 'Dispatcher',
            'description' => 'This is Dispatcher'
        ]);

        Role::create([
            'name' => 'router',
            'display_name' => 'Router',
            'description' => 'This is Router'
        ]);

        Role::create([
            'name' => 'loader',
            'display_name' => 'Loader',
            'description' => 'This is Loader',
        ]);
        
    }
}
