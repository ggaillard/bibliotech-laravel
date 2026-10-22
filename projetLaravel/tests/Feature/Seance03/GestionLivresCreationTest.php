<?php

namespace Tests\Feature\Seance03;

use App\Http\Requests\LivreRequest;
use App\Models\Auteur;
use App\Models\Categorie;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 3 — Espace de gestion : lister, consulter, ajouter un livre
 *
 * Toutes les URL commencent par /gestion/livres et les routes s'appellent
 * gestion.livres.index, gestion.livres.create, gestion.livres.store, …
 *
 *   php artisan test --group=seance-03
 */
#[Group('seance-03')]
class GestionLivresCreationTest extends TestCase
{
    use RefreshDatabase;

    private function donneesValides(array $surcharge = []): array
    {
        return array_merge([
            'titre' => 'Le Rouge et le Noir',
            'auteur_id' => Auteur::factory()->create()->id,
            'categorie_id' => Categorie::factory()->create()->id,
            'annee' => 1830,
            'nb_pages' => 576,
            'isbn' => '978-2-07-041311-9',
            'resume' => 'Julien Sorel veut s\'élever.',
            'disponible' => 1,
        ], $surcharge);
    }

    // ── Mission 3.1 🟢 — Contrôleur resource et liste paginée ─────

    public function test_m1_la_liste_de_gestion_affiche_les_livres(): void
    {
        Livre::factory()->create(['titre' => 'Germinal']);

        $this->actingAs($this->bibliothecaire())
            ->get('/gestion/livres')
            ->assertOk()
            ->assertSee('Germinal');

        $this->assertSame(url('/gestion/livres'), route('gestion.livres.index'));
    }

    public function test_m1_la_liste_est_paginee_par_15(): void
    {
        Livre::factory()->count(20)->create();

        $this->actingAs($this->bibliothecaire())
            ->get(route('gestion.livres.index'))
            ->assertViewHas('livres', fn ($livres) => $livres instanceof LengthAwarePaginator
                && $livres->perPage() === 15
                && $livres->total() === 20);
    }

    // ── Mission 3.2 🟢 — Formulaire d'ajout et validation ─────────

    public function test_m2_le_formulaire_d_ajout_s_affiche_avec_ses_champs(): void
    {
        $this->actingAs($this->bibliothecaire())
            ->get(route('gestion.livres.create'))
            ->assertOk()
            ->assertSee('name="titre"', false)
            ->assertSee('name="auteur_id"', false)
            ->assertSee('name="categorie_id"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_m2_un_livre_valide_est_enregistre_avec_un_message(): void
    {
        $this->actingAs($this->bibliothecaire())
            ->post(route('gestion.livres.store'), $this->donneesValides())
            ->assertRedirect(route('gestion.livres.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('livres', ['titre' => 'Le Rouge et le Noir', 'isbn' => '978-2-07-041311-9']);
    }

    public function test_m2_les_donnees_invalides_sont_refusees(): void
    {
        $this->actingAs($this->bibliothecaire())
            ->from(route('gestion.livres.create'))
            ->post(route('gestion.livres.store'), $this->donneesValides([
                'titre' => '',
                'annee' => 3000,
                'auteur_id' => 999999,
            ]))
            ->assertRedirect(route('gestion.livres.create'))
            ->assertSessionHasErrors(['titre', 'annee', 'auteur_id']);

        $this->assertDatabaseCount('livres', 0);
    }

    public function test_m2_l_isbn_doit_etre_unique(): void
    {
        Livre::factory()->create(['isbn' => '978-2-07-041311-9']);

        $this->actingAs($this->bibliothecaire())
            ->post(route('gestion.livres.store'), $this->donneesValides())
            ->assertSessionHasErrors('isbn');
    }

    // ── Mission 3.3 🟡 — Fiche de gestion et Form Request ─────────

    public function test_m3_la_fiche_de_gestion_utilise_la_liaison_de_modele(): void
    {
        $livre = Livre::factory()->create(['titre' => 'Bel-Ami']);

        $this->actingAs($this->bibliothecaire())
            ->get(route('gestion.livres.show', $livre))
            ->assertOk()
            ->assertSee('Bel-Ami')
            ->assertSee($livre->auteur->nom_complet);

        $this->actingAs($this->bibliothecaire())
            ->get('/gestion/livres/999999')
            ->assertNotFound();
    }

    public function test_m3_la_validation_est_dans_une_form_request(): void
    {
        $this->assertTrue(
            class_exists(LivreRequest::class),
            'Créez la classe App\Http\Requests\LivreRequest (php artisan make:request LivreRequest).'
        );

        $regles = (new LivreRequest)->rules();
        foreach (['titre', 'auteur_id', 'categorie_id', 'annee', 'isbn'] as $champ) {
            $this->assertArrayHasKey($champ, $regles, "LivreRequest::rules() doit valider « $champ ».");
        }
    }
}
