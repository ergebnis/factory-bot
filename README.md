# factory-bot

[![Integrate](https://github.com/ergebnis/factory-bot/workflows/Integrate/badge.svg)](https://github.com/ergebnis/factory-bot/actions)
[![Prune](https://github.com/ergebnis/factory-bot/workflows/Prune/badge.svg)](https://github.com/ergebnis/factory-bot/actions)
[![Release](https://github.com/ergebnis/factory-bot/workflows/Release/badge.svg)](https://github.com/ergebnis/factory-bot/actions)
[![Renew](https://github.com/ergebnis/factory-bot/workflows/Renew/badge.svg)](https://github.com/ergebnis/factory-bot/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/factory-bot/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/factory-bot)
[![Type Coverage](https://shepherd.dev/github/ergebnis/factory-bot/coverage.svg)](https://shepherd.dev/github/ergebnis/factory-bot)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/factory-bot/v/stable)](https://packagist.org/packages/ergebnis/factory-bot)
[![Total Downloads](https://poser.pugx.org/ergebnis/factory-bot/downloads)](https://packagist.org/packages/ergebnis/factory-bot)

Provides a fixture factory for [`doctrine/orm`](https://github.com/doctrine/orm) entities.

## Installation

Run

```shell
composer require --dev ergebnis/factory-bot
```

## Usage

The entry point of `ergebnis/factory-bot` is the [`FixtureFactory`](src/FixtureFactory.php).

You will use the fixture factory to create entity definitions and to create Doctrine entities populated with fake data.

- [Examples](#examples)
- [Creating a fixture factory](#creating-a-fixture-factory)
- [Creating entity definitions](#creating-entity-definitions)
- [Loading entity definitions](#loading-entity-definitions)
- [Registering entity definitions](#registering-entity-definitions)
- [Creating entities](#creating-entities)
- [Persisting entities](#persisting-entities)
- [Flushing entities](#flushing-entities)

### Examples

You can find examples in

- the directory [`example/`](example/)
- the repository [`ergebnis/factory-bot-example`](https://github.com/ergebnis/factory-bot-example)

### Creating a fixture factory

The fixture factory requires an instance of `Doctrine\ORM\EntityManagerInterface` (for reading class metadata from Doctrine entities, and for persisting Doctrine entities when necessary) and an instance of `Faker\Generator` for generating fake data.

```php
<?php

use Doctrine\ORM;
use Ergebnis\FactoryBot;
use Faker\Factory;

$entityManager = ORM\EntityManager::create(...);
$faker = Factory::create(...);

$fixtureFactory = new FactoryBot\FixtureFactory(
    $entityManager,
    $faker
);
```

To simplify the creation of a fixture factory in tests, you can create an [abstract test case](example/test/Unit/AbstractTestCase.php) with access to an entity manager, a faker, and a fixture factory.

```php
<?php

namespace App\Test\Functional;

use Doctrine\ORM;
use Ergebnis\FactoryBot;
use Faker\Generator;
use PHPUnit\Framework;

abstract class AbstractTestCase extends Framework\TestCase
{
    final protected static function entityManager(): ORM\EntityManagerInterface
    {
          // create entity manager from configuration or fetch it from container

          return $entityManager;
    }

    final protected static function faker(): Generator
    {
        $faker = Factory::create();

        $faker->seed(9001);

        return $faker;
    }

    final protected static function fixtureFactory(): FactoryBot\FixtureFactory
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            static::entityManager(),
            static::faker()
        );

        // create or load entity definitions

        return $fixtureFactory;
    }
}
```

### Creating entity definitions

Now that you have access to a fixture factory, you can create definitions for Doctrine entities.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class);
```

This simple definition might work when all entity fields have default values, but typically, you will want to provide a map of entity field names to field definitions.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'avatar' => FactoryBot\FieldDefinition::reference(Entity\Avatar::class),
    'id' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
        return $faker->uuid;
    }),
    'location' => FactoryBot\FieldDefinition::optionalClosure(static function (Generator $faker): string {
        return $faker->city;
    }),
    'login' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
        return $faker->userName;
    }),
]);
```

In addition to the map of field names to field definitions, you can specify a closure that the fixture factory will invoke after creating the entity. The closure accepts the freshly created entity and the map of field names to field values that the fixture factory used to populate the entity.

```php
<?php

$closure = static function (object $entity, array $fieldValues): void {
    // ...
};
```

:bulb: You can use the closure to modify the freshly created entity.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(
    Entity\User::class,
    [
        'avatar' => FactoryBot\FieldDefinition::reference(Entity\Avatar::class),
        'id' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
            return $faker->uuid;
        }),
        'location' => FactoryBot\FieldDefinition::optionalClosure(static function (Generator $faker): string {
            return $faker->city;
        }),
        'login' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
            return $faker->userName;
        }),
    ],
    static function (Entity\User $user, array $fieldValues): void {
        if (is_string($fieldValues['location')) {
            // ...
        }
    }
);
```

#### Field Definitions

A field definition can be

- an implementation of [`FieldDefinition\Resolvable`](src/FieldDefinition/Resolvable.php)
- a closure (will be normalized to [`FieldDefinition\Closure`](src/FieldDefinition/Closure.php))
- an arbitrary value (will be normalized to [`FieldDefinition\Value`](src/FieldDefinition/Value.php))

You can use the [`FieldDefinition`](src/FieldDefinition.php) factory to create field definitions shipped with this package or implement the `FieldDefinition\Resolvable` interface yourself.

:bulb: Custom field definitions can be useful when you are dealing with identical field definitions over and over again.

#### Non-nullable fields

When you are working with non-nullable fields, you can use the following field definitions, all of which will resolve to concrete references or values:

- [`FieldDefinition::closure()`](#fielddefinitionclosure)
- [`FieldDefinition::reference()`](#fielddefinitionreference)
- [`FieldDefinition::references()`](#fielddefinitionreferences)
- [`FieldDefinition::sequence()`](#fielddefinitionsequence)
- [`FieldDefinition::value()`](#fielddefinitionvalue)

#### Nullable fields

When you are working with nullable fields, you can use the following field definitions, all of which will either resolve to `null` or to a concrete reference or value (depending on the [strategy](#creating-entities):

- [`FieldDefinition::optionalClosure()`](#fielddefinitionoptionalclosure)
- [`FieldDefinition::optionalReference()`](#fielddefinitionoptionalreference)
- [`FieldDefinition::optionalSequence()`](#fielddefinitionoptionalsequence)
- [`FieldDefinition::optionalValue()`](#fielddefinitionoptionalvalue)

##### `FieldDefinition::closure()`

`FieldDefinition::closure()` accepts a closure.

```php
<?php

use Ergebnis\FactoryBot;
use Faker\Generator;

$closure = static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory) {
    // return whatever makes sense
};
```

The fixture factory will resolve the field definition to the return value of invoking the closure with the instance of `Faker\Generator` composed into the fixture factory, and the fixture factory itself.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'id' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
        return $faker->uuid;
    }),
    'organizations' => FactoryBot\FieldDefinition::closure(static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory): array {
        return $fixtureFactory->createMany(
            Entity\Organization::class,
            FactoryBot\Count::exact($faker->numberBetween(
                1,
                5
            ))
        );
    }),
]);

/** @var Entity\User $user */
$user = $fixtureFactory->createOne(Entity\User::class);

var_dump($user->id());            // string
var_dump($user->organizations()); // array with 1-5 instances of Entity\Organization
```

:bulb: It is possible to specify a closure only (will be normalized to `FieldDefinition\Closure`):

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'id' => static function (Generator $faker): string {
        return $faker->uuid;
    },
    'organizations' => static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory): array {
        return $fixtureFactory->createMany(
            Entity\Organization::class,
            FactoryBot\Count::exact($faker->numberBetween(
                1,
                5
            ))
        );
    },
]);

/** @var Entity\User $user */
$user = $fixtureFactory->createOne(Entity\User::class);

var_dump($user->id());            // string
var_dump($user->organizations()); // array with 1-5 instances of Entity\Organization
```

##### `FieldDefinition::optionalClosure()`

`FieldDefinition::optionalClosure()` accepts a closure.

```php
<?php

use Ergebnis\FactoryBot;
use Faker\Generator;

$closure = static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory) {
    // return whatever makes sense
};
```

A fixture factory using the [`Strategy\DefaultStrategy`](#strategydefaultstrategy) will resolve the field definition to `null` or to the return value of invoking the closure with the instance of `Faker\Generator` composed into the fixture factory.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'location' => FactoryBot\FieldDefinition::optionalClosure(static function (Generator $faker): string {
        return $faker->city;
    }),
]);

/** @var Entity\User $user */
$user = $fixtureFactory->createOne(Entity\User::class);

var_dump($user->location()); // null or a random city
```

A fixture factory using the [`Strategy\WithOptionalStrategy`](#strategywithoptionalstrategy) will resolve the field definition to the return value of invoking the closure with the instance of `Faker\Generator` composed into the fixture factory.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'location' => FactoryBot\FieldDefinition::optionalClosure(static function (Generator $faker): string {
        return $faker->city;
    }),
]);

$withOptionalFixtureFactory = $fixtureFactory->withOptional();

/** @var Entity\User $user */
$user = $withOptionalFixtureFactory->createOne(Entity\User::class);

var_dump($user->location()); // a random city
```

A fixture factory using the [`Strategy\WithoutOptionalStrategy`](#strategywithoutoptionalstrategy) will resolve the field definition to `null`.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'location' => FactoryBot\FieldDefinition::optionalClosure(static function (Generator $faker): string {
        return $faker->city;
    }),
]);

$withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

/** @var Entity\User $user */
$user = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

var_dump($user->location()); // null
```

##### `FieldDefinition::reference()`

`FieldDefinition::reference()` accepts the class name of an entity or embeddable, and optionally an array of field definition overrides.

Every fixture factory will resolve the field definition to an instance of the entity or embeddable class populated through the fixture factory.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'avatar' => FactoryBot\FieldDefinition::reference(Entity\Avatar::class),
]);

/** @var Entity\User $user */
$user = $fixtureFactory->createOne(Entity\User::class);

var_dump($user->avatar()); // an instance of Entity\Avatar
```

When field definition overrides are specified, they will be used to override exsting field definitions of the referenced entity.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'avatar' => FactoryBot\FieldDefinition::reference(
        Entity\Avatar::class,
        [
            'height' => 300,
            'width' => 200,
        ]
    ),
]);

/** @var Entity\User $user */
$user = $fixtureFactory->createOne(Entity\User::class);

var_dump($user->avatar()); // an instance of Entity\Avatar with height set to 300 and width set to 200
```

:exclamation: When resolving the reference, the fixture factory needs to be aware of the referenced entity or embeddable.

##### `FieldDefinition::optionalReference()`

`FieldDefinition::optionalReference()` accepts the class name of an entity or embeddable, and optionally an array of field definition overrides.

A fixture factory using the [`Strategy\DefaultStrategy`](#strategydefaultstrategy) will resolve the field definition to `null` or an instance of the entity or embeddable class populated through the fixture factory.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\Repository::class, [
    'template' => FactoryBot\FieldDefinition::optionalReference(Entity\Repository::class),
]);

/** @var Entity\Repository $repository */
$repository = $fixtureFactory->createOne(Entity\Repository::class);

var_dump($repository->template()); // null or an instance of Entity\Repository
```

A fixture factory using the [`Strategy\WithOptionalStrategy`](#strategywithoptionalstrategy) will resolve the field definition to an instance of the entity or embeddable class populated through the fixture factory.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\Repository::class, [
    'template' => FactoryBot\FieldDefinition::optionalReference(Entity\Repository::class),
]);

$withOptionalFixtureFactory = $fixtureFactory->withOptional();

/** @var Entity\Repository $repository */
$repository = $withOptionalFixtureFactory->createOne(Entity\Repository::class);

var_dump($repository->template()); // an instance of Entity\Repository
```

A fixture factory using the [`Strategy\WithoutOptionalStrategy`](#strategywithoutoptionalstrategy) will resolve the field definition to `null`.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\Repository::class, [
    'template' => FactoryBot\FieldDefinition::optionalReference(Entity\Repository::class),
]);

$withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

/** @var Entity\Repository $repository */
$repository = $withoutOptionalFixtureFactory->createOne(Entity\Repository::class);

var_dump($repository->template()); // null
```

:exclamation: When resolving the reference, the fixture factory needs to be aware of the referenced entity or embeddable.

##### `FieldDefinition::references()`

`FieldDefinition::references()` accepts the class name of an entity or embeddable, the count of desired references, and optionally an array of field definition overrides.

You can create the count from an exact number, or minimum and maximum values.

```php
<?php

use Ergebnis\FactoryBot;

$count = FactoryBot\Count::exact(5);

$otherCount = FactoryBot\Count::between(
    0,
    20
);
```

:bulb: When you create the count from minimum and maximum values, the fixture factory will resolve its actual value before creating references. This way, you can have variation in the number of references - any number between the minimum and maximum can be assumed.

A fixture factory using the [`Strategy\DefaultStrategy`](#strategydefaultstrategy) will resolve the field definition to an array with zero or more instances of the entity or embeddable class populated through the fixture factory. Depending on the value of `$count`, the array might be empty.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\Organization::class, [
    'members' => FactoryBot\FieldDefinition::references(
        Entity\User::class,
        FactoryBot\Count::exact(5)
    ),
    'repositories' => FactoryBot\FieldDefinition::references(
        Entity\Repository::class,
        FactoryBot\Count::between(0, 20),
        [
            'codeOfConduct' => null,
        ]
    ),
]);

/** @var Entity\Organization $organization */
$organization = $fixtureFactory->createOne(Entity\Organization::class);

var_dump($organization->members());      // array with 5 instances of Entity\User
var_dump($organization->repositories()); // array with 0-20 instances of Entity\Repository
```

A fixture factory using the [`Strategy\WithOptionalStrategy`](#strategywithoptionalstrategy) will resolve the field definition to an array containing at least one instance of the entity or embeddable class populated through the fixture factory, unless `$count` uses an exact value, see [`FixtureFactory::createMany()`](#fixturefactorycreatemany).

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\Organization::class, [
    'members' => FactoryBot\FieldDefinition::references(
        Entity\User::class,
        FactoryBot\Count::exact(5)
    ),
    'repositories' => FactoryBot\FieldDefinition::references(
        Entity\Repository::class,
        FactoryBot\Count::between(0, 20)
    ),
]);

$withOptionalFixtureFactory = $fixtureFactory->withOptional();

/** @var Entity\Organization $organization */
$organization = $withOptionalFixtureFactory->createOne(Entity\Organization::class);

var_dump($organization->members());      // array with 5 instances of Entity\User
var_dump($organization->repositories()); // array with 1-20 instances of Entity\Repository
```

A fixture factory using the [`Strategy\WithoutOptionalStrategy`](#strategywithoutoptionalstrategy) will resolve the field definition to an empty array, unless `$count` uses an exact value, see [`FixtureFactory::createMany()`](#fixturefactorycreatemany).

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\Organization::class, [
    'members' => FactoryBot\FieldDefinition::references(
        Entity\User::class,
        FactoryBot\Count::exact(5)
    ),
    'repositories' => FactoryBot\FieldDefinition::references(
        Entity\Repository::class,
        FactoryBot\Count::between(0, 20)
    ),
]);

$withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

/** @var Entity\Organization $organization */
$organization = $withoutOptionalFixtureFactory->createOne(Entity\Organization::class);

var_dump($organization->members());      // array with 5 instances of Entity\User
var_dump($organization->repositories()); // empty array
```

:exclamation: When resolving the references, the fixture factory needs to be aware of the referenced entity or embeddable.

##### `FieldDefinition::sequence()`

`FieldDefinition::sequence()` accepts a string containing the `%d` placeholder at least once and an optional initial number (defaults to `1`).

Every fixture factory will resolve the field definition by replacing all occurrences of the placeholder `%d` in the string with the sequential number's current value. The sequential number will then be incremented by `1` for the next run.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'login' => FactoryBot\FieldDefinition::sequence(
        'user-%d',
        1
    ),
]);

/** @var Entity\User $userOne */
$userOne = $fixtureFactory->createOne(Entity\User::class);

/** @var Entity\User $userTwo */
$userTwo = $fixtureFactory->createOne(Entity\User::class);

var_dump($userOne->login()); // 'user-1'
var_dump($userTwo->login()); // 'user-2'
```

##### `FieldDefinition::optionalSequence()`

`FieldDefinition::optionalSequence()` accepts a string containing the `%d` placeholder at least once and an optional initial number (defaults to `1`).

A fixture factory using the [`Strategy\DefaultStrategy`](#strategydefaultstrategy) will resolve the field definition to `null` or by replacing all occurrences of the placeholder `%d` in the string with the sequential number's current value. The sequential number will then be incremented by `1` for the next run.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'location' => FactoryBot\FieldDefinition::optionalSequence(
        'City %d',
        1
    ),
]);

/** @var Entity\User $userOne */
$userOne = $fixtureFactory->createOne(Entity\User::class);

/** @var Entity\User $userTwo */
$userTwo = $fixtureFactory->createOne(Entity\User::class);

var_dump($userOne->location()); // null or 'City 1'
var_dump($userTwo->location()); // null or 'City 1' or 'City 2'
```

A fixture factory using the [`Strategy\WithOptionalStrategy`](#strategywithoptionalstrategy) will resolve the field definition by replacing all occurrences of the placeholder `%d` in the string with the sequential number's current value. The sequential number will then be incremented by `1` for the next run.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'location' => FactoryBot\FieldDefinition::optionalSequence(
        'City %d',
        1
    ),
]);

$withOptionalFixtureFactory = $fixtureFactory->withOptional();

/** @var Entity\User $userOne */
$userOne = $withOptionalFixtureFactory->createOne(Entity\User::class);

/** @var Entity\User $userTwo */
$userTwo = $withOptionalFixtureFactory->createOne(Entity\User::class);

var_dump($userOne->location()); // 'City 1'
var_dump($userTwo->location()); // 'City 2'
```

A fixture factory using the [`Strategy\WithoutOptionalStrategy`](#strategywithoutoptionalstrategy) will resolve the field definition to `null`.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'location' => FactoryBot\FieldDefinition::optionalSequence(
        'City %d',
        1
    ),
]);

$withOptionalFixtureFactory = $fixtureFactory->withOptional();

/** @var Entity\User $userOne */
$userOne = $withOptionalFixtureFactory->createOne(Entity\User::class);

/** @var Entity\User $userTwo */
$userTwo = $withOptionalFixtureFactory->createOne(Entity\User::class);

var_dump($userOne->location()); // null
var_dump($userTwo->location()); // null
```

##### `FieldDefinition::value()`

`FieldDefinition::value()` accepts an arbitrary value.

The fixture factory will resolve the field definition to the value.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'login' => FactoryBot\FieldDefinition::value('localheinz'),
]);

