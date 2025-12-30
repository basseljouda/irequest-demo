<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Auth;

class SwitchClientDatabase
{
    public function handle($request, Closure $next)
    {
        //logger("2");
        logger(Cache::get('1'));
        // Get the selected client database from the request (e.g., from the dropdown)
        /*$selectedClientDatabase = $request->input('client');

        if ($selectedClientDatabase) {
            // Set the database connection based on the selected client database
            Config::set('database.default', $selectedClientDatabase);
            
            $request->session()->regenerate();
            //Cache::put('db', 'value', $selectedClientDatabase);
            //DB::reconnect();
        }*/
        if (Auth::check()) {
            Config::set('database.default', Cache::get('user_'.Auth::id().'_connection'));
            

        }

        return $next($request);
    }
}
