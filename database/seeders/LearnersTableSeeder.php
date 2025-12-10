<?php

namespace Database\Seeders;

use App\Models\Learner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearnersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $learners = [
            ['firstname' => 'Amahle', 'lastname' => 'Ndlovu'],
            ['firstname' => 'Angel', 'lastname' => 'Mkhize'],
            ['firstname' => 'Angel', 'lastname' => 'Stevens'],
            ['firstname' => 'Asemahle', 'lastname' => 'Du Toit'],
            ['firstname' => 'Asemahle', 'lastname' => 'Mkhize'],
            ['firstname' => 'Asemahle', 'lastname' => 'Nkosi'],
            ['firstname' => 'Asemahle', 'lastname' => 'Smith'],
            ['firstname' => 'Blessing', 'lastname' => 'Gumede'],
            ['firstname' => 'Blessing', 'lastname' => 'Stadler'],
            ['firstname' => 'Blessing', 'lastname' => 'Stevens'],
            ['firstname' => 'Cindy', 'lastname' => 'Johannisen'],
            ['firstname' => 'Gift', 'lastname' => 'Ndlovu'],
            ['firstname' => 'Gift', 'lastname' => 'Stevens'],
            ['firstname' => 'Grace', 'lastname' => 'Khumalo'],
            ['firstname' => 'Grace', 'lastname' => 'Ndlovu'],
            ['firstname' => 'Iminathi', 'lastname' => 'Stevens'],
            ['firstname' => 'Innocent', 'lastname' => 'Ndlovu'],
            ['firstname' => 'Jacob', 'lastname' => 'Smith'],
            ['firstname' => 'Jayden', 'lastname' => 'Nkosi'],
            ['firstname' => 'Joanie', 'lastname' => 'Stadler'],
            ['firstname' => 'John', 'lastname' => 'Smith'],
            ['firstname' => 'Johnathan', 'lastname' => 'Botha'],
            ['firstname' => 'Johnathan', 'lastname' => 'Stadler'],
            ['firstname' => 'Johnathan', 'lastname' => 'Van Der Merwe'],
            ['firstname' => 'Junior', 'lastname' => 'Botha'],
            ['firstname' => 'Junior', 'lastname' => 'Dlamini'],
            ['firstname' => 'Junior', 'lastname' => 'Johannisen'],
            ['firstname' => 'Junior', 'lastname' => 'Mahlangu'],
            ['firstname' => 'Jurie', 'lastname' => 'Botha'],
            ['firstname' => 'Jurie', 'lastname' => 'Gumede'],
            ['firstname' => 'Lethabo', 'lastname' => 'Botha'],
            ['firstname' => 'Lethabo', 'lastname' => 'Dlamini'],
            ['firstname' => 'Lethabo', 'lastname' => 'Mahlangu'],
            ['firstname' => 'Lethabo', 'lastname' => 'Nkosi'],
            ['firstname' => 'Lethabo', 'lastname' => 'Sithole'],
            ['firstname' => 'Lethokuhle', 'lastname' => 'Botha'],
            ['firstname' => 'Lethokuhle', 'lastname' => 'Geldenhuys'],
            ['firstname' => 'Lethokuhle', 'lastname' => 'Mkhize'],
            ['firstname' => 'Lethokuhle', 'lastname' => 'Stevens'],
            ['firstname' => 'Lisakhanya', 'lastname' => 'Du Toit'],
            ['firstname' => 'Lisakhanya', 'lastname' => 'Mkhize'],
            ['firstname' => 'Lubanz', 'lastname' => 'Ngcobo'],
            ['firstname' => 'Lubanz', 'lastname' => 'Van Der Merwe'],
            ['firstname' => 'Lubanzi', 'lastname' => 'Gumede'],
            ['firstname' => 'Lubanzi', 'lastname' => 'Mahlangu'],
            ['firstname' => 'Lwandle', 'lastname' => 'Sithole'],
            ['firstname' => 'Lwandle', 'lastname' => 'Smith'],
            ['firstname' => 'Melokuhle', 'lastname' => 'Botha'],
            ['firstname' => 'Melokuhle', 'lastname' => 'Khumalo'],
            ['firstname' => 'Melokuhle', 'lastname' => 'Mahlangu'],
            ['firstname' => 'Melokuhle', 'lastname' => 'Smith'],
            ['firstname' => 'Melokuhle', 'lastname' => 'Van Der Merwe'],
            ['firstname' => 'Nora', 'lastname' => 'Ndlovu'],
            ['firstname' => 'Ofentse', 'lastname' => 'Sithole'],
            ['firstname' => 'Ofentse', 'lastname' => 'Van Der Merwe'],
            ['firstname' => 'Prince', 'lastname' => 'Nkosi'],
            ['firstname' => 'Princess', 'lastname' => 'Nkosi'],
            ['firstname' => 'Siphosethu', 'lastname' => 'Ndlovu'],
        ];

        foreach ($learners as $learner) {
            Learner::query()->create([
                'firstname' => $learner['firstname'],
                'lastname' => $learner['lastname'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
