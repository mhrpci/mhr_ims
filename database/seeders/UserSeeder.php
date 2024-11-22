<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create the admin user if doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'administrator@access.mhrpci'],
            [
                'username' => 'Administrator',
                'password' => Hash::make('Letmein@2024'),
            ]
        );

        // Create Cebu branch manager
        $cebuBranchManager = User::firstOrCreate(
            ['email' => 'cebu.branch.manager@access.mhrpci'],
            [
                'username' => 'Cebu Branch Manager',
                'password' => Hash::make('cebu.branch.manager@2024'),
                'branch_id' => 1,
            ]
        );

        // Create Cebu stock manager
        $cebuStockManager = User::firstOrCreate(
            ['email' => 'cebu.stock.manager@access.mhrpci'],
            [
                'username' => 'Cebu Stock Manager',
                'password' => Hash::make('cebu.stock.manager@2024'),
                'branch_id' => 1,
            ]
        );

        // Create Makati branch manager
        $makatiBranchManager = User::firstOrCreate(
            ['email' => 'makati.branch.manager@access.mhrpci'],
            [
                'username' => 'Makati Branch Manager',
                'password' => Hash::make('makati.branch.manager@2024'),
                'branch_id' => 2,
            ]
        );

        // Create Makati stock manager
        $makatiStockManager = User::firstOrCreate(
            ['email' => 'makati.stock.manager@access.mhrpci'],
            [
                'username' => 'Makati Stock Manager',
                'password' => Hash::make('makati.stock.manager@2024'),
                'branch_id' => 2,
            ]
        );

        // Create CDO branch manager
        $cdoBranchManager = User::firstOrCreate(
            ['email' => 'cdo.branch.manager@access.mhrpci'],
            [
                'username' => 'CDO Branch Manager',
                'password' => Hash::make('cdo.branch.manager@2024'),
                'branch_id' => 3,
            ]
        );

        // Create CDO stock manager
        $cdoStockManager = User::firstOrCreate(
            ['email' => 'cdo.stock.manager@access.mhrpci'],
            [
                'username' => 'CDO Stock Manager',
                'password' => Hash::make('cdo.stock.manager@2024'),
                'branch_id' => 3,
            ]
        );

        // Fetch the roles
        $adminRole = Role::where('name', 'Super Admin')->first();
        $branchManagerRole = Role::where('name', 'Branch Manager')->first();
        $stockManagerRole = Role::where('name', 'Stock Manager')->first();

        // Sync roles instead of attach to prevent duplicates
        $adminUser->roles()->sync($adminRole);
        $cebuBranchManager->roles()->sync($branchManagerRole);
        $cebuStockManager->roles()->sync($stockManagerRole);
        $makatiBranchManager->roles()->sync($branchManagerRole);
        $makatiStockManager->roles()->sync($stockManagerRole);
        $cdoBranchManager->roles()->sync($branchManagerRole);
        $cdoStockManager->roles()->sync($stockManagerRole);
    }
}
