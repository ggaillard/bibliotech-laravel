<?php

namespace Tests\Feature\Bonus;

use App\Models\Categorie;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('bonus')]
class Seance04BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s4_m4_la_liste_de_gestion_se_filtre_et_garde_ses_filtres_en_pagination(): void
    {
        $sf = Categorie::factory()->create(['slug' => 'sf']);
        Livre::factory()->count(16)->create(['categorie_id' => $sf->id]);
        Livre::factory()->create(['titre' => 'Hors filtre']);

        $this->actingAs($this->bibliothecaire())
            ->get(route('gestion.livres.index', ['categorie' => $sf->id]))
            ->assertOk()
            ->assertDontSee('Hors filtre')
            ->assertSee('categorie='.$sf->id.'&amp;page=2', false);
    }
}
