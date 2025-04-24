<?php

declare(strict_types=1);

namespace App\Application\Ledger\Handler;

use App\Application\Ledger\Query\GetLedgerBalancesQuery;
use App\Domain\Ledger\Entity\LedgerBalance;
use App\Domain\Ledger\Repository\LedgerBalanceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Brick\Money\Money;

#[AsMessageHandler]
final readonly class GetLedgerBalancesHandler
{
    public function __construct(
        private LedgerBalanceRepositoryInterface $balanceRepository
    ) {}

    public function __invoke(GetLedgerBalancesQuery $query): array
    {
        $balances = $this->balanceRepository->findByLedgerId($query->ledgerId);

        return array_map(function (LedgerBalance $balance) {
            $money = Money::ofMinor($balance->getBalanceInCents(), $balance->getCurrency());

            return [
                'currency' => $balance->getCurrency(),
                'amount' => (string) $money->getAmount(),
            ];
        }, $balances);
    }
}