<?php

namespace Database\Seeders;

use App\Models\assgin;
use App\Models\Assign;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Assign::factory(10)->create();
        Member::factory(10)->create();
    }
}
