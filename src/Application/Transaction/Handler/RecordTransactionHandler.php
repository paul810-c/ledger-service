<?php

declare(strict_types=1);

namespace App\Application\Transaction\Handler;

use App\Application\Transaction\Command\RecordTransactionCommand;
use App\Domain\Ledger\Entity\LedgerBalance;
use App\Domain\Ledger\Repository\LedgerBalanceRepositoryInterface;
use App\Domain\Ledger\Repository\LedgerRepositoryInterface;
use App\Domain\Shared\ValueObject\Money;
use App\Domain\Transaction\Entity\Transaction;
use App\Domain\Transaction\Repository\TransactionRepositoryInterface;
use App\Domain\Transaction\ValueObject\TransactionType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RecordTransactionHandler
{
    public function __construct(
        private readonly LedgerRepositoryInterface $ledgerRepository,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly LedgerBalanceRepositoryInterface $balanceRepository,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(RecordTransactionCommand $command): void
    {
        if ($this->transactionRepository->exists($command->transactionId)) {
            $this->logger->info('TRANSACTION RECORD HANDLER: Duplicate transaction', $this->context($command));
            return;
        }

        $this->em->beginTransaction();

        try {
            $ledger = $this->ledgerRepository->findOrFail($command->ledgerId);

            $transaction = Transaction::create(
                $command->transactionId,
                $ledger,
                $command->type,
                new Money($command->amountInCents, $command->currency)
            );
            $this->transactionRepository->save($transaction);

            $adjustment = $command->type === TransactionType::CREDIT
                ? $command->amountInCents
                : -$command->amountInCents;

            $balance = $this->balanceRepository->findOrCreateForUpdate(
                $ledger,
                $command->currency->value
            );

            $balance->updateBalance($adjustment);
            $this->balanceRepository->save($balance);

            $this->em->flush();
            $this->em->commit();

            $this->logger->info('TRANSACTION RECORD HANDLER: Transaction successfully recorded', $this->context($command));
        } catch (\DomainException $e) {
            $this->em->rollback();
            $this->logger->warning('TRANSACTION RECORD HANDLER: Transaction denied: ' . $e->getMessage(), $this->context($command));
            throw $e;
        } catch (\Throwable $e) {
            $this->em->rollback();
            $this->logger->error('TRANSACTION RECORD HANDLER: Transaction failed', [
                ...$this->context($command),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function context(RecordTransactionCommand $command): array
    {
        return [
            'transactionId' => $command->transactionId,
            'ledgerId' => (string) $command->ledgerId,
            'amountInCents' => $command->amountInCents,
            'currency' => $command->currency->value,
            'type' => $command->type->value,
        ];
    }
}