/** @var Entity\User $user */
$user = $fixtureFactory->createOne(Entity\User::class);

var_dump($user->login()); // 'localheinz'
```

:bulb: It is also possible to specify a value only:

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'login' => 'localheinz',
]);

/** @var Entity\User $user */
$user = $fixtureFactory->createOne(Entity\User::class);

var_dump($user->login()); // 'localheinz'
```

##### `FieldDefinition::optionalValue()`

`FieldDefinition::optionalValue()` accepts an arbitrary value.

A fixture factory using the [`Strategy\DefaultStrategy`](#strategydefaultstrategy) will resolve the field definition to `null` or the value.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'location' => FactoryBot\FieldDefinition::optionalValue('Berlin'),
]);

/** @var Entity\User $user */
$user = $fixtureFactory->create(Entity\User::class);

var_dump($user->location()); // null or 'Berlin'
```

A fixture factory using the [`Strategy\WithOptionalStrategy`](#strategywithoptionalstrategy) will resolve the field definition to the value.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'location' => FactoryBot\FieldDefinition::optionalValue('Berlin'),
]);

$withOptionalFixtureFactory = $fixtureFactory->withOptional();

/** @var Entity\User $user */
$user = $withOptionalFixtureFactory->create(Entity\User::class);

var_dump($user->location()); // 'Berlin'
```

