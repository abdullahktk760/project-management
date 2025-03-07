<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        Project::factory()->count(3)->create()->each(function ($project) use ($faker) {
            $attributes = Attribute::inRandomOrder()->take(4)->get();

            foreach ($attributes as $attribute) {
                switch ($attribute->name) {
                    case 'department':
                        $value = $faker->randomElement(['IT', 'Marketing', 'Development', 'HR']);
                        break;
                    case 'start_date':
                        $value = $faker->date();
                        break;
                    case 'end_date':
                        $value = $faker->date();
                        break;
                    case 'budget':
                        $value = $faker->randomElement([1234, 4567, 8765]);
                        break;
                    default:
                        $value = 'Sample Value';
                        break;
                }

                $project->attributeValues()->create([
                    'attribute_id' => $attribute->id,
                    'value'        => $value,
                ]);
            }
        });
    }
}
