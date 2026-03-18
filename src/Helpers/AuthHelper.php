<?php

namespace App\ESolutions\Helpers;

use Illuminate\Support\Facades\Hash;

class AuthHelper
{
    /**
     * Valida que la contraseña enviada coincida con la del usuario autenticado.
     *
     * @param string $password
     * @return bool
     */
    public static function checkPassword(string $password): bool
    {
        $user = auth()->user();
        return $user && Hash::check($password, $user->password);
    }
}
