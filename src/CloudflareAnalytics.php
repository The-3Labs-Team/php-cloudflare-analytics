<?php

namespace The3LabsTeam\PhpCloudflareAnalytics;

use Dotenv\Dotenv;


class CloudflareAnalytics
{
    public string $apiToken;
    public string $zoneTag;
    public string $endpoint;

    public string $startDate;
    public string $endDate;
    public int $limit;

    /**
     * CloudflareAnalytics constructor.
     *
     * @param string|null $apiToken
     * @param string|null $zoneTag
     */
    public function __construct(?string $apiToken = null, ?string $zoneTag = null)
    {
      $dotenv = Dotenv::createImmutable(__DIR__.'/../');
      $dotenv->load();

        $this->apiToken = $apiToken ?? $_ENV['CLOUDFLARE_API_TOKEN'];
        $this->zoneTag = $zoneTag ?? $_ENV['CLOUDFLARE_ZONE_TAG_ID'];
        $this->endpoint = 'https://api.cloudflare.com/client/v4/graphql';

        $this->startDate = date('Y-m-d', strtotime('-1 month'));
        $this->endDate = date('Y-m-d');
        $this->limit = 1000;
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

    protected function sumTotal($response, $zonesType, $param, $paramType)
    {
        $response = $response['data']['viewer']['zones'][0][$zonesType];

        $total = 0;
        foreach ($response as $key => $value) {
            $total += $value[$param][$paramType];
        }

        return $total;
    }

     /**
     * Get the total views between two dates - Returns the total views
     */
    public function whereBetween(string $startDate, string $endDate) {
      $this->startDate = $startDate;
      $this->endDate = $endDate;

      return $this;
    }

    /**
     * Get a specific number of results
     */
    public function take(int $limit) {
      $this->limit = $limit;

      return $this;
    }

    /**
     * Get data
     */
    public function get($param = 'uniq', $paramType = 'pageViews')
    {

        $query = <<<GRAPHQL
            query {
              viewer {
                zones(filter: {zoneTag: "$this->zoneTag"}) {
                  httpRequests1dGroups(
                    limit: $this->limit
                    filter: {
                      date_geq: "$this->startDate"
                      date_lt: "$this->endDate"
                    }
                  ) {
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

        return $response;

        return $this->sumTotal($response, 'httpRequests1dGroups', $param, $paramType);
    }

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

        return $this->sumTotal($response, 'httpRequests1hGroups', $param, $paramType);
    }

}