A fixture factory using the [`Strategy\WithoutOptionalStrategy`](#strategywithoutoptionalstrategy) will resolve the field definition to `null`.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'location' => FactoryBot\FieldDefinition::optionalValue('Berlin'),
]);

$withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

/** @var Entity\User $user */
$user = $withoutOptionalFixtureFactory->create(Entity\User::class);

var_dump($user->location()); // null
```

### Loading entity definitions

Instead of creating entity definitions inline, you can implement the [`EntityDefinitionProvider`](src/EntityDefinitionProvider.php) interface and load entity definitions contained within a directory with the fixture factory.

First, create concrete definition providers.

```php
<?php

namespace Example\Test\Fixture\Entity;

use Ergebnis\FactoryBot;
use Example\Entity;

final class UserDefinitionProvider implements FactoryBot\EntityDefinitionProvider
{
    public function accept(FactoryBot\FixtureFactory $fixtureFactory): void
    {
        $fixtureFactory->define(Entity\User::class, [
            // ...
        ]);
    }
}
```

:bulb: While you can use a single entity definition provider to provide definitions for all entities, I recommend using one definition provider per entity. Then you can quickly implement an [auto-review test](example/test/AutoReview/FixtureTest.php) to enforce that an entity definition provider exists for each entity.

Second, adjust your abstract test case to load definitions from entity definition providers contained in a directory.

```php
<?php

