<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'department',
                'type' => 'text',
            ],
            [
                'name' => 'start_date',
                'type' => 'date',
            ],
            [
                'name' => 'end_date',
                'type' => 'date',
            ],
            [
                'name' => 'budget',
                'type' => 'number',
            ],

        ];

        // Create each attribute.
        foreach ($attributes as $data) {
            Attribute::create($data);
        }
    }
}
