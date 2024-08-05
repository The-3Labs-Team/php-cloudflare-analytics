# Cloudflare Analytics GraphQL API PHP Client

This package is a simple PHP client for the Cloudflare Analytics GraphQL API.

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
$startDate = (new DateTime)->sub(new DateInterval('P1D'))->format('c');
$endDate = (new DateTime)->format('c');

$cf = new CloudflareAnalytics;

$results = $cf->select('firewallEventsAdaptive AS firewall')
    ->whereBetween('firewall', $startDate, $endDate)
    ->orderBy('firewall.datetime', 'DESC')
    ->take('firewall', 2)
    ->get('firewall.datetime', 'firewall.action');
```

In the example above, we are querying the `firewallEventsAdaptive` field, filtering by the last 24 hours, ordering by datetime in descending order, and limiting the results to 2.

The `get` method will return an array with the results.

## Available fields

- `firewallEventsAdaptive`
- `httpRequests1mGroups`
- `httpRequestsAdaptiveGroups`
- `threatsAdaptiveGroups`
- `threatsByCountryAdaptiveGroups`

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [The3LabsTeam](https://3labs.it)

// TODO: Add the rest of the file