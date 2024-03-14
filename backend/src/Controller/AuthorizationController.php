<?php

namespace App\Controller;

use App\Exceptions\ValidationException;
use App\Service\TokenService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AuthorizationService;

#[AsController]
class AuthorizationController extends AbstractController
{
    public function __construct(
        private readonly AuthorizationService $authorizationService,
        private TokenService $tokenService
    ) {

    }

    /**
     * @throws \Exception
     */
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
            properties: [new OA\Property(property: 'token', type: 'string', example: 'token')],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message1', type: 'string', example: 'Missing data'),
                new OA\Property(property: 'message2', type: 'string', example: 'This user does not exist')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Wrong login or password'),
            ],
            type: 'object'
        )
    )]
    #[Tag('Authorization')]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['login'] || !$data['password']){
            return new JsonResponse(
                    [
                        'message' => 'Missing data'
                    ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $result = $this->authorizationService->login($data['login'], $data['password']);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return new JsonResponse(
            [
                'token' => $result,
            ],
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/logout",
        methods: ['POST']
    )]
    #[OA\Response(
        response: 200,
        description: 'Logout successfully',
        content: new OA\JsonContent(
            properties: [new OA\Property(property: 'message', type: 'string', example: 'Logout success')],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message1', type: 'string', example: 'Missing token'),
                new OA\Property(property: 'message2', type: 'string', example: 'User does not exist'),
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'User already unauthorized'),
            ],
            type: 'object'
        )
    )]
    #[Tag('Authorization')]
    public function logout(Request $request): ?JsonResponse
    {
        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        try {
            $result = $this->authorizationService->logout($decodedToken->login);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return new JsonResponse(
            [
            'message' => $result
            ],
            Response::HTTP_OK
        );
    }

    #[Route(
        "/api/register",
        name: 'app_register',
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
    #[Parameter(
        name: 'email',
        description: 'User email',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'test_email'
    )]
    #[Parameter(
        name: 'nickname',
        description: 'User nickname',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'test_nickname'
    )]
    #[OA\Response(
        response: 200,
        description: 'Register successfully',
        content: new OA\JsonContent(
            properties: [new OA\Property(property: 'token', type: 'string', example: 'token')],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Missing data'),
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'This login is already in use'),
            ],
            type: 'object'
        )
    )]
    #[Tag('Authorization')]
    public function register(Request $request): ?JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['login'] || !$data['password'] || !$data['email'] || !$data['nickname']){
            return new JsonResponse(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $result = $this->authorizationService->register(
                $data['login'],
                $data['password'],
                $data['email'],
                $data['nickname']
            );
        } catch (UniqueConstraintViolationException $e) {
            return new JsonResponse(
                [
                    'message' => 'This login is already in use'
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (ValidationException $e) {
            return new JsonResponse(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return new JsonResponse(
            [
                'token' => $result
            ],
            Response::HTTP_OK
        );
    }
}
