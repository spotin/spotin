<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = config('admin.name');
        $email = Str::lower(trim(config('admin.email')));
        $password = config('admin.password');

        $admin = User::firstOrNew(['email' => $email]);

        if (! $admin->exists) {
            $admin->name = $name;
            $admin->password = $password;
        }

        $admin->is_admin = true;
        $admin->email_verified_at ??= now();
        $admin->save();
    }
}
