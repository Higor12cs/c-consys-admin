<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([IndicatorSeeder::class, ScheduleSeeder::class]);

        User::create([
            'name' => 'Admin User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Customer::create([
            'external_id' => 'CUST001',
            'name' => 'Customer One',
            'api_token' => bin2hex(random_bytes(30)),
            'is_active' => true,
            'last_synced_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}
