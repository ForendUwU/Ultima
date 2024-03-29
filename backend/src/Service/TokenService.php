<?php

namespace App\Service;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService
{
    private const PRIVATE_KEY = 'WHFZQGXm#k$mBzX]A0f(=g^GbcFz5,~zUQY:$kGdEvu((%s*EmSRQFJ[/#qW^';
    private const ALGORITHM = 'HS256';

    public function createToken(User $user): string
    {
        $tokenCreationDate = new \DateTimeImmutable();

        $payload = [
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'email' => $user->getEmail(),
            //TODO change role
            'role' => $user->getRoles(),
            'tokenCreationDate' => $tokenCreationDate->format('Y-m-d H:i:s')
        ];

        return $this->encode($payload);
    }

    private function encode(array $payload): string
    {
        return JWT::encode($payload, self::PRIVATE_KEY, self::ALGORITHM);
    }

    public function decode(string $token): \stdClass
    {
        return JWT::decode($token, new Key(self::PRIVATE_KEY, self::ALGORITHM));
    }

    public function decodeLongToken(string $token): \stdClass
    {
        return JWT::decode(substr($token,7), new Key(self::PRIVATE_KEY, self::ALGORITHM));
    }
}
