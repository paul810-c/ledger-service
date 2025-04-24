<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public const API_TOKEN_HEADER = 'X-API-TOKEN';

    public function __construct(private readonly string $validToken)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::API_TOKEN_HEADER);
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get(self::API_TOKEN_HEADER);

        if ($token !== $this->validToken) {
            throw new AuthenticationException('Invalid API token');
        }

        return new SelfValidatingPassport(
            new UserBadge('service', function () {
                return new class implements UserInterface {
                    public function getRoles(): array
                    {
                        return ['ROLE_API'];
                    }

                    public function getPassword(): ?string
                    {
                        return null;
                    }

                    public function getSalt(): ?string
                    {
                        return null;
                    }

                    public function getUserIdentifier(): string
                    {
                        return 'service';
                    }

                    public function eraseCredentials(): void {}
                };
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Authentication Failed', Response::HTTP_UNAUTHORIZED);
    }
}
