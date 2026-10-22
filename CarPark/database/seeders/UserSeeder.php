<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->upsert('admin@carpark.test', 'Administrators', true);
        $this->upsert('user@carpark.test', 'Testa Lietotājs', false);
    }

    private function upsert(string $email, string $name, bool $isAdmin): void
    {
        User::firstOrNew(['email' => $email])
            ->forceFill([
                'name' => $name,
                'password' => 'password',
                'is_admin' => $isAdmin,
            ])
            ->save();
    }
}
