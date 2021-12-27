# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`0.4.0...main`][0.4.0...main].

### Changed

- Dropped support for PHP 7.3 ([#682]), by [@localheinz]

## [`0.4.0`][0.4.0]

For a full diff see [`0.3.2...0.4.0`][0.3.2...0.4.0].

### Changed

* Required at least `doctrine/annotations:^1.10.3` ([#495]), by [@localheinz]
* Required at least `doctrine/collections:^1.6.5` ([#496]), by [@localheinz]
* Required at least `doctrine/orm:^2.8.0` ([#498]), by [@localheinz]
* Required at least `doctrine/dbal:^2.12.0` ([#499]), by [@localheinz]
* Added support for PHP 8.0 ([#481]), by [@localheinz]

### Fixed

* Dropped support for PHP 7.2 ([#493]), by [@localheinz]

## [`0.3.2`][0.3.2]

For a full diff see [`0.3.1...0.3.2`][0.3.1...0.3.2].

### Fixed

* Started using `fakerphp/faker` instead of `fzaninotto/faker` ([#459]), by [@localheinz]

## [`0.3.1`][0.3.1]

For a full diff see [`0.3.0...0.3.1`][0.3.0...0.3.1].

### Fixed

* Actually implemented `WithoutOptionalStrategy` ([#375]), by [@localheinz]

## [`0.3.0`][0.3.0]

For a full diff see [`0.2.1...0.3.0`][0.2.1...0.3.0].

### Added

* Implemented a `WithOptionalStrategy` ([#365]), by [@localheinz]
* Implemented a `WithoutOptionalStrategy` ([#369]), by [@localheinz]

### Changed

* Moved resolution of `Count` to `FixtureFactory` ([#351]), by [@localheinz]
* Extracted `DefaultStrategy` for resolving field values and count ([#353]), by [@localheinz]
* Replaced `FixtureFactory::persistAfterCreate()` and `FixtureFactory::doNotPersistAfterCreate()` with `FixtureFactory::persisting()`, a mutator that returns a persisting `FixtureFactory` ([#374]), by [@localheinz]

## [`0.2.1`][0.2.1]

For a full diff see [`0.2.0...0.2.1`][0.2.0...0.2.1].

### Changed

* Required `ergebnis/classy:^1.0.0` ([#338]), by [@localheinz]

## [`0.2.0`][0.2.0]

For a full diff see [`0.1.0...0.2.0`][0.1.0...0.2.0].

### Changed

* Renamed `InvalidDefinition::fromClassNameAndException()` to `InvalidDefinition::throwsExceptionDuringInstantiation()` ([#300]), by [@localheinz]
* Renamed `Number` to `Count` ([#309]), by [@localheinz]
* Split `FixtureFactory::persistOnGet()` into `FixtureFactory::persistAfterCreate()` and `FixtureFactory::doNotPersistAfterCreate()` ([#311]), by [@localheinz]
* Merged `Definitions` into `FixtureFactory::load()` ([#312]), by [@localheinz]
* Renamed `Definition` to `EntityDefinitionProvider` ([#314]), by [@localheinz]

### Fixed

* Started throwing an `InvalidDefinition` exception when a definition is concrete but cannot be instantiated ([#301]), by [@localheinz]
* Started throwing an `InvalidDefinition` exception when a definition cannot be autoloaded ([#302]), by [@localheinz]

### Removed

* Removed `FixtureFactory::definitions()` ([#321]), by [@localheinz]

## [`0.1.0`][0.1.0]

For a full diff see [`fa9c564...0.1.0`][fa9c564...0.1.0].

### Added

* Imported [`breerly/factory-girl-php@0e6f1b6`](https://github.com/GoodPete/factory-girl-php/tree/0e6f1b6724d39108a2e7cef68a74668b7a77b856) ([#1]), by [@localheinz]
* Imported [`ergebnis/factory-girl-definition@23e57bc`](https://github.com/ergebnis/factory-girl-definition/tree/23e57bc2105ac7a32e3ec7103c866899fe6ad20c) ([#6]), by [@localheinz]
* Allowed use of `Faker\Generator` in field definitions ([#144]), by [@localheinz]
* Added `FieldDefinition::value()` which allows resolving a field definition to a constant value ([#149]), by [@localheinz]
* Added `FieldDefinition::closure()` which allows resolving a field definition to the return value of a closure that is invoked with the `FixtureFactory` ([#155]), by [@localheinz]
* Allowed creation of optional field definitions ([#167]) and ([#196]), by [@localheinz]
* Allowed using field definitions as field overrides ([#270]), by [@localheinz]
* Allowed use of `Faker\Generator` in closure invoked after entity creation ([#287]), by [@localheinz]

### Changed

* Used `Doctrine\ORM\EntityManagerInterface` instead of `Doctrine\ORM\EntityManager` in type and return type declarations ([#24]), by [@localheinz]
* Marked all classes as `final` ([#33]), by [@localheinz]
* Marked `EntityDef` as internal ([#49]), by [@localheinz]
* Started throwing an `Exception\InvalidFieldNames` exception instead of a generic `Exception` when fields are referenced that are not present in the corresponding entity ([#87]), by [@localheinz]
* Renamed `EntityDef` to `EntityDefinition` ([#91]), by [@localheinz]
* Renamed `FieldDef` to `FieldDefinition` ([#92]), by [@localheinz]
* Turned `$configuration` parameter of `FixtureFactory::defineEntity()` into `$afterCreate`, a `Closure` that will be invoked after object construction ([#101]), by [@localheinz]
* Started throwing an `Exception\InvalidCount` exception instead of a generic `Exception` when an invalid number of entities are requested ([#105]), by [@localheinz]
* Started throwing an `Exception\EntityDefinitionAlreadyRegistered` exception instead of a generic `Exception` when an entity definition for a class name has already been registered ([#106]), by [@localheinz]
* Added `$faker` parameter to `Definition\Definition::accept()` and `Definition\Definitions::registerWith()`, providing and requiring to pass in an instance of `Faker\Generator` ([#117]), by [@localheinz]
* Started throwing an `Exception\ClassNotFound` exception instead of a generic `Exception` when a class was not found ([#125]), by [@localheinz]
* Added `@template` annotations to assist with static code analysis ([#128]), by [@localheinz]
* Removed the fluent interface from `FixtureFactory::defineEntity()` ([#131]), by [@localheinz]
* Extracted `FieldDefinition\Reference` ([#157]), by [@localheinz]
* Extracted `FieldDefinition\References` ([#159]), by [@localheinz]
* Extracted `FieldDefinition\Value` ([#160]), by [@localheinz]
* Extracted `FieldDefinition\Closure` ([#161]), by [@localheinz]
* Extracted `FieldDefinition\Sequence` ([#164]), by [@localheinz]
* Introduced named constructors for field definitions and marked primary constructor as `private` ([#188]), by [@localheinz]
* Renamed `FixtureFactory::get()` to `FixtureFactory::create()` ([#189]), by [@localheinz]
* Renamed `FixtureFactory::getList()` to `FixtureFactory::createMultiple()` ([#190]), by [@localheinz]
* Renamed `FixtureFactory::defineEntity()` to `FixtureFactory::define()` ([#197]), by [@localheinz]
* Extracted `FieldDefinition\Optional` ([#260]), by [@localheinz]
* Extracted `Count` ([#262]), by [@localheinz]
* Renamed `FixtureFactory::create()` to `FixtureFactory::createOne()` ([#263]), by [@localheinz]
* Renamed `FixtureFactory::createMultiple()` to `FixtureFactory::createMany()` ([#264]), by [@localheinz]
* Changed order of parameters for `FixtureFactory::createMany()` ([#266]), by [@localheinz]
* Renamed `Count` to `Number\Exact` and `InvalidCount` to `InvalidNumber` ([#273]), by [@localheinz]

### Fixed

* Populated embeddables and disallowed referencing fields using dot notation ([#79]), by [@localheinz]
* Started throwing an `Exception\ClassMetadataNotFound` exception instead of bubbling up `Doctrine\ORM\Mapping\MappingException` when a class is not an entity ([#126]), by [@localheinz]

### Removed

* Removed possibility to set the entity namespace on the `FixtureFactory` ([#3]), by [@localheinz]
* Removed `Provider\Doctrine\DBAL\Types\StatusArrayType` ([#13]), by [@localheinz]
* Removed `Doctrine\FieldDef::past()`, `Doctrine\FieldDef::future()`, and `Doctrine\DateIntervalHelper` ([#14]), by [@localheinz]
* Removed `Doctrine\ORM\Repository` along with locking capabilities ([#15]), by [@localheinz]
* Removed `Doctrine\ORM\QueryBuilder` ([#16]), by [@localheinz]
* Removed `Definition\AbstractDefinition` ([#114] and [#116]), by [@localheinz]
* Removed `Definition\FakerAwareDefinition` ([#120] and [#123]), by [@localheinz]
* Removed `FixtureFactory::provideWith()` ([#122]), by [@localheinz]
* Removed `FixtureFactory::getAsSingleton()`, `FixtureFactory::setSingleton()`, and `FixtureFactory::unsetSingleton()` ([#124]), by [@localheinz]
* Removed `callable` support for field definitions ([#133]) and ([#185]), by [@localheinz]
* Removed support for `string` sequences that do not contain a `%d` placeholder ([#185]), by [@localheinz]
* Removed `FieldDefinition::optionalReferences()` and `FieldDefinition\References::optional()` ([#259]), by [@localheinz]
* Removed parameter `$faker` from `Definition::registerWith()` ([#286]), by [@localheinz]

[0.1.0]: https://github.com/ergebnis/factory-bot/releases/tag/0.1.0
[0.2.0]: https://github.com/ergebnis/factory-bot/releases/tag/0.2.0
[0.2.1]: https://github.com/ergebnis/factory-bot/releases/tag/0.2.1
[0.3.0]: https://github.com/ergebnis/factory-bot/releases/tag/0.3.0
[0.3.1]: https://github.com/ergebnis/factory-bot/releases/tag/0.3.1
[0.3.2]: https://github.com/ergebnis/factory-bot/releases/tag/0.3.2
[0.4.0]: https://github.com/ergebnis/factory-bot/releases/tag/0.4.0

[fa9c564...0.1.0]: https://github.com/ergebnis/factory-bot/compare/fa9c564...0.1.0
[0.1.0...0.2.0]: https://github.com/ergebnis/factory-bot/compare/0.1.0...0.2.0
[0.2.0...0.2.1]: https://github.com/ergebnis/factory-bot/compare/0.2.0...0.2.1
[0.2.1...0.3.0]: https://github.com/ergebnis/factory-bot/compare/0.2.1...0.3.0
[0.3.0...0.3.1]: https://github.com/ergebnis/factory-bot/compare/0.3.0...0.3.1
[0.3.1...0.3.2]: https://github.com/ergebnis/factory-bot/compare/0.3.1...0.3.2
[0.3.2...0.4.0]: https://github.com/ergebnis/factory-bot/compare/0.3.2...0.4.0
[0.4.0...main]: https://github.com/ergebnis/factory-bot/compare/0.4.0...main

[#1]: https://github.com/ergebnis/factory-bot/pull/1
[#3]: https://github.com/ergebnis/factory-bot/pull/3
[#6]: https://github.com/ergebnis/factory-bot/pull/6
[#13]: https://github.com/ergebnis/factory-bot/pull/13
[#14]: https://github.com/ergebnis/factory-bot/pull/14
[#15]: https://github.com/ergebnis/factory-bot/pull/15
[#16]: https://github.com/ergebnis/factory-bot/pull/16
[#24]: https://github.com/ergebnis/factory-bot/pull/24
[#33]: https://github.com/ergebnis/factory-bot/pull/33
[#49]: https://github.com/ergebnis/factory-bot/pull/49
[#79]: https://github.com/ergebnis/factory-bot/pull/79
[#87]: https://github.com/ergebnis/factory-bot/pull/87
[#91]: https://github.com/ergebnis/factory-bot/pull/91
[#92]: https://github.com/ergebnis/factory-bot/pull/92
[#101]: https://github.com/ergebnis/factory-bot/pull/101
[#105]: https://github.com/ergebnis/factory-bot/pull/105
[#106]: https://github.com/ergebnis/factory-bot/pull/106
[#114]: https://github.com/ergebnis/factory-bot/pull/114
[#116]: https://github.com/ergebnis/factory-bot/pull/116
[#117]: https://github.com/ergebnis/factory-bot/pull/117
[#120]: https://github.com/ergebnis/factory-bot/pull/120
[#122]: https://github.com/ergebnis/factory-bot/pull/122
[#123]: https://github.com/ergebnis/factory-bot/pull/123
[#124]: https://github.com/ergebnis/factory-bot/pull/124
[#125]: https://github.com/ergebnis/factory-bot/pull/125
[#126]: https://github.com/ergebnis/factory-bot/pull/126
[#128]: https://github.com/ergebnis/factory-bot/pull/128
[#131]: https://github.com/ergebnis/factory-bot/pull/131
[#133]: https://github.com/ergebnis/factory-bot/pull/133
[#144]: https://github.com/ergebnis/factory-bot/pull/144
[#149]: https://github.com/ergebnis/factory-bot/pull/149
[#155]: https://github.com/ergebnis/factory-bot/pull/155
[#157]: https://github.com/ergebnis/factory-bot/pull/157
[#159]: https://github.com/ergebnis/factory-bot/pull/159
[#160]: https://github.com/ergebnis/factory-bot/pull/160
[#161]: https://github.com/ergebnis/factory-bot/pull/161
[#164]: https://github.com/ergebnis/factory-bot/pull/164
[#167]: https://github.com/ergebnis/factory-bot/pull/167
[#185]: https://github.com/ergebnis/factory-bot/pull/185
[#188]: https://github.com/ergebnis/factory-bot/pull/188
[#189]: https://github.com/ergebnis/factory-bot/pull/189
[#190]: https://github.com/ergebnis/factory-bot/pull/190
[#196]: https://github.com/ergebnis/factory-bot/pull/196
[#197]: https://github.com/ergebnis/factory-bot/pull/197
[#259]: https://github.com/ergebnis/factory-bot/pull/259
[#260]: https://github.com/ergebnis/factory-bot/pull/260
[#262]: https://github.com/ergebnis/factory-bot/pull/262
[#263]: https://github.com/ergebnis/factory-bot/pull/263
[#264]: https://github.com/ergebnis/factory-bot/pull/264
[#266]: https://github.com/ergebnis/factory-bot/pull/266
[#270]: https://github.com/ergebnis/factory-bot/pull/270
[#273]: https://github.com/ergebnis/factory-bot/pull/273
[#286]: https://github.com/ergebnis/factory-bot/pull/286
[#287]: https://github.com/ergebnis/factory-bot/pull/287
[#300]: https://github.com/ergebnis/factory-bot/pull/300
[#301]: https://github.com/ergebnis/factory-bot/pull/301
[#302]: https://github.com/ergebnis/factory-bot/pull/302
[#309]: https://github.com/ergebnis/factory-bot/pull/309
[#311]: https://github.com/ergebnis/factory-bot/pull/311
[#312]: https://github.com/ergebnis/factory-bot/pull/312
[#314]: https://github.com/ergebnis/factory-bot/pull/314
[#321]: https://github.com/ergebnis/factory-bot/pull/321
[#338]: https://github.com/ergebnis/factory-bot/pull/338
[#351]: https://github.com/ergebnis/factory-bot/pull/351
[#353]: https://github.com/ergebnis/factory-bot/pull/353
[#365]: https://github.com/ergebnis/factory-bot/pull/365
[#369]: https://github.com/ergebnis/factory-bot/pull/369
[#374]: https://github.com/ergebnis/factory-bot/pull/374
[#375]: https://github.com/ergebnis/factory-bot/pull/375
[#459]: https://github.com/ergebnis/factory-bot/pull/459
[#481]: https://github.com/ergebnis/factory-bot/pull/481
[#493]: https://github.com/ergebnis/factory-bot/pull/493
[#495]: https://github.com/ergebnis/factory-bot/pull/495
[#496]: https://github.com/ergebnis/factory-bot/pull/496
[#498]: https://github.com/ergebnis/factory-bot/pull/498
[#499]: https://github.com/ergebnis/factory-bot/pull/499
[#682]: https://github.com/ergebnis/factory-bot/pull/682

[@localheinz]: https://github.com/localheinz
