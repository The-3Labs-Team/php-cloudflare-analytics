<?php

use The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics;

beforeEach(function () {
    $this->cf = new CloudflareAnalytics;
});

it('can get total views sum by date', function () {
    $cf = new CloudflareAnalytics;

    $results = $cf->select('httpRequests1mGroups AS http')
        ->take('http', 10)
        ->get('sum.countryMap.clientCountryName', 'sum.countryMap.requests', 'sum.countryMap.bytes', 'sum.countryMap.threats', 'dimensions.datetimeHour');

    $this->assertIsArray($results);
    $this->assertGreaterThan(0, $results);
});

it('can get total views sum by date between two dates', function () {
    $startDate = (new DateTime)->sub(new DateInterval('P51M'))->format('c');
    $endDate = (new DateTime)->format('c');

    $cf = new CloudflareAnalytics;

    $results = $cf->select('httpRequests1mGroups AS http')
        ->whereBetween('http', $startDate, $endDate)
        ->take('http', 10)
        ->get('sum.countryMap.clientCountryName', 'sum.countryMap.requests', 'sum.countryMap.bytes', 'sum.countryMap.threats', 'dimensions.datetimeHour');

    $this->assertIsArray($results);
    $this->assertGreaterThan(0, $results);
});

it('can get firewall data', function () {
    $cf = new CloudflareAnalytics;

    $results = $cf->select('firewallEventsAdaptive AS firewall')
        ->get('firewall.datetime', 'firewall.action');

    $this->assertIsArray($results);
    $this->assertGreaterThan(0, $results);
});

it('can get firewall between two dates', function () {
    $startDate = (new DateTime)->sub(new DateInterval('P1D'))->format('c');
    $endDate = (new DateTime)->format('c');

    $cf = new CloudflareAnalytics;

    $results = $cf->select('firewallEventsAdaptive AS firewall')
        ->whereBetween('firewall', $startDate, $endDate)
        ->orderBy('firewall.datetime', 'DESC')
        ->take('firewall', 2)
        ->get('firewall.datetime', 'firewall.action');

    $this->assertIsArray($results);
    $this->assertGreaterThan(0, $results);
});

it('can get firewall data with a specific limit', function () {
    $limit = 10;

    $cf = new CloudflareAnalytics;

    $results = $cf->select('firewallEventsAdaptive AS firewall')
        ->take('firewall', $limit)
        ->get('firewall.datetime', 'firewall.action');

    $this->assertIsArray($results);
    $this->assertGreaterThan(0, $results);
});

it('can get firewall data with a specific order', function () {
    $cf = new CloudflareAnalytics;

    $results = $cf->select('firewallEventsAdaptive AS firewall')
        ->orderBy('firewall.datetime', 'DESC')
        ->get('firewall.datetime', 'firewall.action');

    $this->assertIsArray($results);
    $this->assertGreaterThan(0, $results);
});

it('can get firewall data with a specific order and limit', function () {
    $limit = 10;

    $cf = new CloudflareAnalytics;

    $results = $cf->select('firewallEventsAdaptive AS firewall')
        ->orderBy('firewall.datetime', 'DESC')
        ->take('firewall', $limit)
        ->get('firewall.datetime', 'firewall.action');

    $this->assertIsArray($results);
    $this->assertGreaterThan(0, $results);
});

it('can get firewall data with a specific order and limit between two dates', function () {
    $startDate = (new DateTime)->sub(new DateInterval('P1D'))->format('c');
    $endDate = (new DateTime)->format('c');
    $limit = 10;

    $cf = new CloudflareAnalytics;

    $results = $cf->select('firewallEventsAdaptive AS firewall')
        ->whereBetween('firewall', $startDate, $endDate)
        ->orderBy('firewall.datetime', 'DESC')
        ->take('firewall', $limit)
        ->get('firewall.datetime', 'firewall.action');

    $this->assertIsArray($results);
    $this->assertGreaterThan(0, $results);
});
