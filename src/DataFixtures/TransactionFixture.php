<?php

namespace App\DataFixtures;

use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Transaction\Entity\Transaction;
use App\Domain\Transaction\ValueObject\TransactionType;
use App\Domain\Shared\ValueObject\Money;
use App\Domain\Shared\ValueObject\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Ramsey\Uuid\Uuid;

class TransactionFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
//        /** @var Ledger $ledger */
//        $ledger = $this->getReference(LedgerFixture::LEDGER_REFERENCE, Ledger::class);
//        $transaction = Transaction::create(
//            Uuid::uuid4()->toString(),
//            $ledger,
//            TransactionType::CREDIT,
//            new Money(5000, Currency::EUR)
//        );
//
//        $manager->persist($transaction);
//        $manager->flush();
    }

    public function getDependencies(): array
    {
//        return [
//            LedgerFixture::class,
//        ];
    }
}