<?php

namespace App\ESolutions\DataTable\Table;

/**
 * Clase para construir una colección de columnas para la tabla.
 */
class ColumnBuilder implements \JsonSerializable
{
    /**
     * @var Column[] $columns Colección de columnas agregadas.
     */
    protected $columns = [];

    /**
     * Agrega una columna a la colección.
     *
     * @param Column $column
     * @return self
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * Permite agregar varias columnas a la vez.
     *
     * @param Column[] $columns
     * @return self
     */
    public function addColumns(array $columns)
    {
        foreach ($columns as $column) {
            if ($column instanceof Column) {
                $this->addColumn($column);
            }
        }
        return $this;
    }

    /**
     * Retorna la configuración de todas las columnas como array.
     *
     * @return array
     */
    public function getColumns()
    {
        return array_map(function ($column) {
            return $column->toArray();
        }, $this->columns);
    }

    /**
     * Permite serializar directamente la colección de columnas a JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getColumns();
    }
}
