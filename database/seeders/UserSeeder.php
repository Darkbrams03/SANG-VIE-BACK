<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrateur par défaut
        User::updateOrCreate(
            ['matricule' => 'ADMIN-001'],
            [
                'name'     => 'Super Administrateur',
                'password' => 'AdminSangVie2026!', 
                'role'     => 'admin',
            ]
        );

        // Agent par défaut
        User::updateOrCreate(
            ['matricule' => 'AGENT-001'],
            [
                'name'     => 'Agent CNHU de Test',
                'password' => 'AgentSangVie2026!', 
                'role'     => 'agent',
            ]
        );

        $this->command->info('Administrateur et Agent par défaut créés avec succès !');
    }
}