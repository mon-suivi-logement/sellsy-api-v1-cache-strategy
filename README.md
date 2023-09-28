# sellsy-api-v1-cache-strategy
Sellsy Api V1 cache strategy for Kevinrob Guzzle cache middleware


## Installation

`composer require mon-suivi-logement/sellsy-api-v1-cache-strategy:dev-main`

or add it the your `composer.json` and run `composer update`.



# How?

Example with LaravelCacheStorage :

```php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\FlysystemStorage;
use Kevinrob\GuzzleCache\Storage\LaravelCacheStorage;
use MonSuiviLogement\GuzzleCache\Strategy\SellsyApiV1Strategy;

$stack = HandlerStack::create();
$cache_middleware = new CacheMiddleware(
    new SellsyApiV1Strategy(
        new LaravelCacheStorage(
            Cache::store('file')
        ),
        1800, // the TTL in seconds
    ),
);
//Allow POST methods on middleware
$cache_middleware->setHttpMethods(['GET' => true, 'POST' => true]);
 
$stack->push($cache_middleware,'sellsy-cache');

$guzzle_client = new Client(["handler"  => $stack]);
$transport_bridge = new Guzzle6($guzzle_client);

```