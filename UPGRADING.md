# Upgrading swisnl/json-api-client

## From 1.x to 2.x

1. If you are using Laravel, you should remove `swisnl/json-api-client` and install `swisnl/json-api-client-laravel` instead. If you are not using Laravel, you can simply bump the dependency constraint to `^2.0`;
2. Make sure you don't have references to `Jenssegers\Model\Model`. This dependency has been dropped in favor of `Swis\JsonApi\Client\Item`, which is mostly the same.
3. Read through the [CHANGELOG](CHANGELOG.md) for other changes affecting you.
