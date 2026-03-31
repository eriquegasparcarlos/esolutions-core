<?php

namespace App\ESolutions\Helpers;

use Symfony\Component\Console\Output\ConsoleOutput;

class ProgressBarHelper
{
    /** @var int */
    protected $barLength;
    /** @var ConsoleOutput */
    protected $output;

    /**
     * @param int $barLength
     */
    public function __construct($barLength = 20)
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
     * @return void
     */
    public function render($processed, $total, $status = 'success', $message = null)
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

        switch ($status) {
            case 'success':
                $color = 'green';
                break;
            case 'error':
                $color = 'red';
                break;
            case 'warning':
                $color = 'yellow';
                break;
            default:
                $color = 'white';
                break;
        }

        $output = "<fg={$color}>Progreso: [{$bar}] {$processed}/{$total} ({$percent}%)</>";

        if ($message && $message !== '') {
            $output .= " - <fg=white>{$message}</>";
        }

        $this->output->writeln($output);
    }
}
