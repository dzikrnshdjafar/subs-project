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
            'name' => 'Free',
            'slug' => 'free',
            'description' => 'Akses dasar ke fitur-fitur utama.',
            'price' => 0,
            'duration_days' => null, // Atau bisa juga 9999 jika ingin representasi 'selamanya'
        ]);

        Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'description' => 'Akses ke fitur basic selama 10 hari.',
            'price' => 10, // Contoh harga
            'duration_days' => 10,
        ]);

        Plan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'description' => 'Akses penuh ke semua fitur premium selama 30 hari.',
            'price' => 25, // Contoh harga
            'duration_days' => 30,
        ]);
    }
}
