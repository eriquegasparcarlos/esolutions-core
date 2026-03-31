<?php

namespace App\ESolutions\DataTable\Table;

use JsonSerializable;

/**
 * Clase para definir columnas de una tabla que serán visualizadas en el frontend.
 */
class Column implements JsonSerializable
{
    /** @var string */
    public $name;
    /** @var string|null */
    public $label = null;
    /** @var string|null */
    public $align = 'left';
    /** @var string|null */
    public $width = null;
    /** @var bool */
    public $sortable = false;
    /** @var string|null Nombre real de la columna en BD para ORDER BY (si difiere de $name) */
    public $sort_field = null;
    /** @var bool */
    public $searchable = false;
    /** @var bool */
    public $locked = false;
    /** @var bool */
    public $visible = true;
    /** @var bool Solo aparece en exportaciones (Excel/PDF), no en la tabla del frontend */
    public $only_export = false;
    /** @var bool Indica si la columna es sumable en exportaciones */
    public $summable = false;

    /**
     * Constructor principal, solo requiere el nombre de la columna.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Método de factoría para crear una nueva columna.
     *
     * @param string $name
     * @return self
     */
    public static function make($name)
    {
        return new self($name);
    }

    /**
     * Define el label (cabecera) de la columna.
     *
     * @param string $label
     * @return self
     */
    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Define la alineación ('left', 'right', 'center') de la columna.
     *
     * @param string $align
     * @return self
     */
    public function align($align)
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Atajo para alinear a la derecha.
     *
     * @return self
     */
    public function alignRight()
    {
        return $this->align('right');
    }

    /**
     * Atajo para alinear al centro.
     *
     * @return self
     */
    public function alignCenter()
    {
        return $this->align('center');
    }

    /**
     * Define el ancho de la columna (ej: '120px', '10%').
     *
     * @param string $width
     * @return self
     */
    public function width($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Indica si la columna es ordenable.
     *
     * @param bool $sortable
     * @return self
     */
    public function sortable($sortable = true)
    {
        $this->sortable = $sortable;
        return $this;
    }

    /**
     * Define el nombre real de la columna en BD para ORDER BY.
     * Usar cuando el nombre de la columna en el frontend difiere del de la BD.
     *
     * @param string $field
     * @return self
     */
    public function sortField($field)
    {
        $this->sort_field = $field;
        return $this;
    }

    /**
     * Indica si la columna es buscable.
     *
     * @param bool $searchable
     * @return self
     */
    public function searchable($searchable = true)
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * Indica si la columna es visible por defecto.
     *
     * @param bool $visible
     * @return self
     */
    public function visible($visible = true)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Marca la columna como solo-exportación (no se muestra en la tabla del frontend).
     *
     * @param bool $onlyExport
     * @return self
     */
    public function onlyExport($onlyExport = true)
    {
        $this->only_export = $onlyExport;
        return $this;
    }

    /**
     * Marca la columna como sumable (se incluye en fila de totales al exportar).
     *
     * @param bool $summable
     * @return self
     */
    public function summable($summable = true)
    {
        $this->summable = $summable;
        return $this;
    }

    /**
     * Indica si la columna está "bloqueada" (ej: no desplazable).
     *
     * @param bool $locked
     * @return self
     */
    public function locked($locked = true)
    {
        $this->locked = $locked;
        return $this;
    }

    /**
     * Convierte la columna a un array asociativo para el frontend.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'align' => $this->align,
            'width' => $this->width,
            'sortable' => $this->sortable,
            'sort_field' => $this->sort_field,
            'searchable' => $this->searchable,
            'locked' => $this->locked,
            'visible' => $this->visible,
            'only_export' => $this->only_export,
            'summable' => $this->summable,
        ];
    }

    /**
     * Permite serializar la columna directamente a JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Columna predefinida para marcar si es default.
     *
     * @return self
     */
    public static function isDefault()
    {
        return self::make('is_default_text')
            ->label(__('¿is default?'))
            ->alignCenter()
            ->width('120px')
            ->locked()
            ->sortable(false);
    }

    /**
     * Columna predefinida para estado activo/inactivo.
     *
     * @return self
     */
    public static function isActive()
    {
        return self::make('is_active')
            ->label(__('¿is active?'))
            ->alignCenter()
            ->width('120px')
            ->locked()
            ->sortable(false);
    }

    /**
     * Columna predefinida para acciones.
     *
     * @return self
     */
    public static function actions()
    {
        return self::make('actions')
            ->label(__('actions'))
            ->alignRight()
            ->width('180px')
            ->locked()
            ->sortable(false);
    }
}
