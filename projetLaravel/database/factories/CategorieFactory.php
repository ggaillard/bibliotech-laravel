<?php

namespace Database\Factories;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Fabrique de catégories pour les tests : Categorie::factory()->create()
 *
 * @extends Factory<Categorie>
 */
class CategorieFactory extends Factory
{
    public function definition(): array
    {
        $nom = ucfirst($this->faker->unique()->words(2, true));

        return [
            'nom' => $nom,
            'description' => $this->faker->sentence(),
            'slug' => Str::slug($nom),
            'couleur' => $this->faker->hexColor(),
            'icone' => 'fas fa-book',
            'active' => true,
        ];
    }

    /** Catégorie masquée : Categorie::factory()->inactive()->create() */
    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }
}
