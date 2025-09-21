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
}
