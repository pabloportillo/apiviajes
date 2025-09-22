<?php

namespace App\Controller;

use App\Aplicacion\ConsultarDisponibilidad;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador de disponibilidad de vuelos.
 * GET /api/avail?origin=MAD&destination=BIO&date=2022-06-01
 */
class DisponibilidadController
{
    private ConsultarDisponibilidad $consultarDisponibilidad;

    public function __construct(ConsultarDisponibilidad $consultarDisponibilidad)
    {
        $this->consultarDisponibilidad = $consultarDisponibilidad;
    }

    private function segmentToArray(\App\Entity\Segment $s): array
    {
        return [
            'originCode'       => $s->getOriginCode(),
            'originName'       => $s->getOriginName(),
            'destinationCode'  => $s->getDestinationCode(),
            'destinationName'  => $s->getDestinationName(),
            'start'            => $s->getStart()->format('Y-m-d H:i'),
            'end'              => $s->getEnd()->format('Y-m-d H:i'),
            'transportNumber'  => $s->getTransportNumber(),
            'companyCode'      => $s->getCompanyCode(),
            'companyName'      => $s->getCompanyName(),
        ];
    }

    public function getVuelos(Request $request): JsonResponse
    {
        $origen  = (string) $request->query->get('origin', '');
        $destino = (string) $request->query->get('destination', '');
        $fecha   = (string) $request->query->get('date', '');

        if ($origen === '' || $destino === '' || $fecha === '') {
            return new JsonResponse([
                'error' => 'Parametros requeridos: origin, destination, date (YYYY-MM-DD)'
            ], 400);
        }

        $vuelos = $this->consultarDisponibilidad->handle($origen, $destino, $fecha);

        if (!empty($vuelos) && is_array($vuelos[0]) && isset($vuelos[0]['error'])) {
            return new JsonResponse($vuelos, 502);
        }

        $payload = [];
        foreach ($vuelos as $vuelo) {
            if ($vuelo instanceof \App\Entity\Segment) {
                $payload[] = $this->segmentToArray($vuelo);
            }
        }

        return new JsonResponse($payload);
    }
}
