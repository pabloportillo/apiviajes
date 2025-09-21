<?php

namespace App\Comando;

use App\Proveedor\ProveedorDisponibilidad;
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
    protected function configure(): void
    {
        // Argumentos necesarios para el comando
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

        // Pedimos los datos al proveedor
        $proveedor = new ProveedorDisponibilidad();
        $vuelos = $proveedor->buscar($origen, $destino, $fecha);

        // Comprobamos si la lista de vuelos viene vacía
        if (empty($vuelos)) {
            $output->writeln('No se encontraron vuelos');
            return Command::FAILURE;
        }

        // Preparamos filas para la tabla
        $filas = [];
        foreach ($vuelos as $vuelo) {
            $d = $vuelo->toArray();
            $filas[] = [
                $d['originCode'],
                $d['destinationCode'],
                $d['start'],
                $d['end'],
                $d['companyCode'],
                $d['transportNumber'],
            ];
        }

        // Pintamos la tabla con el Table de Symfony
        $tabla = new Table($output);
        $tabla->setHeaders(['originCode', 'destinationCode', 'start', 'end', 'companyCode', 'transportNumber']);
        $tabla->setRows($filas);
        $tabla->render();

        $output->writeln('Total: ' . count($filas) . ' vuelos');

        return Command::SUCCESS;
    }
}
