<?php

namespace App\Proveedor;

use App\Entity\FlightSegment;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Proveedor de disponibilidad de vuelos.
 * Hace GET al endpoint sin SOAP, 
 * Lee el XML y devuelve la lista de vuelos "FlightSegment".
 */
class ProveedorDisponibilidad
{
    public function buscar(string $origen, string $destino, string $fechaIso): array
    {
        $origen  = strtoupper($origen);
        $destino = strtoupper($destino);

        // Llamada HTTP
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

        // Cargamos el XML
        $doc = new \DOMDocument();
        if (@$doc->loadXML($xml) === false) {
            return [['error' => 'XML inválido']];
        }

        // Buscamos todos los "FlightSegment"
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

            // Si faltan datos, lo saltamos
            if ($depAirport === '' || $arrAirport === '' || $depDate === '' || $arrDate === '' || $depTime === '' || $arrTime === '') {
                continue;
            }

            // Filtramos por los parámetros que nos pasaron
            if ($depAirport !== $origen) continue;
            if ($arrAirport !== $destino) continue;
            if ($depDate !== $fechaIso) continue;

            // Formamos las fechas
            $start = $depDate . ' ' . $depTime;
            $end   = $arrDate . ' ' . $arrTime;

            // Creamos el objeto FlightSegment
            $lista[] = new FlightSegment(
                $depAirport,
                $depAirport,
                $arrAirport,
                $arrAirport, 
                $start,
                $end,
                $flightNum !== '' ? $flightNum : '0000',
                $airlineID !== '' ? $airlineID : 'XX',
                $airlineID !== '' ? $airlineID : 'XX'
            );
        }

        return $lista;
    }
}
