<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

readonly class LedgerId
{
    #[ORM\Column(type: 'uuid')]
    public string $id;

    public function __construct(?string $id = null)
    {
        $this->id = $id ?? Uuid::uuid4()->toString();
    }

    public function __toString(): string
    {
        return $this->id;
    }
}