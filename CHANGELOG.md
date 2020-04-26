# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`fa9c564...master`][fa9c564...master].

### Added

* Imported [`breerly/factory-girl-php@0e6f1b6`](https://github.com/unhashable/factory-girl-php/tree/0e6f1b6724d39108a2e7cef68a74668b7a77b856) ([#1]), by [@localheinz]
* Imported [`ergebnis/factory-girl-definition@23e57bc`](https://github.com/ergebnis/factory-girl-definition/tree/23e57bc2105ac7a32e3ec7103c866899fe6ad20c) ([#6]), by [@localheinz]
* Added `FieldDefinition::value()` which allows resolving a field definition to a constant value ([#149]), by [@localheinz]
* Added `FieldDefinition::closure()` which allows resolving a field definition to the return value of a closure that is invoked with the `FixtureFactory` ([#155]), by [@localheinz]
* Allowed creation of optional field definitions ([#167]) and ([#196]), by [@localheinz]

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

[fa9c564...master]: https://github.com/ergebnis/factory-bot/compare/fa9c564...master

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

[@localheinz]: https://github.com/localheinz
