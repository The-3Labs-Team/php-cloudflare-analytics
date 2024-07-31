
Add in your env file the following variables:

```dotenv
CLOUDFLARE_API_TOKEN='your_cloudflare_api_token'
[//]: # (CLOUDFLARE_DEFAULT_ZONE_TAG='your_cloudflare_default_zone_tag')
```

- ### Get views between two dates

```php
(new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics())
    ->getViewsBetweenDates('start_date', 'end_date', 'zone_tag');
```

- ### Get total views between two dates

```php
(new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics())
    ->getTotalViewsBetweenDates('start_date', 'end_date', 'zone_tag');
```
