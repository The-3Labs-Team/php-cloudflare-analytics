# Cloudflare Analytics GraphQL API PHP Client

This package is a simple PHP client for the Cloudflare Analytics GraphQL API.

> ⚠️ **Note:** This package is not official and is not affiliated with Cloudflare. It is a community-driven package.

> 🚨 **Note 2:** This package is under development and is not ready for production.

## Installation

You can install the package via composer:

```bash
composer require the-3labs-team/php-cloudflare-analytics
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

## Sponsor

<div>  
    <a href="https://www.tomshw.it/" target="_blank" rel="noopener noreferrer">
        <img  src="https://3labs-assets.b-cdn.net/assets/logos/banner-github/toms.png" alt="Tom's Hardware - Notizie, recensioni, guide all'acquisto e approfondimenti per tutti gli appassionati di computer, smartphone, videogiochi, film, serie tv, gadget e non solo" />  
    </a>
    <a href="https://spaziogames.it/" target="_blank" rel="noopener noreferrer" >
        <img src="https://3labs-assets.b-cdn.net/assets/logos/banner-github/spazio.png" alt="Spaziogames - Tutto sul mondo dei videogiochi. Troverai tantissime anteprime, recensioni, notizie dei giochi per tutte le console, PC, iPhone e Android." />
    </a>
    <br/>
    <a href="https://cpop.it/" target="_blank" rel="noopener noreferrer" >
        <img src="https://3labs-assets.b-cdn.net/assets/logos/banner-github/cpop.png" alt="Cpop - News, recensioni, guide su fumetto, cinema & serie TV, gioco da tavolo e di ruolo e collezionismo. Tutto quello di cui hai bisogno per rimanere aggiornato sul mondo della cultura pop"/>
    </a>
    <a href="https://data4biz.com/" target="_blank" rel="noopener noreferrer" >
        <img src="https://3labs-assets.b-cdn.net/assets/logos/banner-github/d4b.png" alt="Data4Biz - Sito dedicato alla trasformazione digitale del business" />
    </a>
    <br/>
    <a href="https://soshomegarden.com/" target="_blank" rel="noopener noreferrer" >
        <img src="https://3labs-assets.b-cdn.net/assets/logos/banner-github/sos.png" alt="SOS Home & Garden - Realtà dedicata a 360 gradi ai settori della casa e del giardino." />
    </a>
    <a href="https://global.techradar.com/it-it" target="_blank" rel="noopener noreferrer" >
        <img src="https://3labs-assets.b-cdn.net/assets/logos/banner-github/techradar.png" alt="Techradar - Le ultime notizie e recensioni dal mondo della tecnologia, su computer, sistemi per la casa, gadget e altro." />
    </a>
    <br/>
    <a href="https://aibay.it/" target="_blank" rel="noopener noreferrer" >
        <img src="https://3labs-assets.b-cdn.net/assets/logos/banner-github/aibay.png" alt="Aibay - Scopri AiBay, il leader delle notizie sull'intelligenza artificiale. Resta aggiornato sulle ultime innovazioni, ricerche e tendenze del mondo AI con approfondimenti, interviste esclusive e analisi dettagliate." />
    </a>
    <a href="https://coinlabs.it/" target="_blank" rel="noopener noreferrer" >
        <img src="https://3labs-assets.b-cdn.net/assets/logos/banner-github/coinlabs.png" alt="Coinlabs - Notizie, analisi approfondite, guide e opinioni aggiornate sul mondo delle criptovalute, blockchain e finanza" />
    </a>

</div>

