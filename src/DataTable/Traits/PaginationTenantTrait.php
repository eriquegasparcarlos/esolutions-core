<?php

namespace App\ESolutions\DataTable\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Configuration\Models\ConfigurationDataTable;

trait PaginationTenantTrait
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
