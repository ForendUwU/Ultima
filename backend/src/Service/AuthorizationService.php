<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthorizationService
{
    use ResetDatabase;

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
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $login]);

        if (!$user) {
            throw new \Exception('This user does not exist', Response::HTTP_BAD_REQUEST);
        }

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
            throw new \Exception('Password must contain 6 or more characters');
        } elseif (strlen($password) > 50) {
            throw new \Exception('Password must contain less than 50 characters');
        } elseif (!preg_match("/^[a-zA-Z0-9!~_&*%@$]+$/", $password)) {
            throw new \Exception('Password must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
        } elseif (!preg_match("/[0-9]/", $password)) {
            throw new \Exception('Password must contain at least one number');
        } elseif (!preg_match("/[!~_&*%@$]/", $password)) {
            throw new \Exception('Password must contain at least one of this symbols "!", "~", "_", "&", "*", "%", "@", "$"');
        } else {
            return true;
        }
    }
}
