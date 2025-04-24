<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Http\RequestBodyMapperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestBodyMapper implements RequestBodyMapperInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {}

    public function map(Request $request, string $dtoClass): object
    {
        try {
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                $dtoClass,
                'json'
            );
        } catch (\Throwable $e) {
            throw new BadRequestHttpException(sprintf('Invalid JSON: %s', $e->getMessage()));
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $messages[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }

            throw new BadRequestHttpException(json_encode(['errors' => $messages]));
        }

        return $dto;
    }
}
