<?php

/**
 * Erste Transparent Account Api
 *
 * Example for 10 latest transactions: $client->transactions()->limit(10)->get();
 * @link https://developers.erstegroup.com/docs/apis/bank.csas/bank.csas.v3%2FtransparentAccounts
 */

use GuzzleHttp\Client as GuzzleHttpClient;

class ErsteTransparentAccountApi
{
    protected string $webApiKey = 'xxx-xxx-xxx';

    /**
     * Account Number
     * Testing ID: 000000-2906478309
     */
    protected string $accountNumber = '000000-2906478309';

    protected GuzzleHttpClient $client;

    protected array $query = [];

    /**
     * GuzzleHTTPClient Constructor
     *
     * @param  string|null  $webApiKey     WEB-API-Key for API
     * @param  string|null  $accountNumber Account Number
     */
    public function __construct(string $accountNumber = null, string $webApiKey = null)
    {
        $this->accountNumber = $accountNumber ?: $this->accountNumber;
        $this->webApiKey = $webApiKey ?: $this->webApiKey;
        $this->client = new GuzzleHttpClient([
            'base_uri' => 'https://www.csas.cz/webapi/api/v3/transparentAccounts/',
            'headers' => ['WEB-API-key' => $this->webApiKey],
        ]);
    }

    public function getAccountInfo(): object
    {
        return json_decode(
            $this->client
                ->get($this->accountNumber)
                ->getBody()
                ->getContents()
        );
    }

    public function transactions(): self
    {
        return $this;
    }

    public function page($page): self
    {
        $this->query['page'] = $page;

        return $this;
    }

    public function limit($limit): self
    {
        $this->query['size'] = $limit;

        return $this;
    }

    public function sort($sort): self
    {
        if (! in_array($sort, ['processingDate', 'amount', 'sender'])) {
            throw new \Exception('available are one of following attributes: processingDate, amount, sender');
        }

        $this->query['sort'] = $sort;

        return $this;
    }

    public function order($order): self
    {
        if (! in_array($order, ['asc', 'desc'])) {
            throw new \Exception('available are one of following attributes: asc, desc');
        }

        $this->query['order'] = $order;

        return $this;
    }

    public function dateFrom($date): self
    {
        $this->query['dateFrom'] = $date;

        return $this;
    }

    public function dateTo($date): self
    {
        $this->query['dateTo'] = $date;

        return $this;
    }

    public function get(): object
    {
        return json_decode(
            $this->client
                ->get($this->accountNumber.'/transactions/', $this->buildQuery())
                ->getBody()
                ->getContents()
        );
    }

    public function buildQuery(): array
    {
        return ['query' => $this->query];
    }
}
