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

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {

    }

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

    public function login(string $login, string $password): array
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $login]);

        if (!$user) {
            return array(
                'content' => [
                    'message' => 'This user does not exist',
                ],
                'code' => Response::HTTP_BAD_REQUEST
            );
        }

        if ($user->getPassword() !== $password){
            return array(
                'content' => [
                    'message' => 'Wrong login or password',
                ],
                'code' => Response::HTTP_UNAUTHORIZED
            );
        }

        $token = $this->createToken($user);
        $user->setToken($token);
        $this->em->flush();

        return array(
            'content' => [
                'token' => $token,
                'userId' => $user->getId()
            ],
            'code' => Response::HTTP_OK
        );
    }

    public function registration(string $login, string $password, string $email, string $nickname): array
    {
        $newUser = new User();

        $newUser->setLogin($login);
        $newUser->setPassword($password);
        $newUser->setEmail($email);
        $newUser->setNickname($nickname);

        $token = $this->createToken($newUser);
        $newUser->setToken($token);
        $newUser->setRoles(['ROLE_USER']);

        $this->em->persist($newUser);
        $this->em->flush();

        return array(
            'content' => [
                'token' => $token
            ],
            'code' => Response::HTTP_OK
        );
    }

    public function logout(string $userId): array
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $userId]);

        if (!$user) {
            return array(
                'content' => [
                    'message' => 'User does not exist'
                ],
                'code' => Response::HTTP_BAD_REQUEST
            );
        }

        if (!$user->getToken()){
            return array(
                'content' => [
                    'message' => 'User already unauthorized'
                ],
                'code' => Response::HTTP_FORBIDDEN
            );
        }

        $user->setToken(null);
        $this->em->flush();

        return array(
            'content' => [
                'message' => 'Logout successfully'
            ],
            'code' => Response::HTTP_OK
        );
    }
}