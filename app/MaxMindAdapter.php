<?php


namespace App;


class MaxMindAdapter implements AdapterInterface
{
    protected $reader;
    protected $record = 0;

    public function __construct(\GeoIp2\Database\Reader $reader)
    {
        $this->reader = $reader;
    }

    public function parse(string $ip)
    {
        try {
            $this->record = $record = $this->reader->city($ip);
        } catch (\GeoIp2\Exception\AddressNotFoundException $exception) {
            $this->record = $record = $this->reader->city(env('DEFAULT_IP_ADDR'));
        }
    }

    public function getCountryCode()
    {
        return $this->record->country->isoCode;
    }

    public function getCityName()
    {
        return $this->record->city->name;
    }
}
