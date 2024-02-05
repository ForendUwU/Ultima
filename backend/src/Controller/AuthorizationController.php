<?php

namespace App\Controller;

use App\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\AuthorizationService;

#[AsController]
class AuthorizationController extends AbstractController
{

    public function __construct(private AuthorizationService $authorizationService)
    { }

    #[Route(
        "/api/login",
        methods: ['POST']
    )]
    #[Parameter(
        name: 'login',
        description: 'User login',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'test_login'
    )]
    #[Parameter(
        name: 'password',
        description: 'User password',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'test_password'
    )]
    #[OA\Response(
        response: 200,
        description: 'Successfully authorized',
        content: new OA\JsonContent(
            properties: [new OA\Property(property: 'result', type: 'string', example: 'success')],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Missing credentials',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'result', type: 'string', example: 'fail'),
                new OA\Property(property: 'message', type: 'string', example: 'missing credentials'),
            ],
            type: 'object'
        )
    )]
    #[Tag('Authorization')]
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        return $this->authorizationService->login($data);
    }

    #[Route(
        "/api/logout",
        methods: ['POST']
    )]
    #[Tag('Authorization')]
    public function logout(Request $request): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->authorizationService->logout($data);
    }

    #[Route(
        "/api/register",
        name: 'app_register',
        methods: ['POST']
    )]
    #[Tag('Authorization')]
    public function register(Request $request): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->authorizationService->register($data);
    }
}
