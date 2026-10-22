<?php

namespace Tests\Feature\Seance08;

use App\Models\Livre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 8 — Qualité, sécurité, mise en production
 *
 *   php artisan test --group=seance-08
 */
#[Group('seance-08')]
class QualiteSecuriteTest extends TestCase
{
    use RefreshDatabase;

    // ── Mission 8.1 🟡 — Audit de sécurité : la faille XSS ────────

    public function test_m1_le_resume_d_un_livre_ne_peut_pas_injecter_de_script(): void
    {
        $livre = Livre::factory()->create([
            'resume' => '<script>alert("piraté")</script>',
        ]);

        $this->get(route('livres.show', $livre->id))
            ->assertOk()
            ->assertDontSee('<script>alert("piraté")</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    // ── Mission 8.2 🟢 — Vos propres tests ─────────────────────────

    public function test_m2_vous_avez_ecrit_au_moins_deux_tests(): void
    {
        $dossier = base_path('tests/Feature/Etudiant');
        $this->assertDirectoryExists($dossier, 'Créez le dossier tests/Feature/Etudiant et écrivez-y vos tests.');

        $nombre = 0;
        foreach (glob($dossier.'/*Test.php') as $fichier) {
            $nombre += preg_match_all('/public function test_\w+\s*\(/', file_get_contents($fichier));
        }

        $this->assertGreaterThanOrEqual(2, $nombre, 'Il faut au moins deux méthodes test_… dans tests/Feature/Etudiant.');
    }
}
