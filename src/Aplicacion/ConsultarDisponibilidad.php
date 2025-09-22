<?php

namespace App\Aplicacion;

use App\Proveedor\ProveedorDisponibilidadInterface;

/**
 * Caso de uso: Gestiona la consulta de disponibilidad.
 */
class ConsultarDisponibilidad
{
    private ProveedorDisponibilidadInterface $proveedor;

    public function __construct(ProveedorDisponibilidadInterface $proveedor)
    {
        $this->proveedor = $proveedor;
    }

    /**
     * @return array  // array de FlightSegment o [['error' => '...']]
     */
    public function handle(string $origen, string $destino, string $fechaIso): array
    {
        return $this->proveedor->buscar($origen, $destino, $fechaIso);
    }
}
