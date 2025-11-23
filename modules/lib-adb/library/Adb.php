<?php
/**
 * Adb
 * @package lib-adb
 * @version 0.1.0
 */

namespace LibAdb\Library;

class Adb
{
    public static function exec(string $command): ?string
    {
        $bin = \Mim::$app->config->libAdb->bin;
        $command = $bin . ' ' . $command;
        return `$command`;
    }

    public static function devices(bool $long = false): array
    {
        $cmd = 'devices';
        if ($long) {
            $cmd .= ' -l';
        }
        $devices = self::exec($cmd);
        if (!$devices) {
            $devices = '';
        }
        $devices = str_replace("\t", ' ', $devices);
        $devices = preg_replace('/ +/', ' ', $devices);

        $devices = explode(PHP_EOL, $devices);
        $result = [];
        foreach ($devices as $device) {
            $device = explode(' ', $device);
            if (!isset($device[1]) || $device[1] != 'device') {
                continue;
            }

            $row = [
                'id' => array_shift($device)
            ];
            array_shift($device);

            foreach ($device as $dev) {
                $dev = explode(':', $dev);
                if (!isset($dev[1])) {
                    continue;
                }

                $row[$dev[0]] = $dev[1];
            }
            $result[] = (object)$row;
        }

        return $result;
    }

    public static function getName(string $id): ?string
    {
        $opts = ['ro.product.manufacturer', 'ro.product.name'];
        $cmd = '-s ' . $id . ' shell getprop ';
        $result = [];
        foreach ($opts as $opt) {
            $res = self::exec($cmd . $opt);
            if (!$res || false !== strstr($res, 'not found')) {
                throw new \Exception('Device not connected');
            }
            $result[] = trim($res);
        }

        return implode(' ', $result);
    }

    public static function pair(string $address, string $code)
    {
        $result = self::exec('pair ' . $address . ' ' . $code);
        return $result;
    }
}
