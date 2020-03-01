<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use UAParser\Parser;

App::singleton(\App\AdapterInterface::class, function () {
//    $reader = new \GeoIp2\Database\Reader(resource_path() . '/GeoLite2/GeoLite2-City.mmdb');
//    return new \App\MaxMindAdapter($reader);

    return new \App\IpapiAdapter();
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/r/{code}', function ($code, \App\AdapterInterface $adapter) {
    $city = null;
    $countryCode = null;

    $link = \App\Link::where('short_code', $code)->get()->first();
    $source_link = $link->source_link;
    //$source_link = \App\Link::where('short_code', $code)->value('source_link');

    $adapter->parse(request()->ip());
    $city = $adapter->getCityName();
    $countryCode = $adapter->getCountryCode();

    //-1- maxmind
//    $reader = new \GeoIp2\Database\Reader(resource_path() . '/GeoLite2/GeoLite2-City.mmdb');
//    try {
//        $record = $reader->city(request()->ip());
//    } catch (\GeoIp2\Exception\AddressNotFoundException $exception) {
//        //$record = $reader->city('88.214.10.164');
//        $record = $reader->city(env('DEFAULT_IP_ADDR'));
//    } finally {
//        $city = $record->city->name;
//        $countryCode = $record->country->isoCode;
//    }

    //-2- ip-api.com
//    $result = file_get_contents('http://ip-api.com/json/' . request()->ip());
//    $data = json_decode($result, true);
//    if($data['status'] == 'fail') {
//        $result = file_get_contents('http://ip-api.com/json/' . env('DEFAULT_IP_ADDR'));
//        $data = json_decode($result, true);
//
//        $city = $data['city'];
//        $countryCode = $data['countryCode'];
//    }

    //-3- WhichBrowser/Parser-PHP
//    dd($_SERVER['HTTP_USER_AGENT']);
    $result = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
    //echo "You are using " . $result->toString();
    //browser
    $browser = $result->browser->toString();
//    $data[] = $browser;
    //engine
    $engine = $result->engine->toString();
//    $data[] = $engine;
    //os
    $os = $result->os->toString();
//    $data[] = $os;
    //device
    $device = $result->device->type;
//    $data[] = $device;
//    dd($data);

    //-4- yzalis/UAParser
//    $uaParser = new \UAParser\UAParser();
//    $result =  $uaParser->parse('Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20130406 Firefox/23.0.1');
////    $device = $result->getDevice();
//    dd($result);

    //-5- uap-php
//    $ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36";
////    dd($_SERVER['HTTP_USER_AGENT']);
//    $parser = Parser::create();
////    $result = $parser->parse($ua);
//    $result = $parser->parse($_SERVER['HTTP_USER_AGENT']);
//    print $result->ua->family;            // Safari
//    print $result->ua->major;             // 6
//    print $result->ua->minor;             // 0
//    print $result->ua->patch;             // 2
//    print $result->ua->toString();        // Safari 6.0.2
//    print $result->ua->toVersion();       // 6.0.2
//    die;

    $statistics = new \App\Statistic();
    $statistics->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $statistics->link_id = $link->id;
    $statistics->ip = request()->ip();
    $statistics->user_agent = request()->userAgent();
    $statistics->country_code = $countryCode;
    $statistics->city_name = $city;
    $statistics->browser = $browser;
    $statistics->engine = $engine;
    $statistics->os = $os;
    $statistics->device = $device;
    $statistics->save();

    dd($statistics);

//    if($source_link == NULL) {
//        return redirect('/');
//    } else {
//        return redirect($source_link);
//    }
});
