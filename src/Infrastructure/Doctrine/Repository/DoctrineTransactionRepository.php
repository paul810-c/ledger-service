<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Transaction\Entity\Transaction;
use App\Domain\Transaction\Repository\TransactionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function save(Transaction $transaction): void
    {
        $this->em->persist($transaction);
    }

    public function exists(string $transactionId): bool
    {
        return $this->em->createQueryBuilder()
                ->select('1')
                ->from(Transaction::class, 't')
                ->where('t.id = :id')
                ->setParameter('id', $transactionId)
                ->getQuery()
                ->getOneOrNullResult() !== null;
    }
}
