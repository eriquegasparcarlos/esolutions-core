<?php

namespace App\ESolutions\DataTable\Traits;

use Modules\System\Models\ConfigurationDataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait PaginationSystemTrait
{
    use PaginationBaseTrait;

    /**
     * @return Builder
     */
    protected function defaultModelQuery()
    {
        return ConfigurationDataTable::query();
    }

    /**
     * @return array
     */
    public function initTable()
    {
        return $this->initTableBase($this->defaultModelQuery());
    }

    /**
     * @param Request $request
     * @return void
     */
    public function updatePagination(Request $request)
    {
        $this->updateConfigurationDataTableBase($this->defaultModelQuery(), $request);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function updateVisibleColumns(Request $request)
    {
        $this->updateVisibleColumnsWithDataBase($this->defaultModelQuery(), $request->all());

        return [
            'success' => true,
            'message' => 'Actualización satisfactoria'
        ];
    }
}
