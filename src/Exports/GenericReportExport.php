<?php

namespace App\ESolutions\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
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
    WithColumnWidths,
    WithCustomStartCell
{
    protected $data;
    protected $headings;
    protected $title;
    protected $totalsRow;
    protected $companyName;
    protected $companyRuc;
    protected $columns;
    protected $totalsRows;

    /** Fila donde inician los headers de columna (depende de si hay cabecera empresa) */
    protected $headerRow;

    /**
     * @param array  $data        Datos de la tabla (array de arrays).
     * @param array  $headings    Encabezados de columna.
     * @param string $title       Título grande para el reporte.
     * @param array  $totalsRow   Fila de totales simple (opcional, legacy).
     * @param string $companyName Razón social (opcional).
     * @param string $companyRuc  RUC de la empresa (opcional).
     * @param array  $columns     Columnas con propiedades Excel (excel_width, excel_format, etc.).
     * @param array  $totalsRows  Filas de totales múltiples con merge de label (opcional).
     */
    public function __construct(
        array $data,
        array $headings,
        string $title = 'Reporte',
        array $totalsRow = [],
        string $companyName = '',
        string $companyRuc = '',
        array $columns = [],
        array $totalsRows = []
    ) {
        $this->data = $data;
        $this->headings = $headings;
        $this->title = $title;
        $this->totalsRow = $totalsRow;
        $this->companyName = $companyName;
        $this->companyRuc = $companyRuc;
        $this->columns = $columns;
        $this->totalsRows = $totalsRows;
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
        foreach ($this->totalsRows as $row) {
            $rows[] = $row;
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

    public function columnWidths(): array
    {
        $widths = [];
        foreach ($this->columns as $i => $col) {
            $colLetter = Coordinate::stringFromColumnIndex($i + 1);
            $w = is_array($col) ? ($col['excel_width'] ?? null) : null;
            if ($w) {
                $widths[$colLetter] = $w;
            }
        }
        return $widths;
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

                // Estilo de la fila de totales (legacy: single row)
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

                // Filas de totales múltiples con merge de label
                if (!empty($this->totalsRows)) {
                    // Calcular cuántas columnas no-summable hay antes de la primera summable
                    $labelSpan = 0;
                    foreach ($this->columns as $col) {
                        if (!empty($col['summable'])) break;
                        $labelSpan++;
                    }
                    if ($labelSpan === 0) $labelSpan = 1;
                    $mergeEndCol = Coordinate::stringFromColumnIndex($labelSpan);

                    $baseRowNum = count($this->data) + $this->headerRow + (empty($this->totalsRow) ? 0 : 1);

                    foreach ($this->totalsRows as $idx => $totRow) {
                        $rowNum = $baseRowNum + $idx + 1;

                        // Merge celdas del label
                        if ($labelSpan > 1) {
                            $sheet->mergeCells("A{$rowNum}:{$mergeEndCol}{$rowNum}");
                        }

                        // Estilo fondo verde + negrita
                        $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'e8f5e9']],
                        ]);

                        // Label alineado a la derecha
                        $sheet->getStyle("A{$rowNum}:{$mergeEndCol}{$rowNum}")
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColIndex = count($this->headings);
        $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);
        $hr = $this->headerRow;
        $lastRow = count($this->data) + $hr + (empty($this->totalsRow) ? 0 : 1) + count($this->totalsRows);

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

        // Datos: alineación vertical base
        $dataStart = $hr + 1;
        $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Estilos por columna (alineación horizontal, formato numérico, wrap)
        foreach ($this->columns as $i => $col) {
            if (!is_array($col)) continue;
            $colLetter = Coordinate::stringFromColumnIndex($i + 1);
            $range = "{$colLetter}{$dataStart}:{$colLetter}{$lastRow}";

            // Alineación horizontal
            $align = $col['align'] ?? 'left';
            $excelAlign = Alignment::HORIZONTAL_LEFT;
            if ($align === 'right') {
                $excelAlign = Alignment::HORIZONTAL_RIGHT;
            } elseif ($align === 'center') {
                $excelAlign = Alignment::HORIZONTAL_CENTER;
            }
            $sheet->getStyle($range)->getAlignment()->setHorizontal($excelAlign);

            // Formato numérico
            if (!empty($col['excel_format'])) {
                $sheet->getStyle($range)->getNumberFormat()->setFormatCode($col['excel_format']);
            }

            // Wrap text
            if (!empty($col['excel_wrap'])) {
                $sheet->getStyle($range)->getAlignment()->setWrapText(true);
            }
        }
    }
}
