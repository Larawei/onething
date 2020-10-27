<?php

namespace Larawei\Onething;

use GuzzleHttp\Client;

class Onething
{
    protected $app_id;
    protected $app_key;

    protected $salt;
    protected $request_url;

    protected $guzzleOptions = [];

    public function __construct(string $app_id, string $app_key, string $salt, string $request_url)
    {
        $this->app_id  = $app_id;
        $this->app_key = $app_key;
        $this->salt    = $salt;
        $this->request_url = $request_url;
    }

    // get device info by sn
    public function getDevice(string $sn = '')
    {
        $prepare = array_filter(['sn' => $sn]);

        return $this->request($prepare, 'device/info');
    }


    // get all device list
    public function getDevices(int $pagePos = 1, int $pageSize = 50)
    {
        $prepare = array_filter([
            'pagePos'   => $pagePos,
            'pageSize'  => $pageSize
        ]);

        return $this->request($prepare, 'device/devicelist');
    }

    // get device income by sn
    public function getDeviceIncomes(string $sn = '', string $month = '')
    {
        // default $month is current month
        if ($month === '') $month = date('Y') . date('m');

        $prepare = array_filter([
            'sn'    => $sn,
            'month' => $month
        ]);

        return $this->request($prepare, 'device/incomelist');
    }

    // get device indicator by sn
    public function getDeviceIndicator(string $sn = '')
    {
        $prepare = array_filter([
            'sn'    => $sn,
        ]);

        return $this->request($prepare, 'device/indicator');
    }

    public function sign(array $prepare)
    {
        ksort($prepare);

        $signSource = '';

        foreach ($prepare as $key => $value) {
            $signSource .= $key . '=' . $value . '&';
        }

        $signSource = $signSource . 'AppKey=' . $this->app_key;

        return strtoupper( md5( hash('sha512', $signSource) ) );
    }

    public function request($query, $request_case)
    {
        $baseQuery = [
            'appID'     => $this->app_id,
            'nonce'     => $this->salt,
            'timestamp' => time()
        ];

        $query = array_merge($query, $baseQuery);

        $query['sign'] = $this->sign($query);

        $response = $this->getHttpClient()->post($this->request_url . $request_case, [
            'json' => $query,
        ])->getBody()->getContents();

        return $response;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

}