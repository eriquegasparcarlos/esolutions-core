<?php

if (!function_exists('funcValidateRuc')) {
    /**
     * Valida el RUC peruano (11 dígitos, con verificador).
     */
    function funcValidateRuc(string $ruc): array
    {
        $ruc = trim($ruc);
        if (strlen($ruc) !== 11) {
            return [
                'success' => false,
                'message' => 'El RUC es incorrecto'
            ];
        }

        $patron = "/^[[:digit:]]+$/";
        if (!preg_match($patron, $ruc)) {
            return [
                'success' => false,
                'message' => 'El RUC es incorrecto'
            ];
        }

        if ($ruc) {
            $suma = 0;
            $x = 6;
            for ($i = 0; $i < strlen($ruc) - 1; $i++) {
                if ($i == 4) {
                    $x = 8;
                }
                $digito = $ruc[$i];
                $x--;
                if ($i == 0) {
                    $suma += ($digito * $x);
                } else {
                    $suma += ($digito * $x);
                }
            }
            $resto = $suma % 11;
            $resto = 11 - $resto;
            if ($resto >= 10) {
                $resto = $resto - 10;
            }
            if ($resto == $ruc[strlen($ruc) - 1]) {
                return [
                    'success' => true,
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'El RUC es incorrecto'
        ];
    }
}

if (!function_exists('funcValidateDni')) {
    /**
     * Valida si el formato del DNI es correcto.
     *
     * Acepta los siguientes formatos:
     *  - 8 dígitos numéricos (ej: "12345678")
     *  - 8 dígitos seguidos de guion o espacio y 4 dígitos más (ej: "12345678-1234" o "12345678 1234")
     *
     * @param string $dni DNI a validar.
     * @return array
     */
    function funcValidateDni(string $dni): array
    {
        // Expresión regular:
        // ^\d{8}           => 8 dígitos al inicio
        // (?:[-\s]\d{4})?  => Opcional: guion o espacio seguido de 4 dígitos
        // $                => Fin de la cadena
        if (!preg_match('/^\d{8}(?:[-\s]\d{4})?$/', $dni)) {
            return [
                'success' => false,
                'message' => 'El DNI es incorrecto'
            ];
        }

        return [
            'success' => true,
        ];
    }
}
