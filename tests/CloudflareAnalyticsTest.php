<?php

beforeEach(function () {
    $this->cf = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics;
});

it('can set API token and zone tag', function () {
    $apiToken = 'your-api-token';
    $zoneTag = 'your-zone-tag';

    $cf = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics($apiToken, $zoneTag);

    $this->assertEquals($apiToken, $cf->apiToken);
    $this->assertEquals($zoneTag, $cf->zoneTag);
});


it('can get total views between two dates', function () {
    $startDate = date('Y-m-d', strtotime('-2 months'));
    $endDate = date('Y-m-d');

    $cf = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics();
    $result = $cf->whereBetween($startDate, $endDate)->get();

    $this->assertIsArray($result);
    $this->assertGreaterThan(0, $result);

});

it('can get total views with a specific limit', function () {
    $limit = 10;

    $cf = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics();
    $result = $cf->take($limit)->get();

    $this->assertIsArray($result);
    $this->assertGreaterThan(0, $result);
});

it('can get total views with custom parameters', function () {
    $param = 'cachedRequests';
    $paramType = 'cachedBytes';

    $cf = new \The3LabsTeam\PhpCloudflareAnalytics\CloudflareAnalytics();
    $result = $cf->get($param, $paramType);

    $this->assertIsArray($result);
    $this->assertGreaterThan(0, $result);
});