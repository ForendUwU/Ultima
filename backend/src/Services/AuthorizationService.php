<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationService
{
    private const PRIVATE_KEY = 'WHFZQGXm#k$mBzX]A0f(=g^GbcFz5,~zUQY:$kGdEvu((%s*EmSRQFJ[/#qW^';
    private const ALGORITHM = 'HS256';

    public function __construct(private EntityManagerInterface $em)
    { }

    public function createToken(User $user): string
    {
        $payload = [
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'email' => $user->getEmail(),
            //TODO change role .
            'role' => $user->getRoles()
        ];

        return JWT::encode($payload, self::PRIVATE_KEY, self::ALGORITHM);
    }
    public function login($data): JsonResponse
    {
        if (!$data['login'] || !$data['password']) {
            return new JsonResponse([
                'result' => 'fail',
                'message' => 'Missing credentials',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $data['login']]);

        if (!$user) {
            return new JsonResponse([
                'result' => 'fail',
                'message' => 'This user does not exist'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($user->getPassword() !== $data['password']){
            return new JsonResponse([
                'result' => 'fail',
                'message' => 'Wrong login or password'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->createToken($user);

        return new JsonResponse([
            'result' => 'success',
            'token' => $token
        ]);
    }

    public function register($request): JsonResponse
    {
        $newUser = new User();
        $data = json_decode($request->getContent(), true);
        if ($data['login'] || $data['password']){
            return new JsonResponse([
                'result' => 'fail',
                'message' => 'missing credentials',
            ], 401);
        }
        return new JsonResponse(['result' => 'success']);
    }
}