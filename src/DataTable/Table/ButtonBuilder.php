<?php

namespace App\ESolutions\DataTable\Table;

use JsonSerializable;

/**
 * Builder para gestionar y agrupar botones que serán enviados al frontend.
 * Permite agregar botones sueltos y grupos de botones (por ejemplo, dropdowns o split buttons).
 */
class ButtonBuilder implements JsonSerializable
{
    /**
     * @var array $buttons Colección de botones o grupos de botones.
     */
    protected $buttons = [];

    /**
     * Agrega un botón individual a la colección.
     *
     * @param Button $button Instancia de Button.
     * @return $this
     */
    public function addButton(Button $button)
    {
        $this->buttons[] = $button;
        return $this;
    }

    /**
     * Agrega un grupo de botones como una sola opción en la colección.
     *
     * @param Button[] $buttons Array de instancias Button.
     * @param string|null $label Texto opcional para mostrar como título del grupo.
     * @param string|null $icon Icono opcional para el grupo.
     * @return $this
     */
    public function addButtonGroup(array $buttons, $label = null, $icon = 'fal fa-ellipsis-vertical')
    {
        // 1) eliminar nulls y valores falsy que no sean botones
        $buttons = array_values(array_filter($buttons, function ($b) {
            return $b !== null;
        }));

        if (empty($buttons)) {
            return $this;
        }

        $group = [
            'type' => 'group',
            'label' => $label,
            'icon' => $icon,
            'size' => '14px',
            'buttons' => array_map(function ($b) {
                return $b instanceof Button ? $b->toArray() : $b;
            }, $buttons),
        ];
        $this->buttons[] = $group;
        return $this;
    }

    /**
     * Agrega múltiples botones individuales de una vez.
     *
     * @param Button[] $buttons
     * @return $this
     */
    public function addButtons(array $buttons)
    {
        foreach ($buttons as $button) {
            if ($button instanceof Button) {
                $this->addButton($button);
            }
        }
        return $this;
    }

    /**
     * Devuelve la colección de botones lista para enviar al frontend.
     *
     * @return array
     */
    public function getButtons()
    {
        return array_map(function ($item) {
            if ($item instanceof Button) {
                return $item->toArray();
            }
            return $item;
        }, $this->buttons);
    }

    /**
     * Permite serializar el builder directamente a JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getButtons();
    }
}
