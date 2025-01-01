<?php

namespace Database\Seeders;

use App\Models\ContactContent;
use Illuminate\Database\Seeder;

class ContactContentSeeder extends Seeder
{
    public function run()
    {
        ContactContent::create([
            'title' => 'Teléfono',
            'content' => '+52 4891074086',
            'type' => 'card',
            'map_url' => '',
            'styles' => 1
        ]);

        ContactContent::create([
            'title' => 'Correo',
            'content' => 'altasierracafexilitla@outlook.com  
                            cmaftasierra@gmail.com',
            'map_url' => '',
            'type' => 'card',
            'styles' => 1
        ]);

        ContactContent::create([
            'title' => 'Ubicación',
            'content' => 'Xilitla, San Luis Potosí',
            'map_url' => '',
            'type' => 'card',
            'styles' => 1
        ]);

        ContactContent::create([
            'title' => 'Nuestra Ubicación',
            'content' => 'El punto estratégico de acopio y talleres de manejo correcto del beneficiado y transformación de los cafés especiales se encontrarán ubicados en los municipios de Xilitla San Luis Potosí.',
            'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29757.97087164719!2d-98.99744!3d21.38341!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d6c7bfe563d6f5%3A0x50e54e2fd4bb0aa5!2sXilitla%2C%20San%20Luis%20Potosi!5e0!3m2!1sen!2smx!4v1640901234567!5m2!1sen!2smx',
            'type' => 'container',
            'styles' => 1
        ]);
    }
}