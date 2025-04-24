<?php

namespace App\DataFixtures;

use App\Domain\Ledger\Entity\Ledger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LedgerFixture extends Fixture
{
    public const LEDGER_REFERENCE = 'test-ledger';

    public function load(ObjectManager $manager): void
    {
        $ledger = Ledger::create();
        $manager->persist($ledger);

        $this->addReference(self::LEDGER_REFERENCE, $ledger);
        $manager->flush();
    }
}
