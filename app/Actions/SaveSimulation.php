<?php

namespace App\Actions;

use App\Models\Simulation;

class SaveSimulation
{
    public static function run(array $data): Simulation
    {
        return Simulation::create([
            'created_by' => $data['created_by'] ?? null,
            'form_type' => $data['form_type'],
            'municipios_estados_id' => $data['city_id'] ?? null,
            'name' => $data['customer']['name'],
            'email' => $data['customer']['email'],
            'phone' => $data['customer']['phone'],
            'document' => $data['customer']['document'],
            'answers' => $data['answers'],
        ]);
    }
}
