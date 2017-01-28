<?php

namespace App\Stats;

use Cache;
use Carbon\Carbon;

class LoadStats implements HasStatData
{
    protected $minuteAvg;
    protected $fiveMinuteAvg;
    protected $fifteenMinuteAvg;
    
    public function getStatData()
    {
        $this->drawStats();
        return $this->updateAvgHistory();
    }
    
    private function updateAvgHistory()
    {
        $history = $this->getCachedHistory();
        
        if (count($history) >= 100) {
            $history = array_splice($history, 10);
        }
        
        $history[] = [
            'time' => time(),
            'avg' => $this->fifteenMinuteAvg,
        ];
        $historySize = count($history)-1;
        
        $avgArray = [
            ['Time', 'Load Status',]
        ];
        $avg = 0;
        $i = 1;
        foreach ($history as $key => $stat) {
            $avg += array_get($stat, 'avg', 0);
            
            if ($i > 9 || $key == $historySize) {
                $avgCalc = 0;
                if ($avg > 0) {
                    $avgCalc = $avg / $i;
                }
                $avgArray[] = [
                    Carbon::createFromTimestamp(array_get($stat, 'time'))."",
                    $avgCalc,
                ];
                $avg = 0;
                $i = 0;
            }
            $i++;
        }
        
        $this->setCachedHistory($history);
        
        return $avgArray;
    }
    
    private function drawStats()
    {
        $output = exec("uptime");
        
        $pos = strpos(strtolower($output), "load averages:");
        
        if ($pos === false) {
            return ;
        }
        
        $output = trim(substr($output, $pos+14));
        
        $exp = explode(" ", $output);
        if (count($exp) > 3) {
            return ;
        } 
        
        $this->minuteAvg = floatval(trim($exp[0].","));
        $this->fiveMinuteAvg = floatval(trim($exp[1]));
        $this->fifteenMinuteAvg = floatval(trim($exp[1]));
    }
    
    private function getCachedHistory()
    {
        if (Cache::has(__CLASS__)) {
            return Cache::get(__CLASS__);
        }
        
        return [];
    }
    
    private function setCachedHistory($history)
    {
        Cache::forever(__CLASS__, $history);
    }
}
