<?php

namespace App\ESolutions\DataTable\Traits;

use Illuminate\Http\Request;
use Modules\Configuration\Models\ConfigurationDataTable;

/**
 * Trait para estandarizar el endpoint filter() de los controllers de reportes.
 *
 * Los controllers que usen este trait deben implementar:
 * - getReportFilters(): array de Filter
 * - getReportColumns(): array de Column
 *
 * Opcionalmente pueden sobreescribir:
 * - getReportTableName(): string — identificador para persistir preferencias de columnas
 *
 * Los métodos records(), excel(), pdf() quedan en cada controller
 * porque su lógica de query es específica.
 */
trait ReportDataTableTrait
{
    /**
     * Retorna filtros y columnas en formato estándar para XReportTable.
     *
     * @return array
     */
    public function filter()
    {
        $columns = $this->getReportColumns();
        $tableName = $this->getReportTableName();
        $visibleColumns = $this->getReportVisibleColumns($columns, $tableName);

        $sortPrefs = $this->getReportSortPreferences($tableName);

        return [
            'filters' => $this->getReportFilters(),
            'columns' => $columns,
            'table_name' => $tableName,
            'visible_columns' => $visibleColumns,
            'sort_by' => $sortPrefs['sort_by'],
            'descending' => $sortPrefs['descending'],
        ];
    }

    /**
     * Persiste las columnas visibles seleccionadas por el usuario.
     *
     * @param Request $request
     * @return array
     */
    public function updateVisibleColumns(Request $request)
    {
        $tableName = $request->input('table_name', $this->getReportTableName());
        $visibleColumns = $request->input('visible_columns', []);

        if (is_array($visibleColumns)) {
            $visibleColumns = array_values($visibleColumns);
        }

        $record = ConfigurationDataTable::query()
            ->where('user_id', auth()->id())
            ->where('table', $tableName)
            ->first();

        if ($record) {
            $record->visible_columns = $visibleColumns;
            $record->save();
        } else {
            $config = new ConfigurationDataTable();
            $config->user_id = auth()->id();
            $config->table = $tableName;
            $config->visible_columns = $visibleColumns;
            $config->records_per_page = 20;
            $config->sort_by = 'id';
            $config->descending = true;
            $config->save();
        }

        return [
            'success' => true,
            'message' => 'Actualización satisfactoria',
        ];
    }

    /**
     * Persiste las preferencias de ordenamiento del usuario.
     *
     * @param Request $request
     * @return array
     */
    public function updateSortPreferences(Request $request)
    {
        $tableName = $request->input('table_name', $this->getReportTableName());
        $sortBy = $request->input('sort_by', 'id');
        $descending = filter_var($request->input('descending', true), FILTER_VALIDATE_BOOLEAN);

        $record = ConfigurationDataTable::query()
            ->where('user_id', auth()->id())
            ->where('table', $tableName)
            ->first();

        if ($record) {
            $record->sort_by = $sortBy;
            $record->descending = $descending;
            $record->save();
        } else {
            $config = new ConfigurationDataTable();
            $config->user_id = auth()->id();
            $config->table = $tableName;
            $config->visible_columns = [];
            $config->records_per_page = 20;
            $config->sort_by = $sortBy;
            $config->descending = $descending;
            $config->save();
        }

        return ['success' => true];
    }

    /**
     * Carga las preferencias de ordenamiento del usuario.
     *
     * @param string $tableName
     * @return array
     */
    protected function getReportSortPreferences($tableName)
    {
        $record = ConfigurationDataTable::query()
            ->where('user_id', auth()->id())
            ->where('table', $tableName)
            ->first();

        if ($record && $record->sort_by) {
            return [
                'sort_by' => $record->sort_by,
                'descending' => (bool) $record->descending,
            ];
        }

        return $this->getDefaultSort();
    }

    /**
     * Sort por defecto cuando el usuario no tiene preferencia guardada.
     * Los controllers pueden sobreescribir esto.
     *
     * @return array
     */
    protected function getDefaultSort()
    {
        return [
            'sort_by' => null,
            'descending' => false,
        ];
    }

    /**
     * Carga las columnas visibles del usuario o extrae los defaults.
     *
     * @param array $columns
     * @param string $tableName
     * @return array
     */
    protected function getReportVisibleColumns($columns, $tableName)
    {
        $record = ConfigurationDataTable::query()
            ->where('user_id', auth()->id())
            ->where('table', $tableName)
            ->first();

        if ($record && !empty($record->visible_columns)) {
            return $record->visible_columns;
        }

        // Default: todas las columnas con visible !== false
        return collect($columns)->filter(function ($col) {
            $arr = $col instanceof \JsonSerializable ? $col->jsonSerialize() : (array) $col;
            return !isset($arr['visible']) || $arr['visible'] !== false;
        })->pluck('name')->values()->all();
    }

