<?php

namespace App\ESolutions\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Events\AfterSheet;

/**
 * Exportador genérico reutilizable para cualquier reporte tabular.
 */
class GenericReportExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithEvents,
    ShouldAutoSize,
    WithCustomStartCell
{
    protected $data;
    protected $headings;
    protected $title;
    protected $totalsRow;
    protected $companyName;
    protected $companyRuc;

    /** Fila donde inician los headers de columna (depende de si hay cabecera empresa) */
    protected $headerRow;

    /**
     * @param array  $data        Datos de la tabla (array de arrays).
     * @param array  $headings    Encabezados de columna.
     * @param string $title       Título grande para el reporte.
     * @param array  $totalsRow   Fila de totales (opcional).
     * @param string $companyName Razón social (opcional).
     * @param string $companyRuc  RUC de la empresa (opcional).
     */
    public function __construct(
        array $data,
        array $headings,
        string $title = 'Reporte',
        array $totalsRow = [],
        string $companyName = '',
        string $companyRuc = ''
    ) {
        $this->data = $data;
        $this->headings = $headings;
        $this->title = $title;
        $this->totalsRow = $totalsRow;
        $this->companyName = $companyName;
        $this->companyRuc = $companyRuc;
        // Si hay datos de empresa: fila 1 empresa, fila 2 RUC, fila 3 título, fila 4 headers
        // Si no: fila 1 título, fila 2 headers (comportamiento original)
        $this->headerRow = $this->hasCompanyHeader() ? 4 : 2;
    }

    protected function hasCompanyHeader(): bool
    {
        return $this->companyName !== '' || $this->companyRuc !== '';
    }

    public function array(): array
    {
        $rows = $this->data;
        if (!empty($this->totalsRow)) {
            $rows[] = $this->totalsRow;
        }
        return $rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function startCell(): string
    {
        return 'A' . $this->headerRow;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastColIndex = count($this->headings);
                $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);
                $sheet = $event->sheet;

                if ($this->hasCompanyHeader()) {
                    // Fila 1: Nombre de la empresa
                    $sheet->mergeCells("A1:{$lastCol}1");
                    $sheet->setCellValue("A1", mb_strtoupper($this->companyName));
                    $sheet->getStyle("A1")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 14,
                            'color' => ['rgb' => '1a1a1a'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getRowDimension(1)->setRowHeight(24);

                    // Fila 2: RUC
                    $sheet->mergeCells("A2:{$lastCol}2");
                    $sheet->setCellValue("A2", 'RUC: ' . $this->companyRuc);
                    $sheet->getStyle("A2")->applyFromArray([
                        'font' => [
                            'bold' => false,
                            'size' => 11,
                            'color' => ['rgb' => '4b5563'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getRowDimension(2)->setRowHeight(20);

                    // Fila 3: Título del reporte
                    $sheet->mergeCells("A3:{$lastCol}3");
                    $sheet->setCellValue("A3", $this->title);
                    $sheet->getStyle("A3")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                            'color' => ['rgb' => '1976d2'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getRowDimension(3)->setRowHeight(22);
                } else {
                    // Comportamiento original: solo título en fila 1
                    $sheet->mergeCells("A1:{$lastCol}1");
                    $sheet->setCellValue("A1", $this->title);
                    $sheet->getStyle("A1")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 18,
                            'color' => ['rgb' => '000000'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getRowDimension(1)->setRowHeight(30);
                }

                // Estilo de la fila de totales
                if (!empty($this->totalsRow)) {
                    $totalsRowNum = count($this->data) + $this->headerRow + 1;
                    $sheet->getStyle("A{$totalsRowNum}:{$lastCol}{$totalsRowNum}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => '000000'],
                        ],
                        'fill' => [
                            'fillType' => 'solid',
                            'startColor' => ['rgb' => 'e8f5e9'],
                        ],
                    ]);
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColIndex = count($this->headings);
        $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);
        $hr = $this->headerRow;
        $lastRow = count($this->data) + $hr + (empty($this->totalsRow) ? 0 : 1);

        // Headers de columna
        $sheet->getStyle("A{$hr}:{$lastCol}{$hr}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '1976d2'],
            ],
        ]);

        // Bordes en toda la tabla
        $sheet->getStyle("A{$hr}:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => 'e0e0e0'],
                ],
            ],
        ]);

        // Datos
        $dataStart = $hr + 1;
        $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")
            ->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }
}
