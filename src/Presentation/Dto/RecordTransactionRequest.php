<?php

declare(strict_types=1);

namespace App\Presentation\Dto;

use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Transaction\ValueObject\TransactionType;
use Brick\Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

class RecordTransactionRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $transactionId;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $ledgerId;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TransactionType::class, 'types'])]
    public string $type;

    //    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/', message: 'Amount must be a decimal')]
    #[Assert\NotBlank]
    public string $amount;

    #[Assert\Choice(callback: [Currency::class, 'values'])]
    public string $currency;

    public function getAmountInCents(): int
    {
        return Money::of($this->amount, strtoupper($this->currency))
            ->getMinorAmount()
            ->toInt();
    }
}