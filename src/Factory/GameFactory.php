<?php

namespace App\Factory;

use App\Entity\Game;
use App\Repository\GamesRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Game>
 *
 * @method        Game|Proxy                      create(array|callable $attributes = [])
 * @method static Game|Proxy                      createOne(array $attributes = [])
 * @method static Game|Proxy                      find(object|array|mixed $criteria)
 * @method static Game|Proxy                      findOrCreate(array $attributes)
 * @method static Game|Proxy                      first(string $sortedField = 'id')
 * @method static Game|Proxy                      last(string $sortedField = 'id')
 * @method static Game|Proxy                      random(array $attributes = [])
 * @method static Game|Proxy                      randomOrCreate(array $attributes = [])
 * @method static GamesRepository|RepositoryProxy repository()
 * @method static Game[]|Proxy[]                  all()
 * @method static Game[]|Proxy[]                  createMany(int $number, array|callable $attributes = [])
 * @method static Game[]|Proxy[]                  createSequence(iterable|callable $sequence)
 * @method static Game[]|Proxy[]                  findBy(array $attributes)
 * @method static Game[]|Proxy[]                  randomRange(int $min, int $max, array $attributes = [])
 * @method static Game[]|Proxy[]                  randomSet(int $number, array $attributes = [])
 */
final class GameFactory extends ModelFactory
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
            'description' => self::faker()->text(255),
            'price' => self::faker()->randomFloat(),
            'title' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Game $game): void {})
            ;
    }

    protected static function getClass(): string
    {
        return Game::class;
    }
}
