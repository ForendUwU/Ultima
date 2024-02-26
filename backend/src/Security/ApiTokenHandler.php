<?php

namespace App\Security;

use App\Entity\User;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\ExpiredException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TokenService $tokenService
    ) {

    }

    /**
     * @throws \Exception
     */
    public function getUserBadgeFrom(#[\SensitiveParameter]string $accessToken): UserBadge
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['token' => $accessToken]);

        if (!$user)
        {
            throw new BadCredentialsException();
        }

        $decodedToken = $this->tokenService->decode($accessToken);
        $tokenTime = new \DateTimeImmutable($decodedToken->tokenCreationDate);

        if ($tokenTime->modify('+1 day') <= new \DateTimeImmutable())
        {
            throw new ExpiredException('Token expired');
        }

        return new UserBadge($user->getLogin());
    }
}
