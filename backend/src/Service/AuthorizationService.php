<?php

namespace App\Service;

use App\Entity\User;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthorizationService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TokenService $tokenService,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {

    }

    /**
     * @throws \Exception
     */
    public function login(string $login, string $password): string
    {
        $user = $this->em->getRepository(User::class)->findByLogin($login);

        if (!$this->userPasswordHasher->isPasswordValid($user, $password)){
            throw new \Exception('Wrong login or password', Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->tokenService->createToken($user);
        $user->setToken($token);
        $this->em->flush();

        return $token;
    }

    /**
     * @throws \Exception
     */
    public function logout(string $userLogin): string
    {
        $user = $this->em->getRepository(User::class)->findByLogin($userLogin);

        if (!$user->getToken()){
            throw new \Exception('User already unauthorized', Response::HTTP_FORBIDDEN);
        }

        $user->setToken(null);
        $this->em->flush();

        return 'Logout successfully';
    }

    /**
     * @throws \Exception
     */
    public function register(string $login, string $password, string $email, string $nickname): string
    {
        $this->validatePassword($password);

        $newUser = new User();

        $newUser->setLogin($login);
        $newUser->setEmail($email);
        $newUser->setNickname($nickname);
        $newUser->setPassword(
            $this->userPasswordHasher->hashPassword(
                $newUser,
                $password
            )
        );

        $newUser->setRoles(['ROLE_USER']);

        $token = $this->tokenService->createToken($newUser);
        $newUser->setToken($token);

        $this->em->persist($newUser);
        $this->em->flush();

        return $token;
    }

    /**
     * @throws \Exception
     */
    public function validatePassword(string $password): bool
    {
        if (strlen($password) < 6) {
            throw new ValidationException('Password must contain 6 or more characters', Response::HTTP_UNAUTHORIZED);
        } elseif (strlen($password) > 50) {
            throw new ValidationException('Password must contain less than 50 characters', Response::HTTP_UNAUTHORIZED);
        } elseif (!preg_match("/^[a-zA-Z0-9!~_&*%@$]+$/", $password)) {
            throw new ValidationException(
                'Password must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters',
                Response::HTTP_UNAUTHORIZED
            );
        } elseif (!preg_match("/\d/", $password)) {
            throw new ValidationException('Password must contain at least one number', Response::HTTP_UNAUTHORIZED);
        } elseif (!preg_match("/[!~_&*%@$]/", $password)) {
            throw new ValidationException(
                'Password must contain at least one of this symbols "!", "~", "_", "&", "*", "%", "@", "$"',
                Response::HTTP_UNAUTHORIZED
            );
        } else {
            return true;
        }
    }
}
