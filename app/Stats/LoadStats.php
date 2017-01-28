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
            ['Time', 'Load Average (15m)',]
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
        
        // OS X / Unix
        $loadString = $this->startFrom($output, 'load averages:');
        if (is_null($loadString)) {
            
            // Ubuntu / Linux
            $loadString = $this->startFrom($output, "load average:");
            if (is_null($loadString)) {
                return ;
            }
        }
        
        $exp = explode(" ", str_replace(',', '', $loadString));
        if (count($exp) < 3) {
            return ;
        } 
        
        $this->minuteAvg = floatval(trim($exp[0]));
        $this->fiveMinuteAvg = floatval(trim($exp[1]));
        $this->fifteenMinuteAvg = floatval(trim($exp[2]));
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
    
    private function startFrom($string, $start)
    {
        $lowercase = strtolower($string);
        
        $pos = strpos($lowercase, $start);
        
        if ($pos === false) {
            return ;
        }
        
        return trim(substr($lowercase, $pos+strlen($start)));
    }
}
