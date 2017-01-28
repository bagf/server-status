<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use App\Http\Controllers\StatsDashboardController;
use App\Stats\LoadStats;
use App\Stats\PidStatus;
use Cache;

class UpdateStats extends Command implements SelfHandling
{
    protected $signature = 'update:stats';
    
    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $dashboard = [
            'hostname' => gethostname(),
            'load_stats' => (new LoadStats)->getStatData(),
            'daemons' => $this->loadDeamons(config('pidstatus', [])),
        ];
        
        return Cache::forever(StatsDashboardController::class, $dashboard);
    }
    
    protected function loadDeamons($configs)
    {
        $daemons = [];
        
        foreach ($configs as $config) {
            $pid = array_get($config, 'pid');
            $name = array_get($config, 'name');
            
            if (is_null($pid) || is_null($name)) {
                continue;
            }
            
            $daemons[] = (new PidStatus($pid, $name))->getStatData();
        }
        
        return $daemons;
    }
}
