<?php

namespace App\ESolutions\DataTable\Table;

use JsonSerializable;

/**
 * Clase para definir columnas de una tabla que serán visualizadas en el frontend.
 */
class Column implements JsonSerializable
{
    public string $name;

    public ?string $label = null;

    public ?string $align = 'left';

    public ?string $width = null;

    public bool $sortable = false;

    public bool $searchable = false;

    public bool $locked = false;

    /**
     * Constructor principal, solo requiere el nombre de la columna.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Método de factoría para crear una nueva columna.
     */
    public static function make(string $name): self
    {
        return new self($name);
    }

    /**
     * Define el label (cabecera) de la columna.
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Define la alineación ('left', 'right', 'center') de la columna.
     */
    public function align(string $align): self
    {
        $this->align = $align;

        return $this;
    }

    /**
     * Atajo para alinear a la derecha.
     */
    public function alignRight(): self
    {
        return $this->align('right');
    }

    /**
     * Atajo para alinear al centro.
     */
    public function alignCenter(): self
    {
        return $this->align('center');
    }

    /**
     * Define el ancho de la columna (ej: '120px', '10%').
     */
    public function width(string $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Indica si la columna es ordenable.
     */
    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * Indica si la columna es buscable.
     */
    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * Indica si la columna está "bloqueada" (ej: no desplazable).
     */
    public function locked(bool $locked = true): self
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Convierte la columna a un array asociativo para el frontend.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'align' => $this->align,
            'width' => $this->width,
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'locked' => $this->locked,
        ];
    }

    /**
     * Permite serializar la columna directamente a JSON.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Columna predefinida para marcar si es default.
     */
    public static function isDefault(): self
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
     */
    public static function isActive(): self
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
     */
    public static function actions(): self
    {
        return self::make('actions')
            ->label(__('actions'))
            ->alignRight()
            ->width('180px')
            ->locked()
            ->sortable(false);
    }
}
