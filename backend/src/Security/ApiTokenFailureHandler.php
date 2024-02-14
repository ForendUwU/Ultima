<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class ApiTokenFailureHandler implements AuthenticationFailureHandlerInterface
{

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new Response(
            json_encode([
                'result' => 'fail',
                'message' => 'Unauthorized'
            ])
        );
    }
}
