<?php

namespace Tests\Feature\Seance05;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * SÉANCE 5 — Authentification, rôles, sécurité de la connexion
 *
 *   php artisan test --group=seance-05
 */
#[Group('seance-05')]
class AuthentificationTest extends TestCase
{
    use RefreshDatabase;

    private function compte(string $role = 'adherent'): User
    {
        $user = User::factory()->create([
            'email' => $role.'@bibliotech.test',
            'password' => Hash::make('motdepasse-solide'),
        ]);
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    private function premiereErreur(string $champ): ?string
    {
        $erreurs = session('errors');

        return $erreurs instanceof ViewErrorBag
            ? $erreurs->first($champ)
            : ($erreurs['default']['messages'][$champ][0] ?? null);
    }

    // ── Mission 5.1 🟢 — Rôles, connexion, déconnexion ────────────

    public function test_m1_les_utilisateurs_ont_un_role_adherent_par_defaut(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'role'), 'Ajoutez la colonne users.role (migration).');
        $this->assertSame('adherent', User::factory()->create()->fresh()->role);
    }

    public function test_m1_la_page_de_connexion_s_affiche(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    public function test_m1_on_se_connecte_avec_de_bons_identifiants(): void
    {
        $user = $this->compte();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'motdepasse-solide'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_m1_un_mauvais_mot_de_passe_est_refuse(): void
    {
        $user = $this->compte();

        $this->from(route('login'))
            ->post(route('login'), ['email' => $user->email, 'password' => 'faux'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_m1_on_se_deconnecte(): void
    {
        $this->actingAs($this->compte())
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    // ── Mission 5.2 🟢 — Protéger l'espace de gestion ─────────────

    public function test_m2_un_visiteur_est_renvoye_vers_la_connexion(): void
    {
        $this->get('/gestion/livres')->assertRedirect(route('login'));
    }

    public function test_m2_un_adherent_n_a_pas_acces_a_la_gestion(): void
    {
        $this->actingAs($this->compte('adherent'))
            ->get('/gestion/livres')
            ->assertForbidden();
    }

    public function test_m2_bibliothecaire_et_admin_ont_acces_a_la_gestion(): void
    {
        $this->actingAs($this->compte('bibliothecaire'))->get('/gestion/livres')->assertOk();
        $this->actingAs($this->compte('admin'))->get('/gestion/categories')->assertOk();
    }

    public function test_m2_le_seeder_cree_un_compte_par_role(): void
    {
        $this->seed();

        foreach (['admin', 'bibliothecaire', 'adherent'] as $role) {
            $this->assertTrue(User::where('role', $role)->exists(), "Aucun compte « $role » après le seed.");
        }
    }

    // ── Mission 5.3 🟡 — Inscription d'un adhérent ────────────────

    public function test_m3_un_visiteur_peut_s_inscrire_comme_adherent(): void
    {
        $this->get(route('register'))->assertOk();

        $this->post(route('register'), [
            'name' => 'Léa Martin',
            'email' => 'lea@exemple.fr',
            'password' => 'un-mot-de-passe-long',
            'password_confirmation' => 'un-mot-de-passe-long',
        ])->assertRedirect('/');

        $lea = User::where('email', 'lea@exemple.fr')->first();
        $this->assertNotNull($lea);
        $this->assertSame('adherent', $lea->role);
        $this->assertNotSame('un-mot-de-passe-long', $lea->password, 'Le mot de passe doit être haché.');
        $this->assertTrue(Hash::check('un-mot-de-passe-long', $lea->password));
        $this->assertAuthenticatedAs($lea);
    }

    public function test_m3_l_inscription_refuse_un_mot_de_passe_court_ou_un_email_pris(): void
    {
        $this->compte();

        $this->post(route('register'), [
            'name' => 'X',
            'email' => 'adherent@bibliotech.test',
            'password' => 'court',
            'password_confirmation' => 'court',
        ])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_m3_on_ne_peut_pas_s_inscrire_bibliothecaire_en_trichant(): void
    {
        $this->post(route('register'), [
            'name' => 'Pirate',
            'email' => 'pirate@exemple.fr',
            'password' => 'un-mot-de-passe-long',
            'password_confirmation' => 'un-mot-de-passe-long',
            'role' => 'admin',
        ]);

        $this->assertSame('adherent', User::where('email', 'pirate@exemple.fr')->value('role'));
    }

    // ── Mission 5.4 🟡 — Durcir la connexion ──────────────────────

    public function test_m4_le_message_d_erreur_ne_revele_pas_si_le_compte_existe(): void
    {
        $user = $this->compte();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'faux']);
        $messageMauvaisMotDePasse = $this->premiereErreur('email');

        $this->post(route('login'), ['email' => 'inconnu@exemple.fr', 'password' => 'faux']);
        $messageCompteInconnu = $this->premiereErreur('email');

        $this->assertNotEmpty($messageMauvaisMotDePasse);
        $this->assertSame($messageMauvaisMotDePasse, $messageCompteInconnu,
            'Les deux messages doivent être identiques : sinon un attaquant sait quels emails ont un compte.');
    }

    public function test_m4_trop_de_tentatives_de_connexion_sont_bloquees(): void
    {
        $user = $this->compte();

        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'faux'.$i]);
        }

        $this->post(route('login'), ['email' => $user->email, 'password' => 'faux6'])
            ->assertStatus(429);
    }
}
