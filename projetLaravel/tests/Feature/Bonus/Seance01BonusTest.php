<?php

namespace Tests\Feature\Bonus;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Missions 🔴 (autonomes, facultatives) — php artisan test --group=bonus
 */
#[Group('bonus')]
class Seance01BonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_s1_m5_la_page_404_est_personnalisee(): void
    {
        $this->get('/une-page-qui-n-existe-pas')
            ->assertNotFound()
            ->assertSee('Page introuvable')
            ->assertSee(route('livres.index'), false);
    }
}
