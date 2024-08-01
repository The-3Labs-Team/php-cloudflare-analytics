
Add in your .env file the following variables:

```dotenv
CLOUDFLARE_API_TOKEN='your_cloudflare_api_token'
```

## Usage

Default use:

```php
(new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics('zoneTag'))
```
next you can use the following methods:

```php
->getLast6Hours($param, $paramType)

->getLast24Hours($param, $paramType)

->geLast7Days($param, $paramType)

->getLastMonth($param, $paramType)
```

and you can pass the following parameters to get the data:

- PARAM => `sum` or `uniq`
- PARAM TYPE
    - SUM: `request`, `pageViews`, `cachedBytes`, `cachedRequests`, `threats`
    - UNIQ: 'uniques'

### Example

Get the total number of requests in the last 6 hours:

```php
$cloudflare = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics('29djm3nr...');
$cloudflare->getLast6Hours('sum', 'request');
```

Get the total number of unique visitors in the last 24 hours:

```php
$cloudflare = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics('29djm3nr...');
$cloudflare->getLast24Hours('uniq', 'uniques');
```

Get the total number of page views in the last 7 days:

```php
$cloudflare = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics('29djm3nr...');
$cloudflare->geLast7Days('sum', 'pageViews');
```


