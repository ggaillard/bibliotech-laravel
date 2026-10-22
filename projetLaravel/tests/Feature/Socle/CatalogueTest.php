<?php

namespace Tests\Feature\Socle;

use App\Models\Categorie;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SOCLE — ce qui marche dès le premier jour et doit RESTER vert
 * à chaque séance. Si un de ces tests passe au rouge, vous avez cassé
 * quelque chose qui fonctionnait : c'est une régression.
 *
 *   php artisan test --group=socle
 */
#[Group('socle')]
class CatalogueTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_d_accueil_s_affiche(): void
    {
        $this->get('/')->assertOk()->assertSee('BiblioTech');
    }

    public function test_le_catalogue_liste_les_livres(): void
    {
        $livre = Livre::factory()->create(['titre' => 'Germinal']);

        $this->assertSame(url('/livres'), route('livres.index'), 'La route livres.index a été écrasée.');

        $this->get(route('livres.index'))
            ->assertOk()
            ->assertSee('Germinal');
    }

    public function test_la_fiche_d_un_livre_s_affiche(): void
    {
        $livre = Livre::factory()->create(['titre' => 'Le Petit Prince']);

        $this->assertSame(url('/livre/'.$livre->id), route('livres.show', $livre->id), 'La route livres.show a été écrasée.');

        $this->get(route('livres.show', $livre->id))
            ->assertOk()
            ->assertSee('Le Petit Prince');
    }

    public function test_un_livre_inexistant_renvoie_404(): void
    {
        $this->get('/livre/99999')->assertNotFound();
    }

    public function test_la_recherche_trouve_un_livre_par_son_titre(): void
    {
        Livre::factory()->create(['titre' => 'Dune']);
        Livre::factory()->create(['titre' => 'Fondation']);

        $this->get(route('livres.search', ['q' => 'Dune']))
            ->assertOk()
            ->assertSee('Dune')
            ->assertDontSee('Fondation');
    }

    public function test_la_demo_des_parametres_de_route_fonctionne(): void
    {
        $this->get('/demo/hello/Lea')->assertOk()->assertSee('Bonjour Lea');
        $this->get('/demo/hello')->assertOk()->assertSee('Bonjour Étudiant');
    }

    public function test_la_page_a_propos_s_affiche(): void
    {
        $this->get(route('about'))->assertOk();
    }

    public function test_les_seeders_remplissent_la_base(): void
    {
        $this->seed();

        $this->assertGreaterThanOrEqual(8, Categorie::count());
        $this->assertGreaterThanOrEqual(20, Livre::count());
    }
}
