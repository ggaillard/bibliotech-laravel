<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan db:seed  (ou  php artisan migrate:fresh --seed)
     * L'ordre compte : les livres ont besoin des catégories.
     */
    public function run(): void
    {
        $this->call([
            CategorieSeeder::class,
            LivreSeeder::class,
        ]);
    }
}
