<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DisponibilidadControllerTest extends WebTestCase
{
    public function test_api_avail_responde_200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/avail', [
            'origin'      => 'MAD',
            'destination' => 'BIO',
            'date'        => '2022-06-01',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function test_api_avail_devuelve_array(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/avail', [
            'origin'      => 'MAD',
            'destination' => 'BIO',
            'date'        => '2022-06-01',
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
    }

    public function test_api_avail_no_vacio(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/avail', [
            'origin'      => 'MAD',
            'destination' => 'BIO',
            'date'        => '2022-06-01',
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
    }

    public function test_api_avail_tiene_campos_basicos(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/avail', [
            'origin'      => 'MAD',
            'destination' => 'BIO',
            'date'        => '2022-06-01',
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('originCode', $data[0]);
        $this->assertArrayHasKey('destinationCode', $data[0]);
        $this->assertArrayHasKey('start', $data[0]);
        $this->assertArrayHasKey('end', $data[0]);
    }

    public function test_api_avail_falta_parametro_devuelve_400(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/avail', [
            'origin' => 'MAD',
            // 'destination' falta
            'date'   => '2022-06-01',
        ]);

        $this->assertResponseStatusCodeSame(400);
    }


    public function test_api_avail_fake_responde_200(): void
    {
        $client = static::createClient();

        // Hacemos un mock del puerto para devolver 1 segmento sin pegarle al proveedor
        $seg = (new \App\Entity\Segment())
            ->setOriginCode('MAD')->setOriginName('MAD')
            ->setDestinationCode('BIO')->setDestinationName('BIO')
            ->setStart(new \DateTime('2022-06-01 11:50'))
            ->setEnd(new \DateTime('2022-06-01 12:55'))
            ->setTransportNumber('0426')->setCompanyCode('IB')->setCompanyName('IB');

        $stub = new class($seg) implements \App\Proveedor\ProveedorDisponibilidadInterface {
            public function __construct(private $seg) {}
            public function buscar(string $o, string $d, string $f): array { return [$this->seg]; }
        };
        static::getContainer()->set(\App\Proveedor\ProveedorDisponibilidadInterface::class, $stub);

        $client->request('GET', '/api/avail', [
            'origin' => 'MAD', 'destination' => 'BIO', 'date' => '2022-06-01',
        ]);

        $this->assertResponseIsSuccessful();
    }

}
