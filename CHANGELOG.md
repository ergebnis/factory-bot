# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`fa9c564...master`][fa9c564...master].

### Added

* Imported [`breerly/factory-girl-php@0e6f1b6`](https://github.com/unhashable/factory-girl-php/tree/0e6f1b6724d39108a2e7cef68a74668b7a77b856), ([#1]), by [@localheinz]
* Imported [`ergebnis/factory-girl-definition@23e57bc`](https://github.com/ergebnis/factory-girl-definition/tree/23e57bc2105ac7a32e3ec7103c866899fe6ad20c), ([#6]), by [@localheinz]

### Changed

* Removed possibility to set the entity namespace on the `FixtureFactory` ([#3]), by [@localheinz]
* Removed `StatusArrayType` ([#13]), by [@localheinz]
* Removed `FieldDef::past()`, `FieldDef::future()`, and `DateIntervalHelper` ([#14]), by [@localheinz]
* Removed `Repository` along with locking capabilities ([#15]), by [@localheinz]
* Removed `QueryBuilder` ([#16]), by [@localheinz]
* Used `Doctrine\ORM\EntityManagerInterface` instead of `Doctrine\ORM\EntityManager` in type and return type declarations ([#24]), by [@localheinz]
* Marked all classes as `final` ([#33]), by [@localheinz]
* Marked `EntityDef` as internal ([#49]), by [@localheinz]
* Started throwing an `InvalidFieldNames` exception instead of a generic `Exception` when fields are referenced that are not present in the corresponding entity ([#87]), by [@localheinz]
* Renamed `EntityDef` to `EntityDefinition` ([#91]), by [@localheinz]
* Renamed `FieldDef` to `FieldDefinition` ([#92]), by [@localheinz]

### Fixed

* Populated embeddables and disallowed referencing fields using dot notation ([#79]), by [@localheinz]

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

[@localheinz]: https://github.com/localheinz
