<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Ledger\Entity\LedgerBalance;
use App\Domain\Ledger\Repository\LedgerBalanceRepositoryInterface;
use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Shared\ValueObject\LedgerId;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

class DoctrineLedgerBalanceRepository implements LedgerBalanceRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function save(LedgerBalance $balance): void
    {
        $this->em->persist($balance);
    }

    public function find(Ledger $ledger, Currency $currency): ?LedgerBalance
    {
        return $this->em->getRepository(LedgerBalance::class)->findOneBy([
            'ledger' => $ledger,
            'currency' => $currency,
        ]);
    }

    public function findByLedgerId(LedgerId $ledgerId): array
    {
        return $this->em->getRepository(LedgerBalance::class)->findBy([
            'ledger' => $ledgerId
        ]);
    }

    public function lockRowForUpdate(Ledger $ledger, string $currency): LedgerBalance
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('b')
            ->from(LedgerBalance::class, 'b')
            ->where('b.ledger = :ledger')
            ->andWhere('b.currency = :currency')
            ->setParameter('ledger', $ledger)
            ->setParameter('currency', $currency);

        return $qb->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getSingleResult();
    }

    public function updateLedgerBalance(
        Ledger $ledger,
        string $currency,
        int $adjustmentInCents
    ): void {
        $qb = $this->em->createQueryBuilder();

        $qb->update(LedgerBalance::class, 'lb')
            ->set('lb.balanceInCents', 'lb.balanceInCents + :adjustment')
            ->set('lb.updatedAt', ':updatedAt')
            ->where('lb.ledger = :ledger')
            ->andWhere('lb.currency = :currency')
            ->setParameter('adjustment', $adjustmentInCents)
            ->setParameter('updatedAt', new \DateTimeImmutable())
            ->setParameter('ledger', $ledger)
            ->setParameter('currency', $currency);

        $qb->getQuery()->execute();
    }

    public function findOrCreateForUpdate(Ledger $ledger, string $currency): LedgerBalance
    {
        $qb = $this->em->createQueryBuilder();

        try {
            return $qb->select('b')
                ->from(LedgerBalance::class, 'b')
                ->where('b.ledger = :ledger')
                ->andWhere('b.currency = :currency')
                ->setParameter('ledger', $ledger)
                ->setParameter('currency', $currency)
                ->getQuery()
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)
                ->getSingleResult();
        } catch (NoResultException) {
            try {
                $balance = LedgerBalance::create($ledger, $currency);
                $this->em->persist($balance);

                return $balance;
            } catch (UniqueConstraintViolationException) {
                $this->em->clear();

                return $this->lockRowForUpdate($ledger, $currency);
            }
        }
    }
}
