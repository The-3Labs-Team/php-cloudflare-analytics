
Add in your .env file the following variables:

```dotenv
CLOUDFLARE_API_TOKEN='your_cloudflare_api_token'
CLOUDFLARE_ZONE_TAG_ID='zoneTag'
```

## Refactor

Usabe fields: firewallEventsAdaptive, 

```php

use The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics;

$cf = new CloudflareAnalytics(
    token: 'your_cloudflare_api_token', zoneTag: 'zoneTag'
);

$cf = new CloudflareAnalytics();

// Get results
// $cf->get();

// // Filter by date
// $cf->whereBetween('2021-01-01', '2021-01-31')->get();

// // Limit the results
// $cf->take(10)->get();

// // Order the results
// $cf->orderBy('datetiime', 'DESC')->get();


-- multiple filters --

$cf->select('firewallEventsAdaptive AS last10Events', 'httpRequestsAdaptiveGroups AS top3DeviceTypes')
    ->whereBetween('last10Events.2021-01-01', 'last10Events.2021-01-31')
    ->whereBetween('top3DeviceTypes.2021-01-01', 'top3DeviceTypes.2021-01-31')
    ->orderBy('last10Events.datetime', 'DESC')
    ->orderBy('top3DeviceTypes.count', 'DESC')
    ->get();

```


DEMO MULTIPLE FILTERS

```graphql

{
  viewer {
    zones(filter: { zoneTag: $tag }) {
      last10Events: firewallEventsAdaptive(
        filter: {
          datetime_gt: $start
          datetime_lt: $end
        }
        limit: 10
        orderBy: [
          datetime_DESC
        ]
      ) {
        action
        datetime
        host: clientRequestHTTPHost
      }
      top3DeviceTypes: httpRequestsAdaptiveGroups(
        filter: {
          date: $ts
        }
        limit: 10
        orderBy: [
          count_DESC
        ]
      ) {
        count
        dimensions {
          device: clientDeviceType
        }
      }
    }
  }
}

```


## Usage





and you can pass the following parameters to get the data:

- PARAM => `sum` or `uniq`
- PARAM TYPE
    - SUM: `request`, `pageViews`, `cachedBytes`, `cachedRequests`, `threats`
    - UNIQ: `uniques`
