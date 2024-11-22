<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'name' => 'Cebu Branch',
            'address' => 'Cebu City, Cebu',
            'phone' => ' ',
        ]);

        Branch::create([
            'name' => 'Makati Branch',
            'address' => 'Makati City, Makati',
            'phone' => ' ',
        ]);

        Branch::create([
            'name' => 'CDO Branch',
            'address' => 'Cagayan De Oro City, Cagayan De Oro',
            'phone' => ' ',
        ]);
    }
}
