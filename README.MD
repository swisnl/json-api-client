# { json:api } Client

[![PHP from Packagist](https://img.shields.io/packagist/php-v/swisnl/jsonapi.svg)](https://packagist.org/packages/swisnl/jsonapi)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/swisnl/jsonapi.svg)](https://packagist.org/packages/swisnl/jsonapi)
[![Software License](https://img.shields.io/packagist/l/swisnl/jsonapi.svg)](LICENSE)
[![Run Status](https://api.shippable.com/projects/59ae7fe68a02980600016631/badge?branch=master)](https://app.shippable.com/github/swisnl/jsonapi)
[![Coverage Badge](https://api.shippable.com/projects/59ae7fe68a02980600016631/coverageBadge?branch=master)](https://app.shippable.com/github/swisnl/jsonapi) 

A PHP package for mapping remote { json:api } resources to Eloquent like models and collections.


## Installation

``` bash
composer require swisnl/jsonapi
```

### HTTP Client

We are decoupled from any HTTP messaging client with the help of [PHP-HTTP](http://php-http.org/).
This requires another package providing [php-http/client-implementation](https://packagist.org/providers/php-http/client-implementation).
To use Guzzle 6, for example, simply require `php-http/guzzle6-adapter`:

``` bash
composer require php-http/guzzle6-adapter
```

### Laravel Service Provider

If you are using Laravel < 5.5 or have disabled package auto discover, you must add the service provider to your `config/app.php` file:

``` php
'providers' => [
    ...,
    Swis\JsonApi\Client\ServiceProvider::class,
],
```


## Getting started

Short description
        
### Publish configuration

### Configure endpoint


## Package flow 
   
Short description   
   
### DocumentClient 

### Client (domme versie voor bijvoorbeeld binary bestanden)


## TypeMapper

Short description

### Mapping types


## ServiceProvider

Short description

### Bind TypeMapper

### Bind Parser

### Bind Clients


## Item models

Short description

### Jenssegers

Explain relations.

### Eloquent (EXPERIMENTAL)

## Collections


## Todo

 - [ ] CHANGELOG.MD
 - [ ] Remove resources? Seems redundant.


## Testing

``` bash
composer test
```


## Security

If you discover any security related issues, please email service@swis.nl instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
