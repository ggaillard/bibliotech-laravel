<?php

namespace Tests\Feature\Bonus;

use App\Models\Auteur;
use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('bonus')]
class Seance02BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s2_m4_la_page_d_un_auteur_liste_ses_livres(): void
    {
        $asimov = Auteur::factory()->create(['prenom' => 'Isaac', 'nom' => 'Asimov']);
        Livre::factory()->create(['titre' => 'Fondation', 'auteur_id' => $asimov->id]);

        $this->get(route('auteurs.show', $asimov))
            ->assertOk()
            ->assertSee('Isaac Asimov')
            ->assertSee('Fondation');
    }
}
