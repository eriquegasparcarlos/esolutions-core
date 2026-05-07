<?php

namespace App\ESolutions\DataTable\Traits;

use Illuminate\Http\Request;

trait PaginationBaseTrait
{
    /** @var string */
    protected $pageTitle;
    /** @var string */
    protected $pageDescription;
    /** @var string */
    protected $tableName;
    /** @var string */
    protected $tableTitle;
    /** @var array */
    protected $visibleColumns = [];
    /** @var array */
    protected $columns = [];
    /** @var array */
    protected $filters = [];
    /** @var string|null */
    protected $sortBy = 'id';
    /** @var bool */
    protected $descending = true;
    /** @var string */
    protected $direction = 'asc';
    /** @var int */
    protected $perPage = 10;
    /** @var array */
    protected $metaAdditional = [];

    /**
     * @param mixed $model
     * @return array
     */
    public function initTableBase($model)
    {
        $config = $this->getTableConfig();

        $this->pageTitle = __($config['page_title']);
        $this->pageDescription = isset($config['page_description']) ? __($config['page_description']) : '';
        $this->tableTitle = __($config['table_title']);
        $this->tableName = $config['table_name'];
        $this->columns = $this->getColumns();
        $this->filters = $this->getFilters();

        $this->getConfigurationDataTableBase($model);

        return [
            'pageTitle'       => $this->pageTitle,
            'pageDescription' => $this->pageDescription,
            'tableName'       => $this->tableName,
            'tableTitle'      => $this->tableTitle,
            'columns'         => $this->columns,
            'filters'         => $this->filters,
            'visibleColumns'  => $this->visibleColumns,
            'pagination'      => $this->initPagination(),
            'headerButtons'   => $this->getHeaderButtons(),
            'selectable'      => $this->getSelectable(),
        ];
    }

    /**
     * Indica si la tabla soporta selección múltiple de filas.
     * Sobreescribir en el DataTable trait específico para activarlo.
     *
     * @return bool
     */
    protected function getSelectable(): bool
    {
        return false;
    }

    /**
     * @return array
     */
    public function initPagination()
    {
        $defaultSortable = collect($this->columns)->first(function ($col) {
            return isset($col['sortable']) ? (bool)$col['sortable'] : false;
        });

        $pageSizes = array_values(array_unique([5, 10, 20, 50, 3, $this->perPage]));

        return [
            'sortBy' => $defaultSortable
                ? (isset($defaultSortable['sort_field']) ? $defaultSortable['sort_field'] : $defaultSortable['name'])
                : $this->sortBy,
            'descending' => $this->descending,
            'perPage' => $this->perPage,
            'pageSizes' => $pageSizes,
        ];
    }

    /**
     * Carga/crea preferencia del usuario/tabla
     *
     * @param mixed $model
     * @return void
     */
    protected function getConfigurationDataTableBase($model)
    {
        $record = $model
            ->where('user_id', auth()->id())
            ->where('table', $this->tableName)
            ->first();

        if (!$record) {
            $this->visibleColumns = $this->extractVisibleColumns();
            $modelClass = get_class($model->getModel());
            $model = new $modelClass();
            $model->user_id = auth()->id();
            $model->table = $this->tableName;
            $model->visible_columns = $this->visibleColumns;
            $model->records_per_page = 10;
            $model->sort_by = 'id';
            $model->descending = true;
            $model->save();
        } else {
            $this->visibleColumns = $record->visible_columns ?? [];
            $this->perPage = (int)($record->records_per_page ?? 10);
            $this->sortBy = $this->resolveSortField((string)($record->sort_by ?: 'id'));
            $this->descending = (bool)($record->descending ?? true);
            $this->direction = $this->descending ? 'desc' : 'asc';
        }
    }

    /**
     * @return array
     */
    private function extractVisibleColumns()
    {
        if (empty($this->columns) || !is_array($this->columns)) {
            return [];
        }

        return collect($this->columns)
            ->filter(function ($col) {
                if (isset($col['visible']) && $col['visible'] === false) {
                    return false;
                }
                return true;
            })
            ->pluck('name')
            ->all();
    }

    /**
     * Resuelve el nombre real de la columna en BD para ORDER BY.
     * Si la columna define sort_field, lo usa; si no, retorna el name original.
     */
    protected function resolveSortField($sortByName)
    {
        if ($sortByName === null || $sortByName === '') {
            return 'id';
        }

        if (empty($this->columns) || !is_array($this->columns)) {
            return $sortByName;
        }

        // Buscar por name o por sort_field
        $col = collect($this->columns)->first(function ($c) use ($sortByName) {
            return ($c['name'] ?? null) === $sortByName
                || ($c['sort_field'] ?? null) === $sortByName;
        });

        if ($col && !empty($col['sort_field'])) {
            return $col['sort_field'];
        }

        // Si la columna encontrada no es sortable, usar la primera sortable
        if ($col && empty($col['sortable'])) {
            $firstSortable = collect($this->columns)->first(function ($c) {
                return !empty($c['sortable']);
            });
            if ($firstSortable) {
                return $firstSortable['sort_field'] ?? $firstSortable['name'];
            }
        }

        return $sortByName;
    }

    /**
     * @param mixed $model
     * @param Request $request
     * @return void
     */
    public function updateConfigurationDataTableBase($model, Request $request)
    {
        $this->tableName = $request->input('tableName', $this->tableName ?: '');
        $this->visibleColumns = $request->input('visibleColumns', $this->visibleColumns);
        $this->sortBy = $this->resolveSortField($request->input('sortBy', $this->sortBy));
        $this->descending = (bool)$request->input('descending', $this->descending);
        $this->direction = $this->descending ? 'desc' : 'asc';
        $this->perPage = (int)$request->input('rowsPerPage', $this->perPage);

        $this->metaAdditional = [
            'meta' => [
                'sort_by' => $this->sortBy,
                'descending' => $this->descending,
            ]
        ];

        $record = $model
            ->where('user_id', auth()->id())
            ->where('table', $this->tableName)
            ->first();

        if ($record) {
            $record->records_per_page = $this->perPage;
            $record->sort_by = (string)$this->sortBy;
            $record->descending = $this->descending;
            $record->save();
        }
    }

    /**
     * @param mixed $model
     * @param array $inputs
     * @return array
     */
    public function updateVisibleColumnsWithDataBase($model, array $inputs)
    {
        if (!isset($inputs['table_name'], $inputs['visible_columns'])) {
            return [
                'success' => false,
                'message' => 'No se realizó la actualización'
            ];
        }

        $record = $model
            ->where('user_id', auth()->id())
            ->where('table', $inputs['table_name'])
            ->first();

        if ($record) {
            $visible = $inputs['visible_columns'];
            if (is_array($visible)) {
                $visible = array_values($visible);
            }
            $record->visible_columns = $visible;
            $record->save();
        }

        return [
            'success' => true,
            'message' => 'Actualización satisfactoria'
        ];
    }
}
