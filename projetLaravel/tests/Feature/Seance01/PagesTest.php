<?php

namespace Tests\Feature\Seance01;

use App\Models\Categorie;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 1 — Routes, contrôleurs, vues
 *
 *   php artisan test --group=seance-01
 */
#[Group('seance-01')]
class PagesTest extends TestCase
{
    use RefreshDatabase;

    // ── Mission 1.2 🟢 — Page Contact ─────────────────────────────

    public function test_m2_la_page_contact_existe_et_porte_le_nom_contact(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Horaires');

        $this->assertSame(url('/contact'), route('contact'));
    }

    public function test_m2_le_menu_contient_un_lien_vers_contact(): void
    {
        $this->get('/')->assertSee(route('contact'), false);
    }

    // ── Mission 1.3 🟡 — Liste des catégories ─────────────────────

    public function test_m3_la_page_categories_liste_les_categories_actives(): void
    {
        $roman = Categorie::factory()->create(['nom' => 'Roman']);
        Categorie::factory()->inactive()->create(['nom' => 'Archives']);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Roman')
            ->assertDontSee('Archives');
    }

    public function test_m3_chaque_categorie_affiche_son_nombre_de_livres(): void
    {
        $policier = Categorie::factory()->create(['nom' => 'Policier']);
        Livre::factory()->count(2)->create(['categorie_id' => $policier->id]);

        $this->get(route('categories.index'))
            ->assertSee('2 livre(s)');
    }

    // ── Mission 1.4 🟡 — Livres d'une catégorie ───────────────────

    public function test_m4_la_page_d_une_categorie_liste_ses_livres_seulement(): void
    {
        $sf = Categorie::factory()->create(['nom' => 'Science-fiction', 'slug' => 'science-fiction']);
        $jeunesse = Categorie::factory()->create(['nom' => 'Jeunesse', 'slug' => 'jeunesse']);
        Livre::factory()->create(['titre' => 'Fondation', 'categorie_id' => $sf->id]);
        Livre::factory()->create(['titre' => 'Le Petit Prince', 'categorie_id' => $jeunesse->id]);

        $this->get(route('categories.show', 'science-fiction'))
            ->assertOk()
            ->assertSee('Science-fiction')
            ->assertSee('Fondation')
            ->assertDontSee('Le Petit Prince');

        $this->assertSame(url('/categories/science-fiction'), route('categories.show', 'science-fiction'));
    }

    public function test_m4_un_slug_inconnu_renvoie_404(): void
    {
        $this->assertTrue(Route::has('categories.show'), 'La route categories.show n\'existe pas.');
        $this->get('/categories/n-existe-pas')->assertNotFound();
    }
}
