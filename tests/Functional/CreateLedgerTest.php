<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateLedgerTest extends WebTestCase
{
    public function testCreateLedger(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $token = $container->getParameterBag()->get('api_token');

        $client->request('POST', '/api/ledgers',
            [],
            [],
            [
                'HTTP_X-API-TOKEN' => $token,
            ],
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $data['id']
        );
    }
}
