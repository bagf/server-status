<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cache;

class StatsDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Cache::has(__CLASS__)) {
            abort(404, 'No stats found run php artisan update:stats to collect some data');
        }
        
        return view('stats-dashboard', ['data' => Cache::get(__CLASS__)]);
    }
}
