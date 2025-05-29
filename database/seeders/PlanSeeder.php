<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Gold',
            'slug' => 'gold',
            'description' => 'Akses dasar ke fitur-fitur utama.',
            'price' => 1000,
            'duration_days' => 10,
            'rank' => 1, // Tambahkan ini
        ]);

        Plan::create([
            'name' => 'Platinum',
            'slug' => 'platinum',
            'description' => 'Akses ke fitur platinum selama 10 hari.',
            'price' => 2000,
            'duration_days' => 20,
            'rank' => 2, // Tambahkan ini
        ]);

        Plan::create([
            'name' => 'Diamond',
            'slug' => 'diamond',
            'description' => 'Akses penuh ke semua fitur diamond selama 30 hari.',
            'price' => 2500,
            'duration_days' => 30,
            'rank' => 3, // Tambahkan ini
        ]);
    }
}
