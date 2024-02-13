<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class AuthorizationService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TokenService $tokenService
    ) {

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

        $token = $this->tokenService->createToken($user);
        $user->setToken($token);
        $this->em->flush();

        return array(
            'content' => [
                'token' => $token
            ],
            'code' => Response::HTTP_OK
        );
    }

    public function register(string $login, string $password, string $email, string $nickname): array
    {
        $newUser = new User();

        $newUser->setLogin($login);
        $newUser->setPassword($password);
        $newUser->setEmail($email);
        $newUser->setNickname($nickname);
        $newUser->setRoles(['ROLE_USER']);

        $token = $this->tokenService->createToken($newUser);
        $newUser->setToken($token);

        $this->em->persist($newUser);
        try {
            $this->em->flush();
        }
        catch (UniqueConstraintViolationException $e) {
            return array(
                'content' => [
                    'message' => 'This login already exists',
                ],
                'code' => Response::HTTP_UNAUTHORIZED
            );
        }

        return array(
            'content' => [
                'token' => $token,
            ],
            'code' => Response::HTTP_OK
        );
    }

    public function logout(string $token): array
    {
        $decodedToken = $this->tokenService->decode($token);
        $userLogin = $decodedToken->login;

        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $userLogin]);

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
