<?php


namespace App;


interface AdapterInterface
{
    public function parse(string $ip);
    public function getCountryCode();
    public function getCityName();
}
