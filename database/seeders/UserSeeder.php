<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                    // Dans la méthode run() de ton UserSeeder.php :
            User::updateOrCreate(
                ['matricule' => 'ADMIN-001'],
                [
                    'name' => 'Super Administrateur', // 👈 'name' au lieu de 'fullname'
                    'password' => Hash::make('AdminSangVie2026!'),
                    'role' => 'admin',
                ]
            );

            User::updateOrCreate(
                ['matricule' => 'AGENT-001'],
                [
                    'name' => 'Agent CNHU de Test',   // 👈 'name' au lieu de 'fullname'
                    'password' => Hash::make('AgentSangVie2026!'),
                    'role' => 'agent',
                ]
            );

        $this->command->info('Administrateur et Agent par défaut créés avec succès !');
    }
}