namespace App\Test\Functional;

use Ergebnis\FactoryBot;
use PHPUnit\Framework;

abstract class AbstractTestCase extends Framework\TestCase
{
    // ...

    final protected static function fixtureFactory(): FactoryBot\FixtureFactory
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            static::entityManager(),
            static::faker()
        );

        $fixtureFactory->load(__DIR__ . '/../Fixture');

        return $fixtureFactory;
    }

    // ...
}
```

### Registering entity definitions

Instead of loading entity definition providers contained within a directory with the fixture factory, you can also register entity definition providers that you have already instantiated.

```php
<?php

namespace App\Test\Functional;

use Ergebnis\FactoryBot;
use Example\Test\Fixture;
use PHPUnit\Framework;

abstract class AbstractTestCase extends Framework\TestCase
{
    // ...

    final protected static function fixtureFactory(): FactoryBot\FixtureFactory
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            static::entityManager(),
            static::faker()
        );

        $fixtureFactory->register(new Fixture\UserDefinitionProvider());

        return $fixtureFactory;
    }

    // ...
}
```

### Creating entities

Now that you have created (or loaded) entity definitions, you can create Doctrine entities populated with fake data.

The fixture factory allows to create entities using the following strategies:

[`Strategy\DefaultStrategy`](#strategydefaultstrategy)
[`Strategy\WithOptionalStrategy`](#strategywithoptionalstrategy)
[`Strategy\WithoutOptionalStrategy`](#strategywithoutoptionalstrategy)

#### `Strategy\DefaultStrategy`

The `Strategy\DefaultStrategy` involves random behavior, and based on randomness, the fixture factory might or might not resolve optional field references:

- [`FieldDefinition::optionalClosure()`](#fielddefinitionoptionalclosure) might be resolved to `null` or a concrete value
- [`FieldDefinition::optionalReference()`](#fielddefinitionoptionalreference) might be resolved to `null` or a concrete reference
- [`FieldDefinition::optionalSequence()`](#fielddefinitionoptionalsequence) might be resolved to `null` or a concrete value
- [`FieldDefinition::optionalValue()`](#fielddefinitionoptionalvalue) might be resolved to `null` or a concrete value
- [`FieldDefinition::references()`](#fielddefinitionreferences) might be resolved to an `array` of zero or more references

The fixture factory uses the `Strategy\DefaultStrategy` by default.

#### `Strategy\WithOptionalStrategy`

The `Strategy\WithOptionalStrategy` involves random behavior, but the fixture factory will resolve optional field references:

- [`FieldDefinition::optionalClosure()`](#fielddefinitionoptionalclosure) will be resolved to a concrete value
- [`FieldDefinition::optionalReference()`](#fielddefinitionoptionalreference) will be resolved to a concrete reference
- [`FieldDefinition::optionalSequence()`](#fielddefinitionoptionalsequence) will be resolved to a concrete value
- [`FieldDefinition::optionalValue()`](#fielddefinitionoptionalvalue) will be resolved to a concrete value
- [`FieldDefinition::references()`](#fielddefinitionreferences) will be resolved to an `array` containing at least one reference, unless `$count` uses an exact value, see [`FixtureFactory::createMany()`](#fixturefactorycreatemany)

To create a fixture factory using the `Strategy\WithOptionalStrategy` out of an available fixture factory, invoke `withOptional()`:

```php
<?php

