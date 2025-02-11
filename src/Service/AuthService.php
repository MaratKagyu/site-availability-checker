<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    public function __construct(
        private readonly string $authPassphrase
    )
    {
    }

    public function requireAuthentication(Request $request): void
    {
        if ($request->query->get('auth') === $this->authPassphrase) {
            return;
        }

        $requestData = $request->getPayload()->all();

        if (($requestData['auth'] ?? '') === $this->authPassphrase) {
            return;
        }

        throw new HttpException(
            401,
            'Not Authorized',
        );
    }


}
