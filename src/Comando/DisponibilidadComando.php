<?php

namespace App\Comando;

use App\Aplicacion\ConsultarDisponibilidad;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Comando para consultar los vuelos.
 * Uso: php bin/console lleego:avail MAD BIO 2022-06-01
 */
#[AsCommand(
    name: 'lleego:avail',
    description: 'Consulta disponibilidad de vuelos.'
)]
class DisponibilidadComando extends Command
{
    private ConsultarDisponibilidad $consultarDisponibilidad;

    public function __construct(ConsultarDisponibilidad $consultarDisponibilidad)
    {
        parent::__construct();
        $this->consultarDisponibilidad = $consultarDisponibilidad;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('origen', InputArgument::REQUIRED, 'Código IATA origen')
            ->addArgument('destino', InputArgument::REQUIRED, 'Código IATA destino')
            ->addArgument('fecha', InputArgument::REQUIRED, 'Fecha YYYY-MM-DD');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $origen  = (string) $input->getArgument('origen');
        $destino = (string) $input->getArgument('destino');
        $fecha   = (string) $input->getArgument('fecha');

        $vuelos = $this->consultarDisponibilidad->handle($origen, $destino, $fecha);

        if (empty($vuelos)) {
            $output->writeln('No se encontraron vuelos');
            return Command::FAILURE;
        }

        if (is_array($vuelos[0]) && isset($vuelos[0]['error'])) {
            $output->writeln('Error: ' . ($vuelos[0]['error'] ?? 'desconocido'));
            return Command::FAILURE;
        }

        $filas = [];
        foreach ($vuelos as $vuelo) {
            // Usamos getters del Segment
            $filas[] = [
                $vuelo->getOriginCode(),
                $vuelo->getDestinationCode(),
                $vuelo->getStart()->format('Y-m-d H:i'),
                $vuelo->getEnd()->format('Y-m-d H:i'),
                $vuelo->getCompanyCode(),
                $vuelo->getTransportNumber(),
            ];
        }

        $tabla = new Table($output);
        $tabla->setHeaders(['originCode', 'destinationCode', 'start', 'end', 'companyCode', 'transportNumber']);
        $tabla->setRows($filas);
        $tabla->render();

        $output->writeln('Total: ' . count($filas) . ' vuelos');

        return Command::SUCCESS;
    }
}
