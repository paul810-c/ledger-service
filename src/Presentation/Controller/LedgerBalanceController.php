<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Ledger\Query\GetLedgerBalancesQuery;
use App\Domain\Shared\ValueObject\LedgerId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Ledger')]
class LedgerBalanceController extends AbstractController
{
    #[Route('/api/balances/{ledgerId}', name: 'get_ledger_balances', methods: ['GET'])]
    #[OA\Get(
        path: '/api/balances/{ledgerId}',
        description: 'Returns an array of balances for the specified ledger id',
        summary: 'Get all balances for a given ledger id',
        tags: ['Ledger'],
        parameters: [
            new OA\Parameter(
                name: 'ledgerId',
                description: 'UUID of the ledger',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Balances retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                            new OA\Property(property: 'balanceInCents', type: 'string', example: '1000.00'),
                        ],
                        type: 'object'
                    )
                )
            )
        ]
    )]
    public function __invoke(string $ledgerId, MessageBusInterface $queryBus): JsonResponse
    {
        $query = new GetLedgerBalancesQuery(new LedgerId($ledgerId));
        $balances = $queryBus->dispatch($query);

        $handledStamp = $balances->last(HandledStamp::class);

        return $this->json($handledStamp?->getResult() ?? [], JsonResponse::HTTP_OK);
    }
}