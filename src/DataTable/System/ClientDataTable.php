<?php

namespace App\ESolutions\DataTable\System;

use App\ESolutions\DataTable\Table\Button;
use App\ESolutions\DataTable\Table\ButtonBuilder;
use App\ESolutions\DataTable\Table\Column;
use App\ESolutions\DataTable\Table\ColumnBuilder;
use App\ESolutions\DataTable\Table\Filter;
use App\ESolutions\DataTable\Table\FilterBuilder;
use App\ESolutions\DataTable\Traits\ExcelTrait;
use App\ESolutions\DataTable\Traits\PaginationSystemTrait;
use App\Http\Resources\System\ClientDataTableCollection;
use App\Models\System\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ClientDataTable
{
    use ExcelTrait, PaginationSystemTrait;

    protected function getTableConfig()
    {
        return [
            'page_title' => 'Clientes',
            'table_title' => 'Listado de Clientes',
            'table_name' => 'clients',
        ];
    }

    protected function getHeaderButtons()
    {
        return (new ButtonBuilder())
            ->addButton(Button::newButton()->label('Nuevo Cliente'))
            ->addButton(Button::refreshButton())
            ->addButtonGroup([Button::exportButton()])
            ->getButtons();
    }

    protected function getColumns()
    {
        return (new ColumnBuilder())
            ->addColumn(Column::make('hostname')->label('Hostname'))
            ->addColumn(Column::make('name')->label('Nombre')->sortable())
            ->addColumn(Column::make('number')->label('RUC'))
            ->addColumn(Column::make('plan')->label('Plan'))
            ->addColumn(Column::make('email')->label('Correo'))
            ->addColumn(Column::make('soap_type')->label('Entorno')->alignCenter())
            ->addColumn(Column::make('count_doc')->label('Comprobantes')->alignCenter())
            ->addColumn(Column::make('count_user')->label('Usuarios')->alignCenter())
            ->addColumn(Column::make('created_at')->label('F.Creación')->alignCenter())
            ->addColumn(Column::make('locked_tenant')->label('Bloqueado')->alignCenter()->locked())
            ->addColumn(Column::make('locked_emission')->label('Lim. Doc.')->alignCenter()->locked())
            ->addColumn(Column::actions())
            ->getColumns();
    }

    protected function getFilters()
    {
        return (new FilterBuilder())
            ->addFilter(Filter::makeInput('input')->cssClass('col-24 col-sm-12')->label('Buscar cliente'))
            ->getFilters();
    }

    public function getRecords(Request $request)
    {
        $this->updatePagination($request);
        $query = $this->buildClientFilters($request->input('filters', []));

        return (new ClientDataTableCollection($query->paginate($this->perPage)))
            ->additional($this->metaAdditional);
    }

    protected function buildClientFilters(array $filters): Builder
    {
        $input = collect($filters)->firstWhere('name', 'input');
        $value = is_array($input) ? $input['value'] ?? '' : '';

        return Client::query()
            ->with(['plan', 'hostname'])
            ->when($value, fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'LIKE', "%{$value}%")
                ->orWhere('number', 'LIKE', "%{$value}%")
            ))
            ->orderBy($this->sortBy ?: 'id', $this->direction);
    }

    public function exportClientRecords(Request $request)
    {
        return $this->exportRecordsGeneric(
            $request,
            ClientDataTableCollection::class,
            'reporte_clientes.xlsx',
            'Reporte de Clientes'
        );
    }
}
