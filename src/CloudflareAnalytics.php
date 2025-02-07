<?php

namespace The3LabsTeam\PhpCloudflareAnalytics;

use DateInterval;
use DateTime;
use Dotenv\Dotenv;

class CloudflareAnalytics
{
    private string $apiToken;

    private string $zoneTag;

    protected string $endpoint;

    protected array $selectors = [];

    protected array $filters = [];

    protected array $orderBys = [];

    private $takes = [];

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
    }

    public function select(...$selectors)
    {
        foreach ($selectors as $selector) {
            [$key, $alias] = explode(' AS ', $selector);
            $this->selectors[$alias] = $key;
        }

        return $this;
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
    public function whereBetween(string $context, string $startDate, string $endDate)
    {
        $this->filters[$context] = [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return $this;
    }

    /**
     * Set the order by field and direction
     */
    public function orderBy(string $context, string $field, string $direction = 'ASC')
    {
        $this->orderBys[$context][] = $field.'_'.$direction;

        return $this;
    }

    /**
     * Get a specific number of results
     */
    public function take($alias, $limit)
    {
        $this->takes[$alias] = $limit;

        return $this;
    }

    /**
     * Get data
     */
    public function get(...$fields)
    {
        $queries = [];
        foreach ($this->selectors as $alias => $selector) {
            $filter = $this->filters[$alias] ?? [];
            $orderBy = array_filter($this->orderBys[$alias] ?? [], function ($order) {
                return strpos($order, 'datetime') === false;
            });
            $limit = isset($this->takes[$alias]) ? $this->takes[$alias] : 10;

            $startDate = $filter['startDate'] ?? (new DateTime)->sub(new DateInterval('PT1H'))->format('c');
            $endDate = $filter['endDate'] ?? (new DateTime)->format('c');

            // Formatta i campi nidificati
            $fieldsList = $this->formatFields($fields, $alias);

            $queries[] = <<<GRAPHQL
              $alias: $selector(
                  filter: {
                    datetime_gt: "$startDate",
                    datetime_lt: "$endDate"
                  }
                  limit: $limit
                  orderBy: [
                      {$this->formatOrderBy($orderBy)}
                  ]
              ) {
                  $fieldsList
              }
          GRAPHQL;
        }

        $query = <<<GRAPHQL
          query {
            viewer {
              zones(filter: {zoneTag: "$this->zoneTag"}) {
                {$this->formatQueries($queries)}
              }
            }
          }
      GRAPHQL;

        $response = $this->query($query);

        return $response;
    }

    private function formatFields(array $fields, $alias)
    {
        $formattedFields = [];
        foreach ($fields as $field) {
            $field = str_replace("$alias.", '', $field); // Rimuove il prefisso alias
            $parts = explode('.', $field);
            $current = &$formattedFields;
            foreach ($parts as $part) {
                if (! isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
        }

        return $this->buildFieldString($formattedFields);
    }

    private function buildFieldString(array $fields, $indent = 2)
    {
        $result = '';
        foreach ($fields as $key => $value) {
            $result .= str_repeat(' ', $indent).$key;
            if (is_array($value) && ! empty($value)) {
                $result .= " {\n".$this->buildFieldString($value, $indent + 2).str_repeat(' ', $indent)."}\n";
            } else {
                $result .= "\n";
            }
        }

        return $result;
    }

    private function formatOrderBy(array $orderBy)
    {
        return implode("\n", array_map(fn ($o) => $o, $orderBy));
    }

    private function formatQueries(array $queries)
    {
        return implode("\n", array_map(fn ($q) => $q, $queries));
    }

    private function formatTakes()
    {
        $takes = [];
        foreach ($this->takes as $alias => $limit) {
            if (is_int($limit)) {
                $takes[] = "{$alias}: {$limit}";
            }
        }

        return implode(', ', $takes);
    }
}
