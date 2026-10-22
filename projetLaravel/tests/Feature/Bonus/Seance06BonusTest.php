<?php

namespace Tests\Feature\Bonus;

use App\Models\Emprunt;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('bonus')]
class Seance06BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s6_m5_la_page_des_retards_liste_les_emprunts_en_retard(): void
    {
        $adherent = $this->adherent();
        foreach (['En retard' => -2, 'Dans les temps' => 10] as $titre => $jours) {
            Emprunt::create([
                'user_id' => $adherent->id,
                'livre_id' => Livre::factory()->create(['titre' => $titre])->id,
                'emprunte_le' => now()->subDays(20)->toDateString(),
                'retour_prevu_le' => now()->addDays($jours)->toDateString(),
            ]);
        }

        $this->actingAs($this->bibliothecaire())
            ->get(route('gestion.emprunts.retards'))
            ->assertOk()
            ->assertSee('En retard')
            ->assertDontSee('Dans les temps');
    }
}
