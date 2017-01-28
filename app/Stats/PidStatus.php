<?php

namespace App\Stats;

use Cache;
use Carbon\Carbon;

class PidStatus implements HasStatData
{
    protected $pidFile;
    protected $name;
    protected $cache;

    public function __construct($pidFile, $name)
    {
        $this->pidFile = $pidFile;
        $this->name = $name;
        
        $cache = (is_numeric($pidFile)?$name:$pidFile);
        
        $this->cache = __CLASS__.md5($cache);
    }

    public function getStatData()
    {
        $history = Cache::get($this->cache, function () {
            return array_fill(0, 90, [
                'time' => time(),
                'isup' => null,
            ]);
        });
        
        if (count($history) >= 100) {
            $history = array_splice($history, 10);
        }
        
        $active = $this->isPidActive();
        
        $history[] = [
            'time' => time(),
            'isup' => $active,
        ];
        $historySize = count($history)-1;
        
        $array = [];
        $i = 1;
        $up = null;
        $time = null;
        foreach ($history as $key => $stat) {
            if (is_null($up) || $up === true) {
                if (!is_null(array_get($stat, 'isup'))) {
                    $up = array_get($stat, 'isup');
                    $time = Carbon::createFromTimestamp(array_get($stat, 'time'))."";
                }
            }
            
            if ($i > 9 || $key == $historySize) {
                $array[] = [
                    'time' => $time,
                    'isup' => $up,
                ];
                $i = 0;
                $up = null;
            }
            $i++;
        }
        
        Cache::forever($this->cache, $history);
        
        return [
            'name' => $this->name,
            'isup' => $active,
            'history' => $array,
        ];
    }
    
    private function isPidActive()
    {
        if (is_numeric($this->pidFile)) {
            $pid = $this->pidFile;
        } else {
            if (!is_readable($this->pidFile)) {
                return ;
            }

            $pid = intval(file_get_contents($this->pidFile));
        }
        
        $output = [];
        exec("ps -p ".escapeshellarg($pid), $output);
        
        return count($output) > 1;
    }
}
