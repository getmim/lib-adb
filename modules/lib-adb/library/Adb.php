<?php
/**
 * Adb
 * @package lib-adb
 * @version 0.1.0
 */

namespace LibAdb\Library;

class Adb
{
    protected string $port = '5037';

    public function __construct(string $port = '5037')
    {
        $this->port = $port;
    }

    public function connect(string $address): ?string
    {
        $result = $this->exec('connect ' . $address);
        return $result;
    }

    public function exec(string $command): ?string
    {
        $bin = \Mim::$app->config->libAdb->bin;
        $cmd = [
            $bin,
            '-P' . $this->port,
            $command
        ];

        $command = implode(' ', $cmd);
        return `$command`;
    }

    public function devices(bool $long = false): array
    {
        $cmd = 'devices';
        if ($long) {
            $cmd .= ' -l';
        }
        $devices = $this->exec($cmd);
        if (!$devices) {
            $devices = '';
        }

        $devices = str_replace("\t", ' ', $devices);
        $devices = preg_replace('/ +/', ' ', $devices);

        $devices = explode(PHP_EOL, $devices);
        array_shift($devices);
        $result = [];
        foreach ($devices as $device) {
            $device = explode(' ', $device);
            if (!isset($device[1])) {
                continue;
            }

            $row = [
                'id' => array_shift($device)
            ];

            foreach ($device as $dev) {
                $dev = explode(':', $dev);
                if (!isset($dev[1])) {
                    $row['type'] = $dev[0];
                } else {
                    $row[$dev[0]] = $dev[1];
                }
            }
            $result[] = (object)$row;
        }

        return $result;
    }

    public function getName(string $id): ?string
    {
        $opts = ['ro.product.manufacturer', 'ro.product.name'];
        $cmd = '-s ' . $id . ' shell getprop ';
        $result = [];
        foreach ($opts as $opt) {
            $res = $this->exec($cmd . $opt);
            if (!$res || false !== strstr($res, 'not found')) {
                throw new \Exception('Device not connected');
            }
            $result[] = trim($res);
        }

        return implode(' ', $result);
    }

    public function pair(string $address, string $code)
    {
        $result = $this->exec('pair ' . $address . ' ' . $code);
        return $result;
    }
}
