<?php

namespace App\ESolutions\DataTable\Table;

use JsonSerializable;

/**
 * Clase para construir una colección de filtros para la tabla.
 */
class FilterBuilder implements JsonSerializable
{
    /**
     * @var Filter[] $filters
     */
    protected $filters = [];

    /**
     * Agrega un filtro a la colección.
     *
     * @param Filter $filter
     * @return self
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Permite agregar varios filtros a la vez.
     *
     * @param Filter[] $filters
     * @return self
     */
    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            if ($filter instanceof Filter) {
                $this->addFilter($filter);
            }
        }
        return $this;
    }

    /**
     * Retorna la configuración de los filtros como array.
     *
     * @return array
     */
    public function getFilters()
    {
        return array_map(function ($filter) {
            return $filter->toArray();
        }, $this->filters);
    }

    /**
     * Para serialización directa a JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getFilters();
    }
}
