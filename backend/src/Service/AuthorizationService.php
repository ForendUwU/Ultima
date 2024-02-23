<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Zenstruck\Foundry\Test\ResetDatabase;

class AuthorizationService
{
    use ResetDatabase;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TokenService $tokenService
    ) {

    }

    /**
     * @throws \Exception
     */
    public function login(string $login, string $password): string
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $login]);

        if (!$user) {
            throw new \Exception('This user does not exist', Response::HTTP_BAD_REQUEST);
        }

        if ($user->getPassword() !== $password){
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
    public function logout(string $token): string
    {
        $decodedToken = $this->tokenService->decodeLongToken($token);
        $userLogin = $decodedToken->login;

        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $userLogin]);

        if (!$user) {
            throw new \Exception('User does not exist', Response::HTTP_BAD_REQUEST);
        }

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
        $newUser = new User();

        $newUser->setLogin($login);
        $newUser->setPassword($password);
        $newUser->setEmail($email);
        $newUser->setNickname($nickname);
        $newUser->setRoles(['ROLE_USER']);

        $token = $this->tokenService->createToken($newUser);
        $newUser->setToken($token);

        $this->em->persist($newUser);
        $this->em->flush();

        return $token;
    }
}