use Ergebnis\FactoryBot;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$withOptionalFixtureFactory = $fixtureFactory->withOptional();
```

#### `Strategy\WithoutOptionalStrategy`

The `Strategy\WithoutOptionalStrategy` involves random behavior, but the fixture factory will not resolve optional field references:

- [`FieldDefinition::optionalClosure()`](#fielddefinitionoptionalclosure) will be resolved to `null`
- [`FieldDefinition::optionalReference()`](#fielddefinitionoptionalreference) will be resolved to `null`
- [`FieldDefinition::optionalSequence()`](#fielddefinitionoptionalsequence) will be resolved to `null`
- [`FieldDefinition::optionalValue()`](#fielddefinitionoptionalvalue) will be resolved to `null`
- [`FieldDefinition::references()`](#fielddefinitionreferences) will be resolved to an empty `array`, unless `$count` uses an exact value, see [`FixtureFactory::createMany()`](#fixturefactorycreatemany)

To create a fixture factory using the `Strategy\WithoutOptionalStrategy` out of an available fixture factory, invoke `withoutOptional()`:

```php
<?php

use Ergebnis\FactoryBot;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();
```

#### `FixtureFactory::createOne()`

`FixtureFactory::createOne()` accepts the class name of an entity and optionally, a map of entity field names to field definitions that should override the field definitions for that specific entity.

The fixture factory will return a single entity.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'login' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
        return $faker->userName;
    }),
]);

/** @var Entity\User $userOne */
$userOne = $fixtureFactory->createOne(Entity\User::class);

/** @var Entity\User $userTwo */
$userTwo = $fixtureFactory->createOne(Entity\User::class, [
    'login' => FactoryBot\FieldDefinition::value('localheinz'),
]);

/** @var Entity\User $userThree */
$userThree = $fixtureFactory->createOne(Entity\User::class, [
    'login' => 'ergebnis-bot',
]);

var_dump($userOne->login());   // random user name
var_dump($userTwo->login());   // 'localheinz'
var_dump($userThree->login()); // 'ergebnis-bot'
```

