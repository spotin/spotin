<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'bio' => 'Software developer passionate about Laravel and modern web technologies.',
            'enabled' => true,
        ]);

        User::factory()->create([
            'username' => 'janedoe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'bio' => 'Designer & creative thinker.',
            'enabled' => true,
        ]);
    }
}
