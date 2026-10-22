<?php

namespace Tests\Feature\Seance06;

use App\Models\Emprunt;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 6 — Les emprunts : une association qui porte des données,
 * et des règles de gestion
 *
 *   php artisan test --group=seance-06
 */
#[Group('seance-06')]
class EmpruntsTest extends TestCase
{
    use RefreshDatabase;

    // ── Mission 6.1 🟢 — Table, modèle, relations ─────────────────

    public function test_m1_la_table_emprunts_a_les_bonnes_colonnes(): void
    {
        $this->assertTrue(Schema::hasColumns('emprunts', [
            'id', 'user_id', 'livre_id', 'emprunte_le', 'retour_prevu_le', 'rendu_le',
        ]));
    }

    public function test_m1_les_relations_fonctionnent_dans_les_deux_sens(): void
    {
        $adherent = $this->adherent();
        $livre = Livre::factory()->create();

        $emprunt = Emprunt::create([
            'user_id' => $adherent->id,
            'livre_id' => $livre->id,
            'emprunte_le' => now()->toDateString(),
            'retour_prevu_le' => now()->addDays(21)->toDateString(),
        ]);

        $this->assertTrue($emprunt->user->is($adherent));
        $this->assertTrue($emprunt->livre->is($livre));
        $this->assertCount(1, $adherent->emprunts);
        $this->assertCount(1, $livre->emprunts);
    }

    public function test_m1_le_scope_en_cours_ignore_les_emprunts_rendus(): void
    {
        $adherent = $this->adherent();
        foreach ([null, now()->toDateString()] as $rendu) {
            Emprunt::create([
                'user_id' => $adherent->id,
                'livre_id' => Livre::factory()->create()->id,
                'emprunte_le' => now()->subDays(5)->toDateString(),
                'retour_prevu_le' => now()->addDays(16)->toDateString(),
                'rendu_le' => $rendu,
            ]);
        }

        $this->assertSame(1, Emprunt::enCours()->count());
    }

    // ── Mission 6.2 🟢 — Emprunter un livre ───────────────────────

    public function test_m2_un_adherent_connecte_emprunte_un_livre_disponible(): void
    {
        $adherent = $this->adherent();
        $livre = Livre::factory()->create();

        $this->actingAs($adherent)
            ->post(route('livres.emprunter', $livre))
            ->assertRedirect()
            ->assertSessionHas('success');

        $emprunt = Emprunt::first();
        $this->assertNotNull($emprunt);
        $this->assertTrue($emprunt->user->is($adherent));
        $this->assertSame(now()->addDays(21)->toDateString(), $emprunt->retour_prevu_le->toDateString());
        $this->assertFalse($livre->fresh()->disponible);
    }

    public function test_m2_un_visiteur_doit_se_connecter_pour_emprunter(): void
    {
        $livre = Livre::factory()->create();

        $this->post(route('livres.emprunter', $livre))->assertRedirect(route('login'));
        $this->assertDatabaseCount('emprunts', 0);
    }

    // ── Mission 6.3 🟡 — Règles de gestion ────────────────────────

    public function test_m3_on_ne_peut_pas_emprunter_un_livre_deja_emprunte(): void
    {
        $livre = Livre::factory()->indisponible()->create();

        $this->actingAs($this->adherent())
            ->post(route('livres.emprunter', $livre))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('emprunts', 0);
    }

    public function test_m3_un_adherent_ne_peut_pas_avoir_plus_de_trois_emprunts_en_cours(): void
    {
        $adherent = $this->adherent();
        $livres = Livre::factory()->count(4)->create();

        foreach ($livres->take(3) as $livre) {
            $this->actingAs($adherent)->post(route('livres.emprunter', $livre));
        }

        $this->actingAs($adherent)
            ->post(route('livres.emprunter', $livres[3]))
            ->assertSessionHas('error');

        $this->assertSame(3, Emprunt::count());
        $this->assertTrue($livres[3]->fresh()->disponible);
    }

    // ── Mission 6.4 🟡 — Mes emprunts, retour d'un livre ──────────

    public function test_m4_mes_emprunts_n_affiche_que_les_miens(): void
    {
        $moi = $this->adherent();
        $voisin = $this->adherent();
        $this->actingAs($moi)->post(route('livres.emprunter', Livre::factory()->create(['titre' => 'Mon livre'])));
        $this->actingAs($voisin)->post(route('livres.emprunter', Livre::factory()->create(['titre' => 'Livre du voisin'])));

        $this->actingAs($moi)
            ->get(route('emprunts.mes'))
            ->assertOk()
            ->assertSee('Mon livre')
            ->assertDontSee('Livre du voisin');
    }

    public function test_m4_le_bibliothecaire_enregistre_le_retour(): void
    {
        $livre = Livre::factory()->create();
        $this->actingAs($this->adherent())->post(route('livres.emprunter', $livre));
        $emprunt = Emprunt::first();

        $this->actingAs($this->bibliothecaire())
            ->post(route('gestion.emprunts.retour', $emprunt))
            ->assertSessionHas('success');

        $this->assertNotNull($emprunt->fresh()->rendu_le);
        $this->assertTrue($livre->fresh()->disponible);
    }

    public function test_m4_un_adherent_ne_peut_pas_enregistrer_un_retour(): void
    {
        $adherent = $this->adherent();
        $livre = Livre::factory()->create();
        $this->actingAs($adherent)->post(route('livres.emprunter', $livre));

        $this->actingAs($adherent)
            ->post(route('gestion.emprunts.retour', Emprunt::first()))
            ->assertForbidden();
    }
}
