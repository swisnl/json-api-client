# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

* Added support for Laravel 6 [#61](https://github.com/swisnl/json-api-client/pull/61).

### Changed

* Switched from [PHP-HTTP](http://php-http.org/) to [PSR-18](https://www.php-fig.org/psr/psr-18/), its successor [#60](https://github.com/swisnl/json-api-client/pull/60).
* The `\Swis\JsonApi\Client\Client` now uses [php-http/discovery](https://github.com/php-http/discovery) itself instead of the service provider. This should make usage without Laravel easier [#60](https://github.com/swisnl/json-api-client/pull/60).
* Removed the `$baseUri` parameter from `\Swis\JsonApi\Client\Client::__construct()`, use `\Swis\JsonApi\Client\Client::setBaseUri()` instead [#60](https://github.com/swisnl/json-api-client/pull/60).

### Removed

* Removed `\Swis\JsonApi\Client\Providers\ServiceProvider::getHttpClient()` and `\Swis\JsonApi\Client\Providers\ServiceProvider::getMessageFactory()` as the client now discovers these classes itself. Custom HTTP clients must now be registered within your own service provider using a custom container binding [#60](https://github.com/swisnl/json-api-client/pull/60).

### Fixed

* Self and related links can not be `null` [#59](https://github.com/swisnl/json-api-client/pull/59).
* Error links MUST have about link. Relationship links MUST have either self or related link [#59](https://github.com/swisnl/json-api-client/pull/59).
* Links has to be an object.

## [1.0.0-beta] - 2019-07-22

### Changed

* Drop art4/json-api-client dependency and validate the JSON ourselves [#58](https://github.com/swisnl/json-api-client/pull/58).
* `\Swis\JsonApi\Client\Exceptions\ValidationException` is thrown instead of `\Art4\JsonApiClient\Exception\ValidationException` [#58](https://github.com/swisnl/json-api-client/pull/58).
* Added `hasAttribute` to `ItemInterface`.
* All exceptions thrown by this package now implement `\Swis\JsonApi\Client\Exceptions\Exception`.

### Fixed

* Empty (`null`) links are correctly parsed.
* Empty (`null`) singular relationships are correctly parsed.

## [0.20.0] - 2019-07-11

### Changed

* Change signature of `RepositoryInterface::save()`. It should now receive an `ItemInterface` instead of a `ItemDocumentInterface`.

## [0.19.0] - 2019-07-10

### Added

* Added `DocumentFactory` [#52](https://github.com/swisnl/json-api-client/pull/52).
* Added facades for `DocumentFactory`, `DocumentParser`, `ItemHydrator`, `ResponseParser` and `TypeMapper`.
* Added `DocumentParserInterface` and `ResponseParserInterface` interfaces and implementations [#54](https://github.com/swisnl/json-api-client/pull/54).

### Changed

* The `TypeMapper` now checks if the class exists in the setter instead of the getter.
* The `ItemHydrator` now also hydrates the id if provided [#53](https://github.com/swisnl/json-api-client/pull/53).
* Added `hasType`, `hasAttributes`, `hasRelationships` and `getRelations` to `ItemInterface` [#53](https://github.com/swisnl/json-api-client/pull/53).
* Removed `canBeIncluded` and `getIncluded` from `ItemInterface` as the `DocumentFactory` is now responsible for gathering the included items [#53](https://github.com/swisnl/json-api-client/pull/53).
* Renamed `getRelationship` to `getRelation`, `hasRelationship` to `hasRelation` and `removeRelationship` to `unsetRelation` in `Item` [#53](https://github.com/swisnl/json-api-client/pull/53).
* Renamed/aligned some parameters in several relation methods in `Item` [#53](https://github.com/swisnl/json-api-client/pull/53).
* Renamed namespace `Swis\JsonApi\Client\Traits` to `Swis\JsonApi\Client\Concerns` [#53](https://github.com/swisnl/json-api-client/pull/53).
* Renamed namespace `Swis\JsonApi\Client\JsonApi` to `Swis\JsonApi\Client\Parsers` [#54](https://github.com/swisnl/json-api-client/pull/54).
* Renamed `ServiceProvider::registerParser` (singular) to `ServiceProvider::registerParsers` (plural) [#54](https://github.com/swisnl/json-api-client/pull/54).

### Removed

* Removed `CollectionDocumentBuilder` and `ItemDocumentBuilder` in favor of `DocumentFactory` [#52](https://github.com/swisnl/json-api-client/pull/52).
* Removed `ParserInterface` in favor of `DocumentParserInterface` and `ResponseParserInterface` [#54](https://github.com/swisnl/json-api-client/pull/54).

## [0.18.0] - 2019-07-01

### Added

* The id of an item can be set/get using magic accessors just like the attributes [#51](https://github.com/swisnl/json-api-client/pull/51).

### Changed

* Added (optional) type hints to several interfaces.
N.B. This is a breaking change if you implement some of the interfaces yourself.
* Renamed `deleteById` method to simply `delete` and removed the 'old' implementation in `RepositoryInterface`.
N.B. This is a breaking change and all calls to `deleteById` should simply be changed to `delete` as the signature is unchanged.

## [0.17.0] - 2019-03-19

### Added

* Added support for Laravel 5.8 [#50](https://github.com/swisnl/json-api-client/pull/50).

### Changed

* Dropped Laravel <5.5 support [#50](https://github.com/swisnl/json-api-client/pull/50).
* Dropped PHP <7.1 support [#50](https://github.com/swisnl/json-api-client/pull/50).

## [0.16.0] - 2019-03-14

### Added

* Added `DocumentInterface::getResponse()` so every document instance can have its corresponding response. This allows access to the underlying response to check headers or status codes etc [#48](https://github.com/swisnl/json-api-client/pull/48).
* Added `ParserInterface::deserializeResponse($reponse)` to deserialize a `\Psr\Http\Message\ResponseInterface`.

### Changed

* `ClientInterface` must now return a `\Psr\Http\Message\ResponseInterface` for requests instead of our own (removed) `ResponseInterface` [#48](https://github.com/swisnl/json-api-client/pull/48).
N.B. This is a breaking change if you use the `Client` directly, the `DocumentClient` isn't affected.
* Changed namespace of error classes:
```
\Swis\JsonApi\Client\Errors\Error -> \Swis\JsonApi\Client\Error
\Swis\JsonApi\Client\Errors\ErrorCollection -> \Swis\JsonApi\Client\ErrorCollection
\Swis\JsonApi\Client\Errors\ErrorSource -> \Swis\JsonApi\Client\ErrorSource
```

### Removed

* Removed `ResponseInterface` and `Response` classes [#48](https://github.com/swisnl/json-api-client/pull/48).

## [0.15.0] - 2019-02-21

This release includes changes to some interfaces [#47](https://github.com/swisnl/json-api-client/pull/47). This is a breaking change if you use these interfaces in your own code.

### Added

* Added `take` method to `Repository` to allow fetching resources without id.
* Added links and meta to `ItemInterface`.
* Added `Jsonapi` class.
* Added links and meta to `OneRelationInterface` and `ManyRelationInterface`.
* Added `Link` and `Links` classes.
* Added links to `Error`.

### Changed

* `Error::getMeta()` now returns a `Meta` instance instead of an `ErrorMeta` instance. The `Meta` class does not have the `has` and `get` methods, but uses magic overloading methods (e.g. `__get` and `__set`) just like `Item`.
N.B. This is a breaking change if you use meta on errors.
* `DocumentInterface::getLinks()` now returns a `Links` instance instead of a plain array. If no links are present, it returns `null`. All implementations have been updated to reflect these changes.
N.B. This is a minor breaking change if you use links on documents.
* `DocumentInterface::getMeta()` now returns a `Meta` instance instead of a plain array. If no meta is present, it returns `null`. All implementations have been updated to reflect these changes.
N.B. This is a minor breaking change if you use meta on documents.
* `DocumentInterface::getJsonapi()` now returns a `Jsonapi` instance instead of a plain array. If no jsonapi is present, it returns `null`. All implementations have been updated to reflect these changes.
* Parameters for `ItemInterface::setRelation()` have changed to include optional `Links` and `Meta` objects.
* `JsonApi\ErrorsParser`, `JsonApi\Hydrator` and `JsonApi\Parser` have an extra dependency in their constructor.
N.B. Make sure to add this dependency if you've overwritten `ServiceProvider::registerParser` or construct the `JsonApi\Parser` yourself.

### Removed

* Removed `ErrorMeta` class in favor of generic `Meta` class.

### Fixed

* Fixed parsing of [JSON:API object](https://jsonapi.org/format/#document-jsonapi-object) in document.

## [0.14.0] - 2019-01-23

This release includes changes to some interfaces [#45](https://github.com/swisnl/json-api-client/pull/45). This is a breaking change if you use these interfaces in your own code.

### Added

* Added `OneRelationInterface` and `ManyRelationInterface` to differentiate between singular and plural relations.
* Added documentation about `ItemDocumentBuilder`, `ItemHydrator` and `Repository` classes.

### Changed

* Moved `setType` and `getType` from `RelationInterface` to a separate interface; `TypedRelationInterface`.
* Added type hints to `ItemInterface::setRelation`.
* Added return type hint to `Item::hasAttribute`.

### Removed

* Removed `RelationInterface` in favor of `OneRelationInterface` and `ManyRelationInterface`.
* Removed `setId` and `getId` from `HasOneRelation` and `MorphToRelation`. These operations should be performed on the included item.
* Removed `setType` and `getType` from morph relations. Use regular relations if you want to set the type.

## [0.13.0] - 2019-01-14

### Fixed

* Omit item from included when it has no attributes or relationships (only type and id)
N.B. This is a breaking change if you implement the `ItemInterface` yourself instead of using the supplied `Item`.
* Make sure included is always a plain array so it is serialized as array

## [0.12.1] - 2019-01-11

### Fixed

* Fix hydrating of HasOne relations by id using ItemHydrator [#44](https://github.com/swisnl/json-api-client/pull/44)

## [0.12.0] - 2019-01-11

### Added

* Add `isSuccess` method to Document [#40](https://github.com/swisnl/json-api-client/pull/40)

### Changed

* Add headers to request methods in `DocumentClientInterface`.
N.B. This is a breaking change if you implement the interface yourself or extend the `DocumentClient`. [#34](https://github.com/swisnl/json-api-client/pull/34)
* `Repository` doesn't throw exceptions anymore. [#41](https://github.com/swisnl/json-api-client/pull/41)
N.B. This is a breaking change if you catch `DocumentNotFoundException` or `DocumentTypeException`. If you would like the old behaviour, you can simply extend the `Repository` and implement it yourself.
* A HasOne or MorphTo relation do not set a `[relationship]_id` field on the parent when associating a related item. [#42](https://github.com/swisnl/json-api-client/pull/42)

### Removed

* Removed obsolete `ItemDocumentSerializer` in favor of `JsonSerializable`.
N.B. This is a breaking change if you use this class directly, construct the `DocumentClient` yourself or have overwritten `\Swis\JsonApi\Client\Providers\ServiceProvider::registerClients`. The `ItemDocument` can now be serialized using `json_encode`.
* Removed obsolete `DocumentNotFoundException` and `DocumentTypeException`. [#41](https://github.com/swisnl/json-api-client/pull/41)
N.B. This is a breaking change if you catch these exceptions.

### Fixed

* Do not fail on, but skip relationships without data [#38](https://github.com/swisnl/json-api-client/pull/38)
* Dissociating a related item now produces valid JSON [#42](https://github.com/swisnl/json-api-client/pull/42)

## [0.11.0] - 2018-12-21

### Changed

* Implement `JsonSerializable` in `Document` [#32](https://github.com/swisnl/json-api-client/pull/32)
* Add toArray to `DocumentInterface` and `Document` so `CollectionDocument` and `ItemDocument` now share the same toArray method [#32](https://github.com/swisnl/json-api-client/pull/32) 

## [0.10.3] - 2018-10-26

### Fixed

* Omit 'type' attribute when filling attributes for a morph relationship [#28](https://github.com/swisnl/json-api-client/pull/28)

## [0.10.2] - 2018-10-24

### Fixed

* Allow '0' as id in ItemDocumentBuilder [#27](https://github.com/swisnl/json-api-client/pull/27)

## [0.10.1] - 2018-09-25

### Fixed

* Add the id when the item has one and not only when it is not new

## [0.10.0] - 2018-09-10

### Added

* Added Laravel 5.7 support

## [0.9.0] - 2018-08-30

### Added

* Added Laravel 5.6 support
* Travis now runs tests on all supported versions of Laravel 

## [0.8.0] - 2018-08-14

### Changed

* Refactored name(space) of `\Swis\JsonApi\Client\Items\JenssegersItem` to `\Swis\JsonApi\Client\Item` as we only have one item now.

### Removed

* `EloquentItem` is removed because it had some limitations which could not be fixed without being too opinionated.
* `NullItem` is removed in favor of simply `null`. This item was only used internally so this should not affect you.

## [0.7.5] - 2018-07-04

### Fixed

* Do not add attributes to item when empty [#20](https://github.com/swisnl/json-api-client/pull/20)

## [0.7.4] - 2018-06-20

### Fixed

* Reverted the behaviour of handling duplicate items back to what it was in <= 0.7.2. This change in behaviour was introduced in the last performance update (0.7.3). N.B. This change will only affect you if you parse documents with duplicate items, which violates the JSON API spec.

## [0.7.3] - 2018-06-01

### Changed

* Improved performance of JsonApi\Hydrator [#18](https://github.com/swisnl/json-api-client/pull/18)

## [0.7.2] - 2018-05-17

### Fixed

* Fixed building the request with headers

## [0.7.1] - 2018-05-16

### Fixed

* Corrected the path to the config file

## [0.7.0] - 2018-05-11

### Changed

#### Update art4/json-api-client to latest version, this changes some of the returned classes.

The following classes are changed in some arguments and some method returns, if you extended or directly use `\Swis\JsonApi\Client\JsonApi\Hydrator` or `\Swis\JsonApi\Client\JsonApi\Parser` please check your code.

```
\Art4\JsonApiClient\Resource\CollectionInterface -> \Art4\JsonApiClient\ResourceCollectionInterface
\Art4\JsonApiClient\Resource\ItemInterface -> \Art4\JsonApiClient\ResourceItemInterface
\Art4\JsonApiClient\Resource\IdentifierCollection -> \Art4\JsonApiClient\ResourceIdentifierCollection
\Art4\JsonApiClient\Resource\Identifier -> \Art4\JsonApiClient\ResourceIdentifier
\Art4\JsonApiClient\Resource\Collection -> \Art4\JsonApiClient\ResourceCollection
```

## [0.6.0] - 2018-03-06

### Added

* Added CHANGELOG.md

### Changed

* Changed package name in composer.json 
* Changed autodiscovery service provider in composer.json 

## [0.5.0] - 2018-03-06

### Added 

* Added a proper README with instructions.

### Changed

* Updated namespace to `Swis\JsonApi\Client` for consistency.
* Split service provider binds so overwriting is easier.

### Removed

* Extracted fixtures client to  [swisnl/php-http-fixture-client](https://github.com/swisnl/php-http-fixture-client) and [swisnl/guzzle-fixture-handler](https://github.com/swisnl/guzzle-fixture-handler)
* Removed resources as it was redundant.

## [0.4.0] - 2018-01-16

### Changed

Refactored to use [php-http/httpplug](http://docs.php-http.org/en/latest/index.html) instead of Guzzle so the library doesn't depend on a specific http client implementation.

This does change how you customize the ServiceProvider, just include an adapter for the client you want to use and the library uses [autodiscovery](http://docs.php-http.org/en/latest/discovery.html) to find the correct [adapter/client](http://docs.php-http.org/en/latest/clients.html). It also uses a [MessageFactory](http://docs.php-http.org/en/latest/message/message-factory.html) for creating Requests and Response objects.

So for example, if you want to use Guzzle, just `composer require php-http/guzzle6-adapter`, and all should be swell.

In order to have a working mock client for testing you now need to include [php-http/mock-client](http://docs.php-http.org/en/latest/clients/mock-client.html). Which is an easy way to mock requests.
