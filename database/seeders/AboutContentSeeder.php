<?php

namespace Database\Seeders;

use App\Models\AboutContent;
use Illuminate\Database\Seeder;

class AboutContentSeeder extends Seeder
{
    public function run()
    {
        // Crear las cards
        $cards = [
            [
                'title' => 'Misión',
                'content' => 'Nuestra misión es...',
                'type' => AboutContent::TYPES['CARD'],
            ],
            [
                'title' => 'Visión',
                'content' => 'Nuestra visión es...',
                'type' => AboutContent::TYPES['CARD'],
            ],
            [
                'title' => 'Valores',
                'content' => 'Nuestros valores son...',
                'type' => AboutContent::TYPES['CARD'],
            ]
        ];

        foreach ($cards as $card) {
            AboutContent::create($card);
        }

        // Crear el container principal
        AboutContent::create([
            'title' => 'Acerca de Nosotros',
            'content' => 'Contenido principal sobre nuestra empresa...',
            'type' => AboutContent::TYPES['CONTAINER'],
        ]);
    }
}