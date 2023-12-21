<?php

namespace App\Factory;

use App\Entity\Games;
use App\Repository\GamesRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Games>
 *
 * @method        Games|Proxy                     create(array|callable $attributes = [])
 * @method static Games|Proxy                     createOne(array $attributes = [])
 * @method static Games|Proxy                     find(object|array|mixed $criteria)
 * @method static Games|Proxy                     findOrCreate(array $attributes)
 * @method static Games|Proxy                     first(string $sortedField = 'id')
 * @method static Games|Proxy                     last(string $sortedField = 'id')
 * @method static Games|Proxy                     random(array $attributes = [])
 * @method static Games|Proxy                     randomOrCreate(array $attributes = [])
 * @method static GamesRepository|RepositoryProxy repository()
 * @method static Games[]|Proxy[]                 all()
 * @method static Games[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Games[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Games[]|Proxy[]                 findBy(array $attributes)
 * @method static Games[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Games[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class GamesFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'description' => self::faker()->text(255),
            'isPublished' => self::faker()->boolean(),
            'name' => self::faker()->text(255),
            'price' => self::faker()->randomFloat(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Games $games): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Games::class;
    }
}
