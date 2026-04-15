<?php
/**
 * Device
 * @package lib-adb
 * @version 0.1.0
 */

namespace LibAdb\Library;

class Device
{
    protected Adb $adb;
    protected string $id;

    public function __construct(string $id, string $port = '5037')
    {
        $this->adb = new Adb($port);
        $this->id = $id;
    }

    public function activity(): ?string
    {
        $cmd = 'shell "dumpsys activity activities | grep -E \'mCurrentFocus|mFocusedApp\'"';
        $result = $this->exec($cmd);

        if (!preg_match('!(\w+\.[\w\.]+\/[^ ]+)!', $result, $m)) {
            return null;
        }

        return $m[0];
    }

    public function back(): void
    {
        $cmd = 'shell input keyevent KEYCODE_BACK';
        $this->exec($cmd);
    }

    public function click(int $x, int $y)
    {
        $cmd = 'shell input tap ' . $x . ' ' . $y;
        $this->exec($cmd);
    }

    public function dimention(): array
    {
        $cmd = 'shell wm size';
        $result = $this->exec($cmd);

        if (!preg_match('!([0-9]+)x([0-9]+)!', $result, $m)) {
            return [];
        }

        return [
            'width' => (int)$m[1],
            'height' => (int)$m[2]
        ];
    }

    public function exec(string $command)
    {
        $cmd = '-s' . $this->id . ' ' . $command;
        return $this->adb->exec($cmd);
    }

    public function home(): void
    {
        $cmd = 'shell input keyevent KEYCODE_HOME';
        $this->exec($cmd);
    }

    public function power(): void
    {
        $cmd = 'shell input keyevent KEYCODE_POWER';
        $this->exec($cmd);
    }

    public function recent(): void
    {
        $cmd = 'shell input keyevent KEYCODE_APP_SWITCH';
        $this->exec($cmd);
    }

    public function screenshot(?string $file = null)
    {
        if (!$file) {
            $file = tempnam(sys_get_temp_dir(), 'lib-adb-');
        }

        $cmd = 'exec-out screencap -p > ' . $file;
        $this->exec($cmd);

        return $file;
    }

    public function source()
    {
        $cmd = 'exec-out uiautomator dump /dev/tty';
        $result = $this->exec($cmd);
        $result = str_replace('UI hierchary dumped to: /dev/tty', '', $result);
        return $result;
    }

    public function swipe(array $from, array $to, int $time)
    {
        $cmds = [
            'shell input swipe',
            $from['x'],
            $from['y'],
            $to['x'],
            $to['y'],
            $time
        ];
        $cmd = implode(' ', $cmds);

        $this->exec($cmd);
    }
}
