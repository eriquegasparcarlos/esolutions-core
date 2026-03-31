<?php

namespace App\ESolutions\Helpers;

class NumberToLetterHelper
{
    /** @var array */
    private static $UNIDADES = [
        '',
        'un ',
        'dos ',
        'tres ',
        'cuatro ',
        'cinco ',
        'seis ',
        'siete ',
        'ocho ',
        'nueve ',
        'diez ',
        'once ',
        'doce ',
        'trece ',
        'catorce ',
        'quince ',
        'dieciseis ',
        'diecisiete ',
        'dieciocho ',
        'diecinueve ',
        'veinte ',
    ];

    /** @var array */
    private static $DECENAS = [
        'veinti',
        'treinta ',
        'cuarenta ',
        'cincuenta ',
        'sesenta ',
        'setenta ',
        'ochenta ',
        'noventa ',
    ];

    /** @var array */
    private static $CENTENAS = [
        'ciento ',
        'doscientos ',
        'trescientos ',
        'cuatrocientos ',
        'quinientos ',
        'seiscientos ',
        'setecientos ',
        'ochocientos ',
        'novecientos ',
    ];

    /**
     * @param mixed $number
     * @param string $currency
     * @param bool $format
     * @return string
     */
    public static function convertToLetter($number, $currency = '', $format = false)
    {
        $normalized = self::normalizeNumber($number);
        if ($normalized === null) {
            return 'No es posible convertir el numero en letras';
        }

        // Aseguramos 2 decimales exactos
        $normalized = sprintf('%.2f', $normalized);
        $parts = explode('.', $normalized);
        $intStr = $parts[0];
        $decStr = $parts[1];

        $base_number = (int)$intStr;
        $decNumberStr = $decStr;

        if (($base_number < 0) || ($base_number > 999999999)) {
            return 'No es posible convertir el numero en letras';
        }

        $converted = '';

        $numberStrFill = str_pad((string)$base_number, 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles    = substr($numberStrFill, 3, 3);
        $cientos  = substr($numberStrFill, 6);

        if ((int)$millones > 0) {
            if ($millones === '001') {
                $converted .= 'un millon ';
            } else {
                $converted .= sprintf('%smillones ', self::convertGroup($millones));
            }
        }

        if ((int)$miles > 0) {
            if ($miles === '001') {
                $converted .= 'mil ';
            } else {
                $converted .= sprintf('%smil ', self::convertGroup($miles));
            }
        }

        if ((int)$cientos > 0) {
            if ($cientos === '001') {
                $converted .= ((int)$millones > 0 || (int)$miles > 0) ? 'uno ' : 'un ';
            } else {
                $converted .= sprintf('%s', self::convertGroup($cientos));
            }
        }

        if ($base_number === 0) {
            $converted = 'Cero ';
        }

        // Limpieza de espacios dobles
        $converted = preg_replace('/\s+/', ' ', trim($converted)) . ' ';

        if ($format) {
            $valor_convertido = number_format((float)$normalized, 2, ',', '.') .
                ' (' . ucfirst($converted) . $decNumberStr . '/100 ' . $currency . ')';
        } else {
            $valor_convertido = ucfirst($converted) . 'con ' . $decNumberStr . '/100 ' . $currency;
        }

        return trim($valor_convertido);
    }

    /**
     * @param string $n
     * @return string
     */
    private static function convertGroup($n)
    {
        $output = '';

        if ($n === '100') {
            return 'cien ';
        }

        if ($n[0] !== '0') {
            $output = self::$CENTENAS[((int)$n[0]) - 1];
        }

        $k = (int)substr($n, 1);

        if ($k <= 20) {
            $output .= self::$UNIDADES[$k];
            return $output;
        }

        // 21..99
        $tens = (int)$n[1];
        $unit = (int)$n[2];

        $decena = self::$DECENAS[$tens - 2];

        if ($k > 30 && $unit !== 0) {
            $output .= $decena . 'y ' . self::$UNIDADES[$unit];
        } else {
            $output .= $decena . self::$UNIDADES[$unit];
        }

        return $output;
    }

    /**
     * Normaliza entradas tipo:
     * - 1234.56
     * - "1,234.56"
     * - "1.234,56"
     * - "1234,56"
     *
     * @param mixed $value
     * @return float|null
     */
    private static function normalizeNumber($value)
    {
        if (is_int($value) || is_float($value)) {
            return (float)$value;
        }

        if (!is_string($value)) {
            return null;
        }

        $v = trim($value);
        if ($v === '') return null;

        // Quitar espacios
        $v = str_replace(' ', '', $v);

        $hasDot = strpos($v, '.') !== false;
        $hasComma = strpos($v, ',') !== false;

        // Caso "1.234,56" => "." miles, "," decimal
        if ($hasDot && $hasComma) {
            $lastDot = strrpos($v, '.');
            $lastComma = strrpos($v, ',');

            if ($lastComma > $lastDot) {
                $v = str_replace('.', '', $v);
                $v = str_replace(',', '.', $v);
            } else {
                $v = str_replace(',', '', $v);
            }
        } elseif ($hasComma && !$hasDot) {
            $v = str_replace(',', '.', $v);
        }

        if (!is_numeric($v)) return null;

        return (float)$v;
    }
}
