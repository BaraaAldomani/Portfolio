<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Laravel', 'PHP', 'Vue.js', 'Livewire', 'Tailwind CSS', 'MySQL', 'Redis', 'Docker', 'REST APIs', 'Flutter', 'Git', 'CI/CD'];

        foreach ($names as $index => $name) {
            Technology::updateOrCreate(['name' => $name], ['sort_order' => $index + 1]);
        }
    }
}
