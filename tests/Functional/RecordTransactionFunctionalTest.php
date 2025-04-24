<?php

namespace App\Tests\Functional;

use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Transaction\Entity\Transaction;
use App\Tests\WebTestCaseWithFixtures;
use Symfony\Component\HttpFoundation\Response;

class RecordTransactionFunctionalTest extends WebTestCaseWithFixtures
{
    public function testRecordTransactionEndpoint(): void
    {
        $client = static::createClient();

        $this->loadFixtures();
        $container = static::getContainer();
        $token = $container->getParameterBag()->get('api_token');
        // $this->em = static::getContainer()->get('doctrine')->getManager();

        /** @var Ledger $ledger */
        $ledger = $this->em->getRepository(Ledger::class)->findAll()[0];

        $payload = [
            'transactionId' => uuid_create(UUID_TYPE_RANDOM),
            'ledgerId' => (string) $ledger->getId(),
            'type' => 'credit',
            'amount' => '50.00',
            'currency' => 'EUR',
        ];

        $client->request(
            'POST',
            '/api/transactions',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API-TOKEN' => $token,],
            json_encode($payload)
        );

        $response = $client->getResponse();
        // dd($response);
        $this->assertEquals(Response::HTTP_ACCEPTED, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('accepted', $response->getContent());
    }

//    public function testDuplicateTransactionIsIgnored(): void
//    {
//        $client = static::createClient();
//        $this->loadFixtures();
//
//        /** @var Ledger $ledger */
//        $ledger = $this->em->getRepository(Ledger::class)->findAll()[0];
//        $transactionId = uuid_create(UUID_TYPE_RANDOM);
//
//        $payload = [
//            'transactionId' => $transactionId,
//            'ledgerId' => (string) $ledger->getId(),
//            'type' => 'credit',
//            'amount' => '25.00',
//            'currency' => 'EUR',
//        ];
//
//        // First request
//        $client->request(
//            'POST',
//            '/api/transactions',
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json'],
//            json_encode($payload)
//        );
//
//        $this->assertEquals(Response::HTTP_ACCEPTED, $client->getResponse()->getStatusCode());
//
//        // Duplicate request
//        $client->request(
//            'POST',
//            '/api/transactions',
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json'],
//            json_encode($payload)
//        );
//
//        $response = $client->getResponse();
//        $this->assertEquals(Response::HTTP_ACCEPTED, $response->getStatusCode());
//        $this->assertJson($response->getContent());
//        $this->assertStringContainsString('accepted', $response->getContent());
//
//
//        sleep(1);
//
//        // Validate that only one transaction was recorded
//        $transactions = $this->em->getRepository(Transaction::class)
//            ->findBy(['ledger' => $ledger]);
//
//        $this->assertCount(1, $transactions);
//    }
}

