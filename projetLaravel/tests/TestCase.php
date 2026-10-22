<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    /**
     * Crée un compte bibliothécaire.
     * Avant la séance 5, la colonne « role » n'existe pas encore : on crée
     * simplement un utilisateur. Les tests des séances 3 et 4 restent ainsi
     * valables avant ET après l'ajout des rôles.
     */
    protected function bibliothecaire(): User
    {
        return $this->utilisateurAvecRole('bibliothecaire');
    }

    protected function adherent(): User
    {
        return $this->utilisateurAvecRole('adherent');
    }

    protected function utilisateurAvecRole(string $role): User
    {
        $user = User::factory()->create();

        if (Schema::hasColumn('users', 'role')) {
            $user->forceFill(['role' => $role])->save();
        }

        return $user->refresh();
    }
}
