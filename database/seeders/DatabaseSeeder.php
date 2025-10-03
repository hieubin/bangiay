<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo user admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@shoeshop.com',
            'is_admin' => true,
        ]);

        // Tạo user thường
        User::factory()->create([
            'name' => 'Người Dùng',
            'email' => 'user@shoeshop.com',
            'is_admin' => false,
        ]);

        // Chạy các seeder
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
