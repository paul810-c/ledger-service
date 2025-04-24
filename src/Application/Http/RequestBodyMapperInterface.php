<?php

declare(strict_types=1);

namespace App\Application\Http;

use Symfony\Component\HttpFoundation\Request;

interface RequestBodyMapperInterface
{
    public function map(Request $request, string $dtoClass): object;
}
