<?php
/**
 * Adb
 * @package lib-adb
 * @version 0.1.0
 */

namespace LibAdb\Library;

use Mim\Library\Fs;
use Cli\Library\Bash;

class Adb
{
    protected bool $is_cli = false;
    protected string $port = '5037';
    protected string $bin = '';
    protected string $last_error = '';
    protected $logger = null;

    protected function echo(string $text)
    {
        if (!$this->is_cli) {
            return;
        }

        $tx = '    ';
        $tx .= $text;
        Bash::echo($tx);
    }

    protected function defineLogger()
    {
        $log = \Mim::$app->config->libAdb->log ?? false;
        if (!$log) {
            return;
        }

        $base = BASEPATH . '/etc/log/lib-adb/';
        $path = date('Y/m/d/H/i/s/');
        $basepath = $base . $path;
        $basefile = $basepath . '/' . uniqid() . '.txt';

        Fs::mkdir($basepath);
        $this->logger = fopen($basefile, 'w');
    }

    public function __construct(string $port = '5037')
    {
        $this->is_cli = php_sapi_name() === 'cli';
        $this->port = $port;
        $this->bin = \Mim::$app->config->libAdb->bin;
        $this->defineLogger();
    }

    public function exec(string $command): ?string
    {
        $cmd = [
            $this->bin,
            '-P' . $this->port,
            $command
        ];

        $command = implode(' ', $cmd);

        if ($this->logger) {
            fwrite($this->logger, date('Y-m-d H:i') . PHP_EOL);
            fwrite($this->logger, $command . PHP_EOL);
        }

        $this->echo($command);

        $result = `$command`;

        if ($this->logger) {
            if (is_null($result)) {
                $res = 'NULL';
            } else {
                $res = substr($result, 0, 100);
            }

            $res .= PHP_EOL;
            fwrite($this->logger, $res);
            fwrite($this->logger, str_repeat('-', 80) . PHP_EOL);
        }

        return $result;
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
}
