<?php

namespace App\Proveedor;

use App\Entity\Segment;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Adaptador: clase que conecta con el endpoint HTTP y obtiene los vuelos.
 */
class ProveedorDisponibilidadHttp implements ProveedorDisponibilidadInterface
{
    public function buscar(string $origen, string $destino, string $fechaIso): array
    {
        $origen  = strtoupper($origen);
        $destino = strtoupper($destino);

        $client = HttpClient::create();
        $url = 'https://testapi.lleego.com/prueba-tecnica/availability-price-without-soap'
             . '?origin=' . urlencode($origen)
             . '&destination=' . urlencode($destino)
             . '&date=' . urlencode($fechaIso);

        try {
            $res = $client->request('GET', $url);
            $xml = $res->getContent();
        } catch (\Throwable $e) {
            return [['error' => 'Proveedor no disponible']];
        }

        $doc = new \DOMDocument();
        if (@$doc->loadXML($xml) === false) {
            return [['error' => 'XML inválido']];
        }

        $nodos = $doc->getElementsByTagName('FlightSegment');
        $lista = [];

        foreach ($nodos as $nodo) {
            // Departure
            $dep = $nodo->getElementsByTagName('Departure')->item(0);
            $depAirport = $dep?->getElementsByTagName('AirportCode')->item(0)?->nodeValue ?? '';
            $depDate    = $dep?->getElementsByTagName('Date')->item(0)?->nodeValue ?? '';
            $depTime    = $dep?->getElementsByTagName('Time')->item(0)?->nodeValue ?? '';

            // Arrival
            $arr = $nodo->getElementsByTagName('Arrival')->item(0);
            $arrAirport = $arr?->getElementsByTagName('AirportCode')->item(0)?->nodeValue ?? '';
            $arrDate    = $arr?->getElementsByTagName('Date')->item(0)?->nodeValue ?? '';
            $arrTime    = $arr?->getElementsByTagName('Time')->item(0)?->nodeValue ?? '';

            // Carrier
            $carrier = $nodo->getElementsByTagName('MarketingCarrier')->item(0);
            $airlineID = $carrier?->getElementsByTagName('AirlineID')->item(0)?->nodeValue ?? '';
            $flightNum = $carrier?->getElementsByTagName('FlightNumber')->item(0)?->nodeValue ?? '';

            // Validaciones mínimas
            if ($depAirport === '' || $arrAirport === '' || $depDate === '' || $arrDate === '' || $depTime === '' || $arrTime === '') {
                continue;
            }

            // Filtramos por parámetros
            if ($depAirport !== $origen) {
                continue;
            }
            if ($arrAirport !== $destino) {
                continue;
            }
            if ($depDate !== $fechaIso) {
                continue;
            }

            // Creamos las fechas
            $startDt = \DateTime::createFromFormat('Y-m-d H:i', $depDate.' '.$depTime);
            $endDt   = \DateTime::createFromFormat('Y-m-d H:i', $arrDate.' '.$arrTime);

            // Si fallan, salta este segmento
            if ($startDt === false || $endDt === false) {
                continue;
            }

            $segment = (new Segment())
                ->setOriginCode($depAirport)
                ->setOriginName($depAirport)
                ->setDestinationCode($arrAirport)
                ->setDestinationName($arrAirport)
                ->setStart($startDt)
                ->setEnd($endDt)
                ->setTransportNumber($flightNum !== '' ? $flightNum : '0000')
                ->setCompanyCode($airlineID !== '' ? $airlineID : 'XX')
                ->setCompanyName($airlineID !== '' ? $airlineID : 'XX');

            $lista[] = $segment;
        }

        return $lista;
    }
}
