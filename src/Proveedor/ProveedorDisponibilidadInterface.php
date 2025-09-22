<?php

namespace App\Proveedor;

use App\Entity\FlightSegment;

/**
 * Puerto: interfaz que define lo que debe ofrecer un proveedor de disponibilidad.
 */
interface ProveedorDisponibilidadInterface
{
    /**
     * Busca vuelos disponibles.
     *
     * @return array<FlightSegment>|array<array<string,string>>  // si hay error: [['error' => '...']]
     */
    public function buscar(string $origen, string $destino, string $fechaIso): array;
}
