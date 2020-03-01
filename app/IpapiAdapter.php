<?php


namespace App;


class IpapiAdapter implements AdapterInterface
{
    protected  $data;

    public function parse(string $ip)
    {
        $result = file_get_contents('http://ip-api.com/json/' . $ip);
        $this->data = json_decode($result, true);
//        dd($this->data);
        if($this->data['status'] == 'fail') {
            $result = file_get_contents('http://ip-api.com/json/' . env('DEFAULT_IP_ADDR'));
            $this->data = json_decode($result, true);
        }
    }

    public function getCountryCode()
    {
        return $this->data['city'];
    }

    public function getCityName()
    {
        return $this->data['countryCode'];
    }
}
