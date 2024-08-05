<?php

use The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics;

beforeEach(function () {
    $this->cf = new CloudflareAnalytics;
});

it('can get firewall data', function () {
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

it('can get total views', function () {
    $cf = new CloudflareAnalytics;

    $results = $cf->select('httpRequests1mGroups AS http')
        ->get('http.sum.requests');

        dd($results);

    $this->assertIsArray($results);
    $this->assertGreaterThan(0, $results);
});

// it('can get total views between two dates', function () {
//     $startDate = date('Y-m-d', strtotime('-2 months'));
//     $endDate = date('Y-m-d');

//     $cf = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics();
//     $result = $cf->whereBetween($startDate, $endDate)->get();

//     $this->assertIsArray($result);
//     $this->assertGreaterThan(0, $result);

// });

// it('can get total views with a specific limit', function () {
//     $limit = 10;

//     $cf = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics();
//     $result = $cf->take($limit)->get();

//     $this->assertIsArray($result);
//     $this->assertGreaterThan(0, $result);
// });

// it('can get total views with custom parameters', function () {
//     $param = 'cachedRequests';
//     $paramType = 'cachedBytes';

//     $cf = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics();
//     $result = $cf->get($param, $paramType);

//     $this->assertIsArray($result);
//     $this->assertGreaterThan(0, $result);
// });
