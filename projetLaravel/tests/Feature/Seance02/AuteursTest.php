<?php

namespace Tests\Feature\Seance02;

use App\Models\Auteur;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 2 — Base de données : de la colonne « auteur » à la table « auteurs »
 *
 *   php artisan test --group=seance-02
 */
#[Group('seance-02')]
class AuteursTest extends TestCase
{
    use RefreshDatabase;

    // ── Mission 2.1 🟢 — Table et modèle Auteur ───────────────────

    public function test_m1_la_table_auteurs_a_les_bonnes_colonnes(): void
    {
        $this->assertTrue(Schema::hasTable('auteurs'), 'La table « auteurs » n\'existe pas.');
        $this->assertTrue(
            Schema::hasColumns('auteurs', ['id', 'nom', 'prenom', 'nationalite', 'date_naissance', 'biographie', 'created_at']),
            'Il manque une colonne dans « auteurs ».'
        );
    }

    public function test_m1_un_auteur_se_cree_et_donne_son_nom_complet(): void
    {
        $auteur = Auteur::create(['prenom' => 'Victor', 'nom' => 'Hugo', 'nationalite' => 'française']);

        $this->assertDatabaseHas('auteurs', ['nom' => 'Hugo']);
        $this->assertSame('Victor Hugo', $auteur->nom_complet);
    }

    public function test_m1_la_fabrique_d_auteurs_fonctionne(): void
    {
        $this->assertInstanceOf(Auteur::class, Auteur::factory()->create());
    }

    // ── Mission 2.2 🟢 — Relation Livre → Auteur ──────────────────

    public function test_m2_livres_reference_auteurs_et_l_ancienne_colonne_a_disparu(): void
    {
        $this->assertTrue(Schema::hasColumn('livres', 'auteur_id'), 'Il manque livres.auteur_id');
        $this->assertFalse(Schema::hasColumn('livres', 'auteur'), 'L\'ancienne colonne texte livres.auteur doit être supprimée.');
    }

    public function test_m2_un_livre_appartient_a_un_auteur(): void
    {
        $livre = Livre::factory()->create();

        $this->assertInstanceOf(Auteur::class, $livre->auteur);
    }

    public function test_m2_un_auteur_a_plusieurs_livres(): void
    {
        $tolkien = Auteur::factory()->create(['prenom' => 'J.R.R.', 'nom' => 'Tolkien']);
        Livre::factory()->count(2)->create(['auteur_id' => $tolkien->id]);

        $this->assertCount(2, $tolkien->livres);
    }

    public function test_m2_la_fiche_livre_affiche_le_nom_complet_de_l_auteur(): void
    {
        $camus = Auteur::factory()->create(['prenom' => 'Albert', 'nom' => 'Camus']);
        $livre = Livre::factory()->create(['titre' => 'L\'Étranger', 'auteur_id' => $camus->id]);

        $this->get(route('livres.show', $livre->id))
            ->assertOk()
            ->assertSee('Albert Camus');
    }

    // ── Mission 2.3 🟡 — Seeders et recherche par auteur ──────────

    public function test_m3_les_seeders_relient_les_livres_a_leurs_auteurs(): void
    {
        $this->seed();

        $hugo = Auteur::where('nom', 'Hugo')->where('prenom', 'Victor')->first();
        $this->assertNotNull($hugo, 'Victor Hugo doit exister dans la table auteurs après le seed.');
        $this->assertCount(2, $hugo->livres, 'Victor Hugo doit avoir 2 livres (Les Misérables, Notre-Dame de Paris).');
        $this->assertSame(1, Auteur::where('nom', 'Hugo')->count(), 'Un même auteur ne doit pas être créé deux fois.');
    }

    public function test_m3_la_recherche_trouve_un_livre_par_le_nom_de_son_auteur(): void
    {
        $zola = Auteur::factory()->create(['prenom' => 'Émile', 'nom' => 'Zola']);
        Livre::factory()->create(['titre' => 'Germinal', 'auteur_id' => $zola->id]);
        Livre::factory()->create(['titre' => 'Dune']);

        $this->get(route('livres.search', ['q' => 'Zola']))
            ->assertOk()
            ->assertSee('Germinal')
            ->assertDontSee('Dune');
    }
}
