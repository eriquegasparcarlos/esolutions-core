<?php

namespace App\ESolutions\Helpers;

use Symfony\Component\Console\Output\ConsoleOutput;

class ProgressBarHelper
{
    protected int $barLength;
    protected ConsoleOutput $output;

    public function __construct(int $barLength = 20)
    {
        $this->barLength = $barLength;
        $this->output = new ConsoleOutput();
    }

    /**
     * Renderiza la barra de progreso.
     *
     * @param int $processed Número de registros procesados
     * @param int $total Total de registros
     * @param string $status Estado: success, error, warning, info
     * @param string|null $message Mensaje adicional a mostrar
     */
    public function render(int $processed, int $total, string $status = 'success', ?string $message = null): void
    {
        if ($total === 0) {
            $this->output->writeln('<fg=yellow>No hay elementos a procesar.</>');
            return;
        }

        $percentRaw = $processed / $total;
        $filledLength = round($this->barLength * $percentRaw);
        $emptyLength = $this->barLength - $filledLength;

        $filled = str_repeat('█', max(0, $filledLength));
        $empty = str_repeat('░', max(0, $emptyLength));

        $bar = $filled . $empty;
        $percent = number_format($percentRaw * 100, 1);

        $color = match ($status) {
            'success' => 'green',
            'error' => 'red',
            'warning' => 'yellow',
            default => 'white',
        };

        $output = "<fg={$color}>Progreso: [{$bar}] {$processed}/{$total} ({$percent}%)</>";

        if ($message && $message !== '') {
            $output .= " - <fg=white>{$message}</>";
        }

        $this->output->writeln($output);
    }
}
