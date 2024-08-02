<?php

namespace The3LabsTeam\PhpCloudflareAnalytics;

use Dotenv\Dotenv;

class CloudflareAnalytics
{
    private string $apiToken;

    private string $zoneTag;

    protected string $endpoint;

    protected string $startDate;

    protected string $endDate;

    protected ?int $limit;

    protected ?string $orderBy;

    /**
     * CloudflareAnalytics constructor.
     */
    public function __construct(?string $apiToken = null, ?string $zoneTag = null)
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();

        $this->apiToken = $apiToken ?? $_ENV['CLOUDFLARE_API_TOKEN'];
        $this->zoneTag = $zoneTag ?? $_ENV['CLOUDFLARE_ZONE_TAG_ID'];
        $this->endpoint = 'https://api.cloudflare.com/client/v4/graphql';

        $this->startDate = (new \DateTime('-1 day'))->format('c');
        $this->endDate = (new \DateTime)->format('c');
        $this->limit = 1000;
        $this->orderBy = 'datetime_DESC';
    }

    /**
     * Query the Cloudflare API
     */
    protected function query($query)
    {
        $ch = curl_init($this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.$this->apiToken,
            ],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response, true);

        if (isset($response['errors'])) {
            echo $response['errors'][0]['message'];
        }

        return $response;
    }

    /**
     * Get the total views between two dates - Returns the total views
     */
    public function whereBetween(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Set the order by field and direction
     */
    public function orderBy(string $field, string $direction = 'ASC')
    {
        $this->orderBy = $field.'_'.$direction;

        return $this;
    }

    /**
     * Get a specific number of results
     */
    public function take(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get data
     */
    public function get()
    {

        $query = <<<GRAPHQL
            query {
              viewer {
                zones(filter: {zoneTag: "$this->zoneTag"}) {
                  last10Events: firewallEventsAdaptive(
                    filter: {
                      datetime_gt: "$this->startDate"
                      datetime_lt: "$this->endDate"
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
                      datetime_gt: "$this->startDate"
                      datetime_lt: "$this->endDate"
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
        GRAPHQL;

        $response = $this->query($query);

        dd($response);

        return $response;

        // return $this->sumTotal($response, 'httpRequests1dGroups', $param, $paramType);
    }

    // protected function sumTotal($response, $zonesType, $param, $paramType)
    // {
    //     $response = $response['data']['viewer']['zones'][0][$zonesType];

    //     $total = 0;
    //     foreach ($response as $key => $value) {
    //         $total += $value[$param][$paramType];
    //     }

    //     return $total;
    // }

    /**
     * Get the total views between two dates - Return the total views
     *
     * @return array
     */
    public function getBetweenHours($sub, $param, $paramType)
    {
        // Current date/time in ISO 8601 format
        $endDate = date('c');
        $startDate = date('c', strtotime($sub));

        $query = <<<GRAPHQL
           query {
              viewer {
                zones(filter: {zoneTag: "$this->zoneTag"}) {
                  httpRequests1hGroups(
                    limit: 1000
                    filter: {
                      datetime_geq: "$startDate"
                      datetime_lt: "$endDate"
                    }
                  ) {
                    dimensions {
                      datetime
                    }
                    sum {
                      requests
                      pageViews
                      cachedBytes
                      cachedRequests
                      threats
                    }
                    uniq {
                      uniques
                    }
                  }
                }
              }
            }
        GRAPHQL;

        $response = $this->query($query);

        // return $this->sumTotal($response, 'httpRequests1hGroups', $param, $paramType);
    }
}
