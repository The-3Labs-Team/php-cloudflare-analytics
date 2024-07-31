<?php

namespace The3LabsTeam\PhpCloudflareAnalytics;

class CloudflareAnalytics
{
    public string $api_token;

    public string $endpoint;

    public function __construct()
    {
        $this->api_token = env('CLOUDFLARE_API_TOKEN');
        $this->endpoint = 'https://api.cloudflare.com/client/v4/graphql';
    }

    protected function graphQLQuery($query)
    {
        $ch = curl_init($this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.$this->api_token,
            ],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Get the views between two dates - Returns an array with the date as key and the views as value
     *
     * @return array
     */
    public function getViewsBetweenDates($startDate, $endDate, $zoneTag)
    {
        $query = <<<GRAPHQL
            query {
              viewer {
                zones(filter: {zoneTag: "$zoneTag"}) {
                  httpRequests1dGroups(
                    limit: 1000
                    filter: {
                      date_geq: "$startDate"
                      date_lt: "$endDate"
                    }
                  ) {
                    dimensions {
                      date
                    }
                    sum {
                      requests
                    }
                  }
                }
              }
            }
        GRAPHQL;

        $response = $this->graphQLQuery($query);
        $response = $response['data']['viewer']['zones'][0]['httpRequests1dGroups'];

        $parsedResponse = [];
        foreach ($response as $key => $value) {
            $parsedResponse[$value['dimensions']['date']] = $value['sum']['requests'];
        }
        //order by date
        ksort($parsedResponse);

        return $parsedResponse;
    }

    /**
     * Get the total views between two dates - Returns the total views
     *
     * @return int|mixed
     */
    public function getTotalViewsBetweenDates($startDate, $endDate, $zoneTag)
    {
        $query = <<<GRAPHQL
            query {
              viewer {
                zones(filter: {zoneTag: "$zoneTag"}) {
                  httpRequests1dGroups(
                    limit: 1000
                    filter: {
                      date_geq: "$startDate"
                      date_lt: "$endDate"
                    }
                  ) {
                    sum {
                      requests
                    }
                  }
                }
              }
            }
        GRAPHQL;

        $response = $this->graphQLQuery($query);
        $response = $response['data']['viewer']['zones'][0]['httpRequests1dGroups'];

        $total = 0;
        foreach ($response as $key => $value) {
            $total += $value['sum']['requests'];
        }

        return $total;
    }
}
