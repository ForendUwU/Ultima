<?php

namespace App\Factory;

use App\Entity\PurchasedGame;
use App\Repository\PurchasedGameRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<PurchasedGame>
 *
 * @method        PurchasedGame|Proxy                     create(array|callable $attributes = [])
 * @method static PurchasedGame|Proxy                     createOne(array $attributes = [])
 * @method static PurchasedGame|Proxy                     find(object|array|mixed $criteria)
 * @method static PurchasedGame|Proxy                     findOrCreate(array $attributes)
 * @method static PurchasedGame|Proxy                     first(string $sortedField = 'id')
 * @method static PurchasedGame|Proxy                     last(string $sortedField = 'id')
 * @method static PurchasedGame|Proxy                     random(array $attributes = [])
 * @method static PurchasedGame|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PurchasedGameRepository|RepositoryProxy repository()
 * @method static PurchasedGame[]|Proxy[]                 all()
 * @method static PurchasedGame[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static PurchasedGame[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static PurchasedGame[]|Proxy[]                 findBy(array $attributes)
 * @method static PurchasedGame[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static PurchasedGame[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PurchasedGameFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'game' => GameFactory::new(),
            'hoursOfPlaying' => self::faker()->randomFloat(),
            'user' => UserFactory::new(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(PurchasedGame $purchasedGame): void {})
            ;
    }

    protected static function getClass(): string
    {
        return PurchasedGame::class;
    }
}
