<?php

namespace App\ESolutions\DataTable;

use App\ESolutions\DataTable\Table\Button;
use App\ESolutions\DataTable\Table\ButtonBuilder;
use App\ESolutions\DataTable\Table\Column;
use App\ESolutions\DataTable\Table\ColumnBuilder;
use App\ESolutions\DataTable\Table\Filter;
use App\ESolutions\DataTable\Table\FilterBuilder;
use App\ESolutions\DataTable\Traits\PaginationTenantTrait;
use App\Http\Resources\Tenant\PersonDataTableCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Person\Models\Person;

trait PersonDataTable
{
    use PaginationTenantTrait;

    /** @var string 'customers' | 'suppliers' */
    protected $personType = 'customers';

    /**
     * @return array
     */
    protected function getTableConfig()
    {
        $isCustomer = $this->personType === 'customers';

        return [
            'page_title' => $isCustomer ? 'Clientes' : 'Proveedores',
            'table_title' => $isCustomer ? 'Listado de clientes' : 'Listado de proveedores',
            'table_name' => $this->personType,
        ];
    }

    /**
     * @return array
     */
    protected function getHeaderButtons()
    {
        return (new ButtonBuilder())
            ->addButton(Button::newButton())
            ->addButton(Button::exportButton('Exportar', '/persons/' . $this->personType . '/exportation'))
            ->addButton(Button::make()->label('Importar')->icon('upload')->action('import'))
            ->addButton(Button::refreshButton())
            ->getButtons();
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        $isCustomer = $this->personType === 'customers';

        return (new ColumnBuilder())
            ->addColumn(Column::make('name')->label('Nombre')->sortable())
            ->addColumn(Column::make('internal_code')->label('Cód. Interno'))
            ->addColumn(Column::make('document_type')->label('Tipo Doc.'))
            ->addColumn(Column::make('number')->label('Número')->sortable())
            ->addColumn(Column::make('person_type')->label($isCustomer ? 'T. Cliente' : 'T. Proveedor')->visible(false))
            ->addColumn(Column::make('email')->label('Correo')->visible(false))
            ->addColumn(Column::make('telephone')->label('Teléfono')->visible(false))
            ->addColumn(Column::make('credit_days')->label('Días crédito')->visible(false))
            ->addColumn(Column::make('seller_name')->label('Vendedor')->visible(false))
            ->addColumn(Column::make('zone_name')->label('Zona')->visible(false))
            ->addColumn(Column::make('enabled')->label('Estado'))
            ->getColumns();
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return (new FilterBuilder())
            ->addFilter(Filter::makeInput('input')->cssClass('col-24 col-sm-12')->label('Buscar por nombre o número'))
            ->getFilters();
    }

    /**
     * @param Request $request
     * @return PersonDataTableCollection
     */
    public function getRecords(Request $request)
    {
        $this->updatePagination($request);
        $query = $this->buildPersonFilters($request->input('filters', []));

        return (new PersonDataTableCollection($query->paginate($this->perPage)))
            ->additional($this->metaAdditional);
    }

    /**
     * @param array $filters
     * @return Builder
     */
    protected function buildPersonFilters(array $filters)
    {
        $filterCollection = collect($filters);

        $input = $filterCollection->firstWhere('name', 'input');
        $value = is_array($input) ? ($input['value'] ?? '') : '';

        return Person::query()
            ->where('type', $this->personType)
            ->whereFilterCustomerBySeller($this->personType)
            ->when($value, function (Builder $q) use ($value) {
                $q->where(function ($q2) use ($value) {
                    $q2->where('name', 'LIKE', "%{$value}%")
                       ->orWhere('number', 'LIKE', "%{$value}%");
                });
            })
            ->orderBy($this->sortBy, $this->direction);
    }
}
