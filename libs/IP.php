<?php
/**
 * IP 信息
 *
 * @author luolongf <luolongf@gmail.com>
 * @date 2022-05-14
 * @time 8:28
 */

namespace Luolongfei\Libs;

use GuzzleHttp\Client;

class IP extends Base
{
    const TIMEOUT = 2.14;

    /**
     * 匹配 ip 的正则
     */
    const REGEX_IP = '/(?:\d{1,3}\.){3}\d{1,3}/u';

    const REGEX_LOC = '/^.*：(?P<country>[^\s]+?)\s+?(?P<region>[^\s]+?)\s+?(?P<city>[^\s]+?)\s/iu';

    /**
     * @var string ip 地址
     */
    public static $ip = '';

    /**
     * @var string 位置信息
     */
    public static $loc = '';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string 用于查询 ip 的地址
     */
    protected $url;

    public function init()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.64 Safari/537.36',
            ],
            'cookies' => false,
            'timeout' => self::TIMEOUT,
            'verify' => config('verify_ssl'),
            'debug' => config('debug'),
        ]);

        $this->url = is_chinese() ? 'https://myip.ipip.net' : 'https://ipinfo.io/json';
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get()
    {
        try {
            if (!self::$ip && !self::$loc) {
                $res = $this->client->get($this->url);
                $body = $res->getBody()->getContents();

                $this->matchIpInfo($body);
            }

            return sprintf(lang('100130'), self::$ip, self::$loc);
        } catch (\Exception $e) {
            Log::error(lang('100132') . $e->getMessage());

            return lang('100131');
        }
    }

    /**
     * 匹配 ip 信息
     *
     * @param $body
     *
     * @return bool
     */
    protected function matchIpInfo($body)
    {
        if (is_chinese()) {
            if (preg_match(self::REGEX_IP, $body, $m)) {
                self::$ip = $m[0];
            }
            if (preg_match(self::REGEX_LOC, $body, $m)) {
                self::$loc = sprintf('%s %s %s', $m['country'], $m['region'], $m['city']);
            }

            return true;
        }

        $body = (array)json_decode($body, true);
        self::$ip = $body['ip'] ?? '';
        self::$loc = sprintf('%s %s %s', $body['country'] ?? '', $body['region'] ?? '', $body['city'] ?? '');

        return true;
    }
}