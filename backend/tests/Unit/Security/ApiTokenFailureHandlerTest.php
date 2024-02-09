<?php

namespace App\Tests\Unit\Security;

use App\Security\ApiTokenFailureHandler;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;

class ApiTokenFailureHandlerTest extends TestCase
{
    public function testGetUserBadgeFrom(): void
    {
        $apiTokenFailureHandler = new ApiTokenFailureHandler();

        $testRequest = new Request();
        $testAuthException = new AuthenticationException();

        $result = $apiTokenFailureHandler->onAuthenticationFailure($testRequest, $testAuthException);
        $this->assertEquals(
            new Response(
                json_encode([
                    'result' => 'fail',
                    'message' => 'Unauthorized'
                ])
            ), $result
        );
    }
}