    /**
     * Retorna las columnas filtradas según la visibilidad del usuario.
     * Útil para exportaciones (Excel, PDF).
     *
     * @param Request|null $request Si viene select_columns del frontend, las usa.
     * @return array [['name' => ..., 'label' => ...], ...]
     */
    protected function getExportColumns(Request $request = null)
    {
        $allColumns = $this->getReportColumns();
        $tableName = $this->getReportTableName();

        // Prioridad: params del request > preferencias guardadas > default (todas)
        $selectColumns = $request ? $request->input('select_columns') : null;
        if (!empty($selectColumns) && is_array($selectColumns)) {
            $visible = $selectColumns;
        } else {
            $visible = $this->getReportVisibleColumns($allColumns, $tableName);
        }

        return collect($allColumns)->filter(function ($col) use ($visible) {
            $arr = $col instanceof \JsonSerializable ? $col->jsonSerialize() : (array) $col;
            // Las columnas only_export siempre se incluyen en la exportación
            if (!empty($arr['only_export'])) {
                return true;
            }
            return in_array($arr['name'], $visible);
        })->values()->all();
    }

    /**
     * Filtra un array de datos (fila) para solo incluir las columnas exportables.
     *
     * @param array $row Fila completa de datos (name => value)
     * @param array $exportColumns Columnas filtradas de getExportColumns()
     * @return array Fila filtrada en orden de columnas
     */
    protected function filterRowByColumns(array $row, array $exportColumns)
    {
        $filtered = [];
        foreach ($exportColumns as $col) {
            $name = $col instanceof \JsonSerializable ? $col->jsonSerialize()['name'] : $col['name'];
            $value = isset($row[$name]) ? $row[$name] : '';
            // Extraer valor plano de celdas complejas (badge, multi_line, link, etc.)
            if (is_array($value)) {
                $typeInput = isset($value['type_input']) ? $value['type_input'] : (isset($value['type']) ? $value['type'] : null);
                if ($typeInput === 'multi_line' && isset($value['value']) && is_array($value['value'])) {
                    $value = implode(' - ', $value['value']);
                } elseif ($typeInput === 'link') {
                    $value = isset($value['label']) ? $value['label'] : '';
                } elseif (isset($value['value'])) {
                    $value = $value['value'];
                } else {
                    $value = '';
                }
            }
            $filtered[] = $value;
        }
        return $filtered;
    }

    /**
     * Extrae los headers (labels) de las columnas exportables.
     *
     * @param array $exportColumns
     * @return array
     */
    protected function getExportHeaders(array $exportColumns)
    {
        return collect($exportColumns)->map(function ($col) {
            $arr = $col instanceof \JsonSerializable ? $col->jsonSerialize() : (array) $col;
            return $arr['label'];
        })->all();
    }

    /**
     * Genera la fila de totales para las columnas summable que están en la exportación.
     *
     * @param array $allRows Colección de filas sin filtrar (con keys por nombre de columna)
     * @param array $exportColumns Columnas exportables filtradas
     * @return array Fila de totales en el mismo formato indexado que filterRowByColumns
     */
    protected function buildTotalsRow($allRows, array $exportColumns)
    {
        // Identificar qué columnas son summable
        $summableNames = [];
        foreach ($exportColumns as $col) {
            $arr = $col instanceof \JsonSerializable ? $col->jsonSerialize() : (array) $col;
            if (!empty($arr['summable'])) {
                $summableNames[] = $arr['name'];
            }
        }

        if (empty($summableNames)) {
            return [];
        }

        // Sumar valores de cada columna summable
        $sums = array_fill_keys($summableNames, 0);
        foreach ($allRows as $row) {
            foreach ($summableNames as $name) {
                $val = isset($row[$name]) ? $row[$name] : 0;
                $sums[$name] += floatval($val);
            }
        }

        // Construir fila en el orden de exportColumns
        $totalsRow = [];
        $isFirst = true;
        foreach ($exportColumns as $col) {
            $arr = $col instanceof \JsonSerializable ? $col->jsonSerialize() : (array) $col;
            $name = $arr['name'];
            if (!empty($arr['summable'])) {
                $totalsRow[] = number_format($sums[$name], 2, '.', '');
            } elseif ($isFirst) {
                $totalsRow[] = 'TOTALES';
            } else {
                $totalsRow[] = '';
            }
            $isFirst = false;
        }

        return $totalsRow;
    }

    /**
     * Identificador de tabla para persistir preferencias.
     * Los controllers pueden sobreescribir esto.
     *
     * @return string
     */
    protected function getReportTableName()
    {
        return 'report_' . class_basename(static::class);
    }

    /**
     * Debe retornar un array de Filter.
     *
     * @return array
     */
    abstract protected function getReportFilters();

    /**
     * Debe retornar un array de Column.
     *
     * @return array
     */
    abstract protected function getReportColumns();
}
