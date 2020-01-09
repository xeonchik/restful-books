<?php

namespace App\Service;

use AppBase\Cache\CacheWrapper;
use Doctrine\Common\Cache\CacheProvider;
use GuzzleHttp\Client;

class ReferenceService
{
    /**
     * @var CacheWrapper
     */
    protected $cacheWrapper;

    public function __construct(CacheWrapper $cache)
    {
        $this->cacheWrapper = $cache;
    }

    /**
     * Retrieve countries from external source
     * @return array
     */
    public function getCountries() : array
    {
        return $this->cacheWrapper->wrap(function () {
            $client = new Client();
            $response = $client->get('https://api.hostaway.com/countries');
            $data = json_decode($response->getBody()->getContents());
            return (array)$data->result;
        }, ['key' => 'CountryCodesCache', 'ttl' => 3600]);
    }

    /**
     * Retrieve timezones from external source
     * @return array
     */
    public function getTimezones() : array
    {
        return $this->cacheWrapper->wrap(function () {
            $client = new Client();
            $response = $client->get('https://api.hostaway.com/timezones');
            $data = json_decode($response->getBody()->getContents());
            return (array)$data->result;
        }, ['key' => 'TimezoneCodesCache', 'ttl' => 3600]);
    }
}
