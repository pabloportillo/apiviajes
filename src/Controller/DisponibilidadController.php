<?php

namespace App\Controller;

use App\Proveedor\ProveedorDisponibilidad;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador de disponibilidad de vuelos.
 * GET /api/vuelos?origin=MAD&destination=BIO&date=2022-06-01
 */
class DisponibilidadController
{
    public function getVuelos(Request $request): JsonResponse
    {
        $origen = (string) $request->query->get('origin', '');
        $destino = (string) $request->query->get('destination', '');
        $fecha = (string) $request->query->get('date', '');

        // Parametros obligatorios
        if ($origen === '' || $destino === '' || $fecha === '') {
            return new JsonResponse([
                'error' => 'Parametros requeridos: origin, destination, date (YYYY-MM-DD)'
            ], 400);
        }

        $proveedor = new ProveedorDisponibilidad();
        $vuelos = $proveedor->buscar($origen, $destino, $fecha);

        // Si hay algún error lo mostramos
        if (!empty($vuelos) && is_array($vuelos[0]) && isset($vuelos[0]['error'])) {
            return new JsonResponse($vuelos, 502);
        }

        $payload = [];
        foreach ($vuelos as $vuelo) {
            $payload[] = $vuelo->toArray();
        }

        return new JsonResponse($payload);
    }
}
