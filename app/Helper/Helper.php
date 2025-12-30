<?php

use Illuminate\Support\Str;

if (!function_exists('lang')) {

    function lang($id, $parameters = array(), $domain = 'messages', $locale = null) {

        if (\Illuminate\Support\Facades\Lang::has($id, $locale)) {
            return $id;
        }
        $translator = app('translator');
        if (!$translator->has($id, Config::get('app.locale'))) {
            $jsonString = file_get_contents(base_path('resources/lang/' . Config::get('app.locale') . '.json'));

            $data = json_decode($jsonString, true);

            $data[$id] = $id;

            $newJsonString = json_encode($data, JSON_PRETTY_PRINT);

            file_put_contents(base_path('resources/lang/' . Config::get('app.locale') . '.json'),
                    stripslashes($newJsonString));
            return $id; //.'?';
        } else {
            return $id;
        }
    }

}
if (!function_exists('at')) {

    function at($record, $name) {
        try {
            return $record->getFields()[$name];
        } catch (Exception $ex) {
            return "s";
        }
    }

}

if (!function_exists('user')) {

    function user() {
        if (session()->has('user')) {
            return session('user');
        }

        $user = auth()->user();

        if ($user) {
            session(['user' => $user]);
            return session('user');
        }

        return null;
    }

}

if (!function_exists('asset_url')) {


    function asset_url($path) {
        $path = 'user-uploads/' . $path;
        $storageUrl = $path;

        if (!Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }

}

if (!function_exists('dformat')) {


    function dformat($datetime, $notime = false) {
        if ($datetime == 'now') {
            if (!$notime) {
                $datetime = \Carbon\Carbon::now();
            } else {
                $datetime = \Carbon\Carbon::now()->startOfDay();
            }
        } else if ($datetime == '') {
            return '';
        }
        try {
            if (is_string($datetime)){
                $datetime = \Carbon\Carbon::parse($datetime)->format('d/m/Y H:i');
                return $datetime;
            }
            if ($notime) {
                return $datetime->format(config('app.dateformat'));
            } else {
                return $datetime->format(config('app.dateformat') . ' H:i');
            }
        } catch (Exception $ex) {
            return $datetime;
        }
    }

}

if (!function_exists('toCarbon')) {


    function toCarbon($datetime, $notime = false) {
        if ($datetime == '') {
            return $datetime;
        }
        try {

            if (!$notime) {
                return \Carbon\Carbon::createFromFormat(config('app.dateformat') . ' H:i', $datetime);
            } else {
                return \Carbon\Carbon::createFromFormat(config('app.dateformat'), $datetime);
            }
        } catch (Exception $ex) {
            return $datetime;
        }
    }

}

if (!function_exists('abortmodal_if')) {


    function abortmodal_if($permission, $code) {
        if (!$this->user->can($permission)) {
            return '';
        }
    }

}

if (!function_exists('selected')) {


    function selected($value, $option) {
        if ($value == $option) {
            return 'selected';
        }
        return '';
    }

}

if (!function_exists('nil')) {

    function nil($value) {
        if (isset($value))
            return $value;
        else
            return '';
    }

}

if (!function_exists('put_env')) {

    function put_env($values, $add = false) {

        if ($values instanceof Collection) {
            $values = $values->toArray();
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);


        foreach ($values as $envKey => $envValue) {
            $envKey = strtoupper($envKey);
            $str .= "\n"; // In case the searched variable is in the last line without \n
            $keyPosition = strpos($str, "{$envKey}=");
            $endOfLinePosition = strpos($str, "\n", $keyPosition);
            $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

            // If key does not exist, add it
            if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                if ($add) {
                    $str .= "{$envKey}={$envValue}\n";
                }
            } else {
                $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
            }
        }

        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) {
            return false;
        }

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return true;
    }

}
if (!function_exists('CalcOrderRental')) {

    /**
     * DEMO SKELETON: Calculate Order Rental
     * 
     * Original functionality:
     * - Calculated rental days based on bill_started and bill_completed dates
     * - Calculated total order cost based on equipment prices and rental days
     * - Updated order items with rental days and saved to database
     * - Handled manual updates and removed assets
     * 
     * For demo purposes, returns dummy data without any calculations.
     * In production, this would perform complex rental calculations.
     * 
     * @param mixed $order
     * @param bool $manual_update
     * @return stdClass
     */
    function CalcOrderRental($order, $manual_update = false) {
        // DEMO: Return dummy data instead of calculating
        $data = new stdClass();
        $data->orderTotal = 1250.00; // Dummy total
        $data->orderTotalDays = 15; // Dummy total days
        $data->orderRentalDays = 12; // Dummy rental days
        
        // Original logic removed:
        // - Date difference calculations
        // - Equipment price calculations
        // - Database updates
        
        return $data;
    }

}
if (!function_exists('setCookie')) {

    function setCookie($name, $value) {
        $minutes = 6000;
        $response = new Response('Set Cookie');
        $response->withCookie(cookie($name, $value, $minutes));
        return $response;
    }

}
if (!function_exists('truncstr')) {

    function truncstr($str, $length) {

        if (strlen($str) > $length) {
            return substr($str, 0, $length) . '...';
        } else {
            return $str;
        }
    }

}
if (!function_exists('getCookie')) {

    function getCookie($name) {
        $value = request()->cookie($name);
        return $value;
    }

}