<?php

declare(strict_types=1);

namespace App\Application\Ledger\Handler;

use App\Application\Ledger\Command\CreateLedgerCommand;
use App\Domain\Ledger\Entity\Ledger;
use App\Domain\Ledger\Repository\LedgerRepositoryInterface;
use App\Domain\Shared\ValueObject\LedgerId;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateLedgerHandler
{
    public function __construct(
        private readonly LedgerRepositoryInterface $ledgerRepository,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(CreateLedgerCommand $command): LedgerId
    {
        $ledger = Ledger::create();
        $this->ledgerRepository->save($ledger);
        $this->em->flush();

        $this->logger->info('Ledger created', ['ledger_id' => $ledger->getId()]);
        return $ledger->getId();
    }
}
