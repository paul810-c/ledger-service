<?php

namespace App\Tests\Functional;

use App\Domain\Ledger\Entity\Ledger;
use App\Tests\WebTestCaseWithFixtures;
use Symfony\Component\HttpFoundation\Response;

class GetLedgerBalancesFunctionalTest extends WebTestCaseWithFixtures
{
    public function testItReturnsBalancesForLedger(): void
    {
        $client = static::createClient();
        $this->loadFixtures();
        $container = static::getContainer();
        $token = $container->getParameterBag()->get('api_token');

        $this->em = static::getContainer()->get('doctrine')->getManager();

        /** @var Ledger $ledger */
        $ledger = $this->em->getRepository(Ledger::class)->findAll()[0];
        $ledgerId = (string) $ledger->getId();

        $client->request('GET', '/api/balances/' . $ledgerId,
            [],
            [],
            [
                'HTTP_X-API-TOKEN' => $token,
            ],);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertCount(1, $data);

        $balance = $data[0];
        $this->assertEquals('EUR', $balance['currency']);
        $this->assertEquals('0.00', $balance['amount']);
    }
}