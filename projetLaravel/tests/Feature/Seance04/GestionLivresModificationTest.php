<?php

namespace Tests\Feature\Seance04;

use App\Models\Categorie;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 4 — Modifier, supprimer ; gérer les catégories en autonomie
 *
 *   php artisan test --group=seance-04
 */
#[Group('seance-04')]
class GestionLivresModificationTest extends TestCase
{
    use RefreshDatabase;

    private function donneesDe(Livre $livre, array $surcharge = []): array
    {
        return array_merge([
            'titre' => $livre->titre,
            'auteur_id' => $livre->auteur_id,
            'categorie_id' => $livre->categorie_id,
            'annee' => $livre->annee,
            'nb_pages' => $livre->nb_pages,
            'isbn' => $livre->isbn,
            'resume' => $livre->resume,
            'disponible' => 1,
        ], $surcharge);
    }

    // ── Mission 4.1 🟢 — Modifier un livre ─────────────────────────

    public function test_m1_le_formulaire_de_modification_est_pre_rempli(): void
    {
        $livre = Livre::factory()->create(['titre' => 'Candide']);

        $this->actingAs($this->bibliothecaire())
            ->get(route('gestion.livres.edit', $livre))
            ->assertOk()
            ->assertSee('value="Candide"', false)
            ->assertSee('name="_method" value="PUT"', false);
    }

    public function test_m1_la_modification_est_enregistree(): void
    {
        $livre = Livre::factory()->create(['titre' => 'Candid']);

        $this->actingAs($this->bibliothecaire())
            ->put(route('gestion.livres.update', $livre), $this->donneesDe($livre, ['titre' => 'Candide']))
            ->assertRedirect(route('gestion.livres.index'))
            ->assertSessionHas('success');

        $this->assertSame('Candide', $livre->fresh()->titre);
    }

    public function test_m1_on_peut_garder_son_propre_isbn_mais_pas_celui_d_un_autre(): void
    {
        $livre = Livre::factory()->create(['isbn' => '978-0-00-000001-1']);
        $autre = Livre::factory()->create(['isbn' => '978-0-00-000002-2']);

        $this->actingAs($this->bibliothecaire())
            ->put(route('gestion.livres.update', $livre), $this->donneesDe($livre))
            ->assertSessionHasNoErrors();

        $this->actingAs($this->bibliothecaire())
            ->put(route('gestion.livres.update', $livre), $this->donneesDe($livre, ['isbn' => $autre->isbn]))
            ->assertSessionHasErrors('isbn');
    }

    // ── Mission 4.2 🟢 — Supprimer un livre ────────────────────────

    public function test_m2_un_livre_peut_etre_supprime(): void
    {
        $livre = Livre::factory()->create();

        $this->actingAs($this->bibliothecaire())
            ->delete(route('gestion.livres.destroy', $livre))
            ->assertRedirect(route('gestion.livres.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($livre);
    }

    public function test_m2_la_liste_propose_un_formulaire_de_suppression_protege(): void
    {
        Livre::factory()->create();

        $this->actingAs($this->bibliothecaire())
            ->get(route('gestion.livres.index'))
            ->assertSee('name="_method" value="DELETE"', false)
            ->assertSee('name="_token"', false);
    }

    // ── Mission 4.3 🟡 — Les catégories, en autonomie ─────────────

    public function test_m3_on_peut_creer_une_categorie_et_son_slug_est_genere(): void
    {
        $this->actingAs($this->bibliothecaire())
            ->post(route('gestion.categories.store'), ['nom' => 'Bande dessinée', 'couleur' => '#FF8800'])
            ->assertRedirect(route('gestion.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', ['nom' => 'Bande dessinée', 'slug' => 'bande-dessinee']);
    }

    public function test_m3_on_peut_renommer_une_categorie(): void
    {
        $categorie = Categorie::factory()->create(['nom' => 'Polar']);

        $this->actingAs($this->bibliothecaire())
            ->put(route('gestion.categories.update', $categorie), ['nom' => 'Policier', 'couleur' => '#000000'])
            ->assertRedirect(route('gestion.categories.index'));

        $this->assertSame('Policier', $categorie->fresh()->nom);
    }

    public function test_m3_une_categorie_vide_peut_etre_supprimee(): void
    {
        $categorie = Categorie::factory()->create();

        $this->actingAs($this->bibliothecaire())
            ->delete(route('gestion.categories.destroy', $categorie))
            ->assertSessionHas('success');

        $this->assertModelMissing($categorie);
    }

    public function test_m3_une_categorie_qui_contient_des_livres_ne_peut_pas_etre_supprimee(): void
    {
        $categorie = Categorie::factory()->create();
        Livre::factory()->create(['categorie_id' => $categorie->id]);

        $this->actingAs($this->bibliothecaire())
            ->delete(route('gestion.categories.destroy', $categorie))
            ->assertRedirect(route('gestion.categories.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($categorie);
    }
}
