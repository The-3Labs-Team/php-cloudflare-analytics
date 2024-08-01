<?php

namespace The3LabsTeam\PhpCloudflareAnalytics;

class CloudflareAnalytics {

    public string $api_token;
    public string $zoneTag;
    public string $endpoint;

    public function __construct(string $zoneTag) {
        $this->api_token = env('CLOUDFLARE_API_TOKEN');
        $this->zoneTag = $zoneTag;
        $this->endpoint = "https://api.cloudflare.com/client/v4/graphql";
    }

    // ================== UTILITY ================== //

    protected function graphQLQuery($query) {
        $ch = curl_init($this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api_token
            ]
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

    // ================== DEFAULT FUNCTIONS ================== //

    //TODO: Merge all the functions in one function with parameters
    /**
     * Get the total views between two dates - Returns the total views
     * @param $startDate
     * @param $endDate
     * @param $param - 'sum' | 'uniq'
     * @param $paramType - sum : 'request', 'pageViews', 'cachedBytes', 'cachedRequests', 'threats' | uniq: 'uniques'
     * @return int|mixed
     */
    public function getBetweenDates($startDate, $endDate, $param = 'sum', $paramType = 'pageViews') {
        $query = <<<GRAPHQL
            query {
              viewer {
                zones(filter: {zoneTag: "$this->zoneTag"}) {
                  httpRequests1dGroups(
                    limit: 1000
                    filter: {
                      date_geq: "$startDate"
                      date_lt: "$endDate"
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

        $response = $this->graphQLQuery($query);
        return $this->sumTotal($response, 'httpRequests1dGroups', $param, $paramType);
    }

    /**
     * Get the total views between two dates - Return the total views
     * @param $sub
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

        $response = $this->graphQLQuery($query);

        return $this->sumTotal($response, 'httpRequests1hGroups', $param, $paramType);
    }

    // ================== DEFAULT PRESET ================== //

    /**
     * Get the total views last 6 hours - Returns the total views
     * @return int|mixed
     */
    public function getLast6Hours($param, $paramType)
    {
        return $this->getBetweenHours(sub: '-6 hours', param: $param, paramType: $paramType);
    }

    /**
     * Get the total views last 24 hours - Returns the total views
     * @return int|mixed
     */
    public function getLast24Hours($param, $paramType)
    {
        return $this->getBetweenHours(sub: '-24 hours', param: $param, paramType:  $paramType);
    }

    /**
     * Get the total views last 7 days - Returns the total views
     * @return int|mixed
     */
    public function getLast7Days($param, $paramType)
    {
        // Current date/time in Y-m-d
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d');

        return $this->getBetweenDates(startDate: $startDate, endDate: $endDate, param: $param, paramType: $paramType);
    }

    /**
     * Get the total views last month - Returns the total views
     * @return int|mixed
     */
    public function getLastMonth($param, $paramType)
    {
        // Current date/time in Y-m-d
        $startDate = date('Y-m-d', strtotime('-1 month'));
        $endDate = date('Y-m-d');

        return $this->getBetweenDates(startDate: $startDate, endDate: $endDate, param: $param, paramType: $paramType);
    }
}
