<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Application\Ledger\Handler\GetLedgerBalancesHandler;
use App\Application\Ledger\Query\GetLedgerBalancesQuery;
use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Shared\ValueObject\Currency;
use App\Tests\KernelTestCaseWithFixtures;

class GetLedgerBalancesQueryHandlerTest extends KernelTestCaseWithFixtures
{
    public function testItReturnsLedgerBalances(): void
    {
        // Load a ledger from fixtures
        /** @var Ledger $ledger */
        $ledger = $this->em->getRepository(Ledger::class)->findAll()[0];
        $ledgerId = $ledger->getId();

        $query = new GetLedgerBalancesQuery($ledgerId);

        /** @var GetLedgerBalancesHandler $handler */
        $handler = self::getContainer()->get(GetLedgerBalancesHandler::class);
        $result = $handler($query);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $balance = $result[0];

        $this->assertArrayHasKey('currency', $balance);
        $this->assertArrayHasKey('amount', $balance);

        $this->assertEquals(Currency::EUR->value, $balance['currency']);
        $this->assertEquals("0.00", $balance['amount']);
    }
}
