<?php

namespace Tests\Feature\Seance07;

use App\Models\Categorie;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 7 — Une API REST pour le catalogue
 *
 *   php artisan test --group=seance-07
 */
#[Group('seance-07')]
class ApiLivresTest extends TestCase
{
    use RefreshDatabase;

    // ── Mission 7.1 🟢 — GET /api/livres ──────────────────────────

    public function test_m1_la_liste_est_renvoyee_en_json_pagine(): void
    {
        Livre::factory()->count(3)->create();

        $this->getJson('/api/livres')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'titre', 'auteur', 'categorie', 'annee', 'disponible']],
                'links',
                'meta' => ['current_page', 'total'],
            ]);
    }

    public function test_m1_la_ressource_ne_divulgue_pas_les_champs_internes(): void
    {
        Livre::factory()->create();

        $livre = $this->getJson('/api/livres')->json('data.0');

        $this->assertArrayNotHasKey('created_at', $livre);
        $this->assertArrayNotHasKey('categorie_id', $livre);
        $this->assertIsString($livre['auteur'], 'auteur doit être le nom complet (texte).');
    }

    // ── Mission 7.2 🟢 — GET /api/livres/{livre} ──────────────────

    public function test_m2_un_livre_est_renvoye_par_son_id(): void
    {
        $livre = Livre::factory()->create(['titre' => 'Fondation']);

        $this->getJson('/api/livres/'.$livre->id)
            ->assertOk()
            ->assertJsonPath('data.titre', 'Fondation');
    }

    public function test_m2_un_livre_inconnu_renvoie_404_en_json(): void
    {
        $this->assertSame(url('/api/livres/7'), route('api.livres.show', 7),
            'Nommez les routes de l\'API api.livres.* pour ne pas écraser les routes web livres.*');

        $this->getJson('/api/livres/999999')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    // ── Mission 7.3 🟡 — Filtres ───────────────────────────────────

    public function test_m3_on_filtre_par_categorie_et_disponibilite(): void
    {
        $sf = Categorie::factory()->create(['slug' => 'science-fiction']);
        Livre::factory()->create(['titre' => 'Dune', 'categorie_id' => $sf->id]);
        Livre::factory()->indisponible()->create(['titre' => '1984', 'categorie_id' => $sf->id]);
        Livre::factory()->create(['titre' => 'Germinal']);

        $this->getJson('/api/livres?categorie=science-fiction')
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/livres?categorie=science-fiction&disponible=1')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.titre', 'Dune');
    }

    // ── Mission 7.4 🟡 — POST protégé par jeton ───────────────────

    public function test_m4_sans_jeton_la_creation_est_refusee(): void
    {
        $this->postJson('/api/livres', ['titre' => 'X'])->assertUnauthorized();
    }

    public function test_m4_un_adherent_ne_peut_pas_creer_de_livre(): void
    {
        Sanctum::actingAs($this->adherent());

        $this->postJson('/api/livres', ['titre' => 'X'])->assertForbidden();
    }

    public function test_m4_un_bibliothecaire_cree_un_livre(): void
    {
        Sanctum::actingAs($this->bibliothecaire());
        $modele = Livre::factory()->make();

        $this->postJson('/api/livres', [
            'titre' => 'Vingt mille lieues sous les mers',
            'auteur_id' => $modele->auteur_id,
            'categorie_id' => $modele->categorie_id,
            'annee' => 1870,
            'isbn' => '978-2-253-00630-0',
        ])
            ->assertCreated()
            ->assertJsonPath('data.titre', 'Vingt mille lieues sous les mers');

        $this->assertDatabaseHas('livres', ['titre' => 'Vingt mille lieues sous les mers']);
    }

    public function test_m4_une_creation_invalide_renvoie_422(): void
    {
        Sanctum::actingAs($this->bibliothecaire());

        $this->postJson('/api/livres', ['titre' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['titre', 'auteur_id']);
    }
}
