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
        $user->setToken($token);
        $this->em->flush();

        return new JsonResponse([
            'result' => 'success',
            'token' => $token
        ]);
    }

    public function register($data): JsonResponse
    {
        $newUser = new User();
        if (!$data['login'] || !$data['password'] || !$data['email'] || !$data['nickname']){
            return new JsonResponse([
                'result' => 'fail',
                'message' => 'Missing data',
            ], 401);
        }

        $newUser->setLogin($data['login']);
        $newUser->setPassword($data['password']);
        $newUser->setEmail($data['email']);
        $newUser->setNickname($data['nickname']);

        $token = $this->createToken($newUser);
        $newUser->setToken($token);
        $newUser->setRoles(['ROLE_USER']);

        $this->em->persist($newUser);
        $this->em->flush();

        return new JsonResponse([
            'result' => 'success',
            'token' => $token
        ]);
    }

    public function logout($data): JsonResponse
    {
        if (!$data['userId']) {
            return new JsonResponse([
                'result' => 'fail',
                'message' => 'Missing user id',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $data['userId']]);

        if (!$user) {
            return new JsonResponse([
                'result' => 'fail',
                'message' => 'User does not exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$user->getToken()){
            return new JsonResponse([
                'result' => 'fail',
                'message' => 'User already unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $user->setToken(null);
        $this->em->flush();

        return new JsonResponse([
            'result' => 'success',
            'message' => 'Logout successfully',
        ], Response::HTTP_OK);
    }
}