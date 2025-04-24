<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Http\RequestBodyMapperInterface;
use App\Application\Transaction\Command\RecordTransactionCommand;
use App\Domain\Shared\ValueObject\Currency;
use App\Domain\Shared\ValueObject\LedgerId;
use App\Domain\Transaction\ValueObject\TransactionType;
use App\Presentation\Dto\RecordTransactionRequest;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Transaction')]
class TransactionController extends AbstractController
{
    #[Route('/api/transactions', name: 'record_transaction', methods: ['POST'])]
    #[OA\Post(
        path: '/api/transactions',
        description: 'Creates a debit or credit transaction and updates the corresponding balance',
        summary: 'Record a new transaction',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['transactionId', 'ledgerId', 'type', 'amount', 'currency'],
                properties: [
                    new OA\Property(property: 'transactionId', type: 'string', format: 'uuid', example: 'de305d54-75b4-431b-adb2-eb6b9e546014'),
                    new OA\Property(property: 'ledgerId', type: 'string', format: 'uuid', example: '123e4567-e89b-12d3-a456-426614174000'),
                    new OA\Property(property: 'type', type: 'string', enum: ['debit', 'credit']),
                    new OA\Property(property: 'amount', type: 'string', example: '100.00'),
                    new OA\Property(property: 'currency', type: 'string', example: 'EUR')
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 202,
                description: 'Transaction accepted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'accepted')
                    ]
                )
            )
        ]
    )]
    public function record(
        Request $request,
        MessageBusInterface $bus,
        RequestBodyMapperInterface $mapper
    ): JsonResponse {
        $dto = $mapper->map($request, RecordTransactionRequest::class);

        $command = new RecordTransactionCommand(
            $dto->transactionId,
            new LedgerId($dto->ledgerId),
            TransactionType::from($dto->type),
            $dto->getAmountInCents(),
            Currency::from($dto->currency),
        );

        $bus->dispatch($command);

        return $this->json([ 'status' => 'accepted' ], JsonResponse::HTTP_ACCEPTED);
    }
}