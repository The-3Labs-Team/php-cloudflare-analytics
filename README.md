# Cloudflare Analytics GraphQL API PHP Client

This package is a simple PHP client for the Cloudflare Analytics GraphQL API.

> ⚠️ **Note:** This package is not official and is not affiliated with Cloudflare. It is a community-driven package.

> 🚨 **Note 2:** This package is under development and is not ready for production.

## Installation

You can install the package via composer:

```bash
composer require the3labsteam/php-cloudflare-analytics
```

## Configuration

Add in your .env file the following variables:

```dotenv
CLOUDFLARE_API_TOKEN='your_cloudflare_api_token'
CLOUDFLARE_ZONE_TAG_ID='zoneTag'
```

or you can pass the token and zoneTag as parameters in the constructor.

```php
use The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics;

$cf = new CloudflareAnalytics(
    token: 'your_cloudflare_api_token', zoneTag: 'zoneTag'
);
```

## Usage

You can use the following methods to build your query:

```php
use The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics;

$cf = new CloudflareAnalytics;

$results = $cf->select('firewallEventsAdaptive AS firewall')
    ->get('firewall.datetime', 'firewall.action');
```

The `get` method will return an array with the results.

## Available fields

- `firewallEventsAdaptive`
- `httpRequests1mGroups`
- `httpRequestsAdaptiveGroups`
- `threatsAdaptiveGroups`
- `threatsByCountryAdaptiveGroups`

## Default fields

- `datetime`: 1 hour
- `take`: 10
- `orderBy`: `datetime`

## Demo

Get latest 10 firewall events:

```php
$results = $cf->select('firewallEventsAdaptive AS firewall')
    ->get('firewall.datetime', 'firewall.action');
```

Filter between two dates:

```php
$results = $cf->select('firewallEventsAdaptive AS firewall')
    ->where('firewall.datetime', '>=', '2021-10-01T00:00:00Z')
    ->where('firewall.datetime', '<=', '2021-10-02T00:00:00Z')
    ->get('firewall.datetime', 'firewall.action');
```

Limit the results:

```php
$results = $cf->select('firewallEventsAdaptive AS firewall')
    ->take('firewall', 5)
    ->get('firewall.datetime', 'firewall.action');
```

Order the results:

```php
$results = $cf->select('firewallEventsAdaptive AS firewall')
    ->orderBy('firewall.datetime', 'desc')
    ->get('firewall.datetime', 'firewall.action');
```

Get two fields from two different tables: // TODO: test this

```php

$results = $cf->select('firewallEventsAdaptive AS firewall, threatsAdaptiveGroups AS threats')
    ->get('firewall.datetime', 'firewall.action', 'threats.datetime', 'threats.action');
```

Get http visits and sum them:

```php
    $results = $cf->select('httpRequests1mGroups AS http')
        ->take('http', 10)
        ->get('sum.countryMap.clientCountryName', 'sum.countryMap.requests', 'sum.countryMap.bytes', 'sum.countryMap.threats', 'dimensions.datetimeHour');
```


## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [The3LabsTeam](https://3labs.it)

// TODO: Add the rest of the file