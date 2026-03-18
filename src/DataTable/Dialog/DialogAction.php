<?php

namespace App\ESolutions\DataTable\Dialog;

use Illuminate\Database\Eloquent\Model;

class DialogAction
{
    /**
     * Genera información para la eliminación de un modelo.
     *
     * @param  string  $type  Singular del tipo de registro (ejemplo: 'el usuario', 'el cliente', 'la empresa')
     */
    public static function getDeleteRecordActionData(Model $record, string $nameField = 'name', string $type = 'registro', bool $verifyPassword = false): array
    {
        $name = $record->{$nameField};

        return [
            'title' => "Eliminar $type",
            'description' => "¿Esta usted seguro que desea eliminar $type <strong>$name</strong>? Esta acción no puede ser deshecho.",
            'button_label_submit' => __('delete'),
            'button_color' => 'red',
            'icon' => 'triangle-exclamation',
            'icon_color' => 'red',
            'verify_password' => $verifyPassword,
        ];
    }

    /**
     * Genera información para el cambio de estado activo/inactivo de un modelo.
     *
     * @param  string  $type  Singular del tipo de registro (ejemplo: 'usuario', 'cliente')
     */
    public static function getActiveRecordActionData(Model $record, string $nameField = 'name', string $type = 'registro', bool $verifyPassword = false): array
    {
        $isActive = (bool) $record->is_active;
        $name = $record->{$nameField};

        return [
            'title' => $isActive ? "Desactivar el $type" : "Activar el $type",
            'description' => $isActive
                ? "¿Esta usted seguro que desea desactivar al $type <strong>$name</strong>?"
                : "¿Esta usted seguro que desea activar al $type <strong>$name</strong>?",
            'button_label_submit' => $isActive ? __('deactivate') : __('activate'),
            'button_color' => $isActive ? 'red' : 'green',
            'icon' => $isActive ? 'shield-xmark' : 'shield-check',
            'icon_color' => $isActive ? 'red' : 'green',
            'verify_password' => $verifyPassword,
        ];
    }
}
