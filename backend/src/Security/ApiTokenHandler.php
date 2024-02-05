<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }
    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['token' => $accessToken]);
        if (!$user)
        {
            throw new BadCredentialsException();
        }

//        if (!$user->getToken())
//        {
//            throw new CustomUserMessageAuthenticationException('Token expired');
//        }

        return new UserBadge($user->getLogin());
    }
}
