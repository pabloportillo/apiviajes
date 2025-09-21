<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('api_avail', '/api/avail')
        ->controller([\App\Controller\DisponibilidadController::class, 'getVuelos'])
        ->methods(['GET']);
};