A field definition override can be

- an implementation of [`FieldDefinition\Resolvable`](src/FieldDefinition/Resolvable.php)
- a closure (will be normalized to `FieldDefinition\Closure`)
- an arbitrary value (will be normalized to `FieldDefinition\Value`)

Also see [Creating entity definitions](#creating-entity-definitions).

#### `FixtureFactory::createMany()`

`FixtureFactory::createMany()` accepts the class name of an entity, the count of desired entities, and an optional map of entity field names to field definitions that should override the field definitions for that specific entity.

You can create the count from an exact number:

```php
<?php

use Ergebnis\FactoryBot;

$count = FactoryBot\Count::exact(5);
```

The fixture factory will resolve `$count` to `5`.

You can also create the count from minimum and maximum values.

```php
<?php

use Ergebnis\FactoryBot;

$count = FactoryBot\Count::between(
    0,
    20
);
```

The fixture factory will resolve `$count` to any number between `0` and `20`.

The fixture factory will return an array of entities.

```php
<?php

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$fixtureFactory->define(Entity\User::class, [
    'login' => FieldDefinition\Closure(static function (Generator $faker): string {
        return $faker->username;
    }),
]);

/** @var array<Entity\User> $users */
$users = $fixtureFactory->createMany(
    Entity\User::class,
    FactoryBot\Count::exact(5)
);

/** @var array<Entity\User> $otherUsers */
$otherUsers = $fixtureFactory->createMany(
    Entity\User::class,
    FactoryBot\Count::exact(5),
    [
        'login' => FactoryBot\FieldDefinition::sequence('user-%d'),
    ]
);

$normalize = static function (array $users): array {
    return array_map(static function (Entity\User $user): string {
        return $user->login();
    }, $users);
};

var_dump($normalize($users));        // random user names
var_dump($normalize($otherUsers));   // 'user-1', 'user-2', ...
```

A field definition override can be

- an implementation of [`FieldDefinition\Resolvable`](src/FieldDefinition/Resolvable.php)
- a closure (will be normalized to `FieldDefinition\Closure`)
- an arbitrary value (will be normalized to `FieldDefinition\Value`)

Also see [Creating entity definitions](#creating-entity-definitions).

### Persisting entities

When the fixture factory creates entities, the fixture factory does not persist them by default.

To create a fixture factory that persists entities out of an available fixture factory, invoke `persisting()`:

```php
<?php

use Ergebnis\FactoryBot;

/** @var FactoryBot\FixtureFactory $fixtureFactory */
$persistingFixtureFactory = $fixtureFactory->persisting();
```

After this point, the fixture factory will automatically persist every entity it creates.

:exclamation: You need to flush the entity manager yourself.

### Flushing entities

The fixture factory will not flush the entity manager - you need to flush it yourself.

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).

## Credits

This project is based on [`breerly/factory-girl-php@0e6f1b6`](https://github.com/GoodPete/factory-girl-php/tree/0e6f1b6724d39108a2e7cef68a74668b7a77b856) (originally licensed under MIT by [Grayson Koonce](https://github.com/unhashable)), which is based on [`xi/doctrine`](https://github.com/xi-project/xi-doctrine) (originally licensed under MIT by [Xi](https://github.com/xi-project)), which in turn provided a port of [`factory_bot`](https://github.com/thoughtbot/factory_girl) (originally licensed under MIT by [Joe Ferris](https://github.com/jferris) and [thoughtbot, Inc.](https://github.com/thoughtbot)).

## Curious what I am building?

:mailbox_with_mail: [Subscribe to my list](https://localheinz.com/projects/), and I will occasionally send you an email to let you know what I am working on.
