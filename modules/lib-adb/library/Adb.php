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
    protected string $bin = '';
    protected string $last_error = '';

    public function __construct(string $port = '5037')
    {
        $this->port = $port;
        $this->bin = \Mim::$app->config->libAdb->bin;
    }

    public function exec(string $command): ?string
    {
        $cmd = [
            $this->bin,
            '-P' . $this->port,
            $command
        ];

        $command = implode(' ', $cmd);

        return `$command`;
    }

    public function devices(): array
    {
        $cmd = 'devices -l';
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

    // protected string $port = '5037';
    // protected string $last_error = '';

    // protected function setError(string $message)
    // {
    //     $this->last_error = $message;
    //     return null;
    // }

    // public function attach(string $id): void
    // {
    //     $cmd = '-s ' . $id . ' attach';
    //     $this->exec($cmd);
    // }

    // public function connect(string $address): ?string
    // {
    //     $result = $this->exec('connect ' . $address);
    //     return $result;
    // }

    // public function detach(string $id): void
    // {
    //     $cmd = '-s ' . $id . ' detach';
    //     $this->exec($cmd);
    // }





    // public function getName(string $id): ?string
    // {
    //     $opts = ['ro.product.manufacturer', 'ro.product.name'];
    //     $cmd = '-s ' . $id . ' shell getprop ';
    //     $result = [];
    //     foreach ($opts as $opt) {
    //         $res = $this->exec($cmd . $opt);
    //         if (!$res) {
    //             return $this->setError('Unknow Error');
    //         } elseif (false !== strstr($res, 'not found')) {
    //             return $this->setError('Device not connected');
    //         } elseif (false !== strstr($res, 'unauthorized')) {
    //             return $this->setError('ADB Unauthorized');
    //         }

    //         $result[] = trim($res);
    //     }

    //     return implode(' ', $result);
    // }

    // public function pair(string $address, string $code)
    // {
    //     $result = $this->exec('pair ' . $address . ' ' . $code);
    //     return $result;
    // }

    // public function screenshot(string $id, string $target): void
    // {
    //     $cmd = '-s ' . $id . ' exec-out screencap -p > ' . $target;
    //     $this->exec($cmd);
    // }

    // public function single(string $id): void
    // {
    //     $cmd = [
    //         '--one-device',
    //         $id,
    //         'start-server'
    //     ];
    //     $this->exec(implode(' ', $cmd));
    // }

    // public function stop(): void
    // {
    //     $this->exec('kill-server');
    // }

    // public function lastError()
    // {
    //     return $this->last_error;
    // }
}
