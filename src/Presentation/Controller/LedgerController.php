<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Ledger\Command\CreateLedgerCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Ledger')]
class LedgerController extends AbstractController
{
    #[Route('/api/ledgers', name: 'create_ledger', methods: ['POST'])]
    #[OA\Post(
        path: '/api/ledgers',
        description: 'Generates a new ledger id',
        summary: 'Create a new ledger',
        tags: ['Ledger'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Ledger created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: '0f8fad5b-d9cb-469f-a165-70867728950e')
                    ]
                )
            )
        ]
    )]
    public function createLedger(
        MessageBusInterface $bus,
    ): JsonResponse {
        $command = new CreateLedgerCommand();
        $envelope = $bus->dispatch($command);
        $handledStamp = $envelope->last(HandledStamp::class);
        $ledgerId = $handledStamp?->getResult();

        return $this->json([
            'id' => (string) $ledgerId,
        ], JsonResponse::HTTP_CREATED);
    }
}
