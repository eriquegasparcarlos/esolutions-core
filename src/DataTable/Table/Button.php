<?php

namespace App\ESolutions\DataTable\Table;

use JsonSerializable;

/**
 * Clase para construir la configuración de un botón para tablas.
 */
class Button implements JsonSerializable
{
    /** @var string|null */
    protected $label;
    /** @var string|null */
    protected $icon;
    /** @var string|null */
    protected $action;
    /** @var string|null */
    protected $color;
    /** @var bool */
    protected $disable;
    /** @var string|null */
    protected $url;
    /** @var string */
    protected $size;
    /** @var string|null */
    protected $tooltip;

    /**
     * Constructor: inicializa los valores por defecto.
     */
    public function __construct()
    {
        $this->label = null;
        $this->icon = null;
        $this->action = null;
        $this->color = 'default';
        $this->disable = false;
        $this->url = null;
        $this->size = '14px';
        $this->tooltip = null;
    }

    /**
     * Crea una nueva instancia de Button.
     *
     * @return self
     */
    public static function make()
    {
        return new self();
    }

    /**
     * Define el texto (etiqueta) del botón.
     *
     * @param string|null $label
     * @return self
     */
    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Define el icono del botón (usa FontAwesome por defecto).
     *
     * @param string $icon
     * @return self
     */
    public function icon($icon)
    {
        $this->icon = 'fal fa-' . $icon;
        return $this;
    }

    /**
     * Define la acción que representa el botón (por ejemplo: new, edit, delete).
     *
     * @param string $action
     * @return self
     */
    public function action($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Define el color principal del botón (ejemplo: primary, red, green).
     *
     * @param string $color
     * @return self
     */
    public function color($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Establece si el botón estará deshabilitado.
     *
     * @param bool $disable
     * @return self
     */
    public function disable($disable)
    {
        $this->disable = $disable;
        return $this;
    }

    /**
     * Define la URL asociada al botón (puede ser null si no aplica).
     *
     * @param string|null $url
     * @return self
     */
    public function url($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Define el tamaño del botón (por defecto: 14px).
     *
     * @param string $size
     * @return self
     */
    public function size($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Define el texto del tooltip (ayuda) para el botón.
     *
     * @param string|null $tooltip
     * @return self
     */
    public function tooltip($tooltip)
    {
        $this->tooltip = $tooltip;
        return $this;
    }

    /**
     * Devuelve el botón como array asociativo (para APIs o frontend).
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'label' => $this->label,
            'icon' => $this->icon,
            'action' => $this->action,
            'color' => $this->color,
            'disable' => $this->disable,
            'url' => $this->url,
            'size' => $this->size,
            'tooltip' => $this->tooltip,
        ];
    }

    /**
     * Permite serializar directamente el botón a JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Botón preconfigurado para agregar nuevo (con parámetros opcionales).
     *
     * @param string|null $label
     * @param string|null $url
     * @param string|null $tooltip
     * @return self
     */
    public static function newButton($label = null, $url = null, $tooltip = 'Nuevo')
    {
        return self::make()
            ->label($label)
            ->icon('plus')
            ->action('new')
            ->color('primary')
            ->url($url)
            ->tooltip($tooltip);
    }

    /**
     * Botón preconfigurado para editar (con parámetros opcionales).
     *
     * @param string|null $label
     * @param string|null $url
     * @param string|null $tooltip
     * @return self
     */
    public static function editButton($label = 'Editar', $url = null, $tooltip = 'Editar')
    {
        return self::make()
            ->label($label)
            ->icon('pencil')
            ->action('edit')
            ->color('default')
            ->url($url)
            ->tooltip($tooltip);
    }

    /**
     * Botón preconfigurado para duplicar (con parámetros opcionales).
     *
     * @param string|null $label
     * @param string|null $url
     * @param string|null $tooltip
     * @return self
     */
    public static function duplicateButton($label = 'Duplicar', $url = null, $tooltip = 'Duplicar')
    {
        return self::make()
            ->label($label)
            ->icon('duplicate')
            ->action('duplicate')
            ->color('default')
            ->url($url)
            ->tooltip($tooltip);
    }

    /**
     * Botón preconfigurado para refrescar (con parámetros opcionales).
     *
     * @param string|null $url
     * @param string|null $tooltip
     * @return self
     */
    public static function refreshButton($url = null, $tooltip = '')
    {
        $tooltip = $tooltip === '' ? __('refresh') : $tooltip;
        return self::make()
            ->icon('arrows-rotate')
            ->action('refresh')
            ->color('primary')
            ->url($url)
            ->tooltip($tooltip);
    }

    /**
     * Botón preconfigurado para exportar (con parámetros opcionales).
     *
     * @param string|null $label
     * @param string|null $url
     * @param string|null $tooltip
     * @return self
     */
    public static function exportButton($label = 'Exportar', $url = null, $tooltip = 'Exportar')
    {
        return self::make()
            ->label($label)
            ->icon('download')
            ->action('export')
            ->color('primary')
            ->url($url)
            ->tooltip($tooltip);
    }

    /**
     * Botón preconfigurado para eliminar (con parámetros opcionales).
     *
     * @param string|null $label
     * @param string|null $url
     * @param string|null $tooltip
     * @return self
     */
    public static function deleteButton($label = 'Eliminar', $url = null, $tooltip = 'Eliminar')
    {
        return self::make()
            ->label($label)
            ->icon('xmark')
            ->action('delete')
            ->color('red')
            ->url($url)
            ->tooltip($tooltip);
    }

    /**
     * Botón que cambia el estado activo/inactivo según la fila (row).
     *
     * @param mixed $row
     * @param string|null $label
     * @param string|null $url
     * @param string|null $tooltip
     * @return self
     */
    public static function activeButton($row, $label = null, $url = null, $tooltip = 'Cambiar estado')
    {
        $isActive = is_array($row) ? ($row['is_active'] ?? false) : ($row->is_active ?? false);
        $icon = $isActive ? 'shield-xmark' : 'shield-check';
        $color = $isActive ? 'red' : 'green';
        $defaultTooltip = $isActive ? 'Desactivar' : 'Activar';

        return self::make()
            ->label($label)
            ->icon($icon)
            ->action('active')
            ->color($color)
            ->url($url)
            ->tooltip($tooltip !== null ? $tooltip : $defaultTooltip);
    }

    /**
     * @return array
     */
    public static function separator()
    {
        return ['type' => 'separator'];
    }
}
