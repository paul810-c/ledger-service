<?php

namespace App\DataFixtures;

use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Ledger\Entity\LedgerBalance;
use App\Domain\Shared\ValueObject\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LedgerBalanceFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var Ledger $ledger */
        $ledger = $this->getReference(LedgerFixture::LEDGER_REFERENCE, Ledger::class);
        $balance = LedgerBalance::create($ledger, Currency::EUR);

        $manager->persist($balance);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LedgerFixture::class,
        ];
    }
}
