<?php

namespace App\Factory;

use App\Entity\Token;
use App\Repository\TokenRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Token>
 *
 * @method        Token|Proxy                     create(array|callable $attributes = [])
 * @method static Token|Proxy                     createOne(array $attributes = [])
 * @method static Token|Proxy                     find(object|array|mixed $criteria)
 * @method static Token|Proxy                     findOrCreate(array $attributes)
 * @method static Token|Proxy                     first(string $sortedField = 'id')
 * @method static Token|Proxy                     last(string $sortedField = 'id')
 * @method static Token|Proxy                     random(array $attributes = [])
 * @method static Token|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TokenRepository|RepositoryProxy repository()
 * @method static Token[]|Proxy[]                 all()
 * @method static Token[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Token[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Token[]|Proxy[]                 findBy(array $attributes)
 * @method static Token[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Token[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TokenFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'ownedBy' => UserFactory::new(),
            'scopes' => []
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Token $token): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Token::class;
    }
}
