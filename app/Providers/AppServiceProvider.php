<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use App\CompanySetting;
use App\ThemeSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use App\LanguageSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Auth;
use Illuminate\Support\Facades\Cache;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);
        /*$this->currentDB = getCookie('irequest_connection');
        view()->share('irequest', $this->currentDB);
        if ($this->currentDB){    
            Config::set('database.default', $this->currentDB);
        }*/
        
        if (CompanySetting::count('*') > 0){
            $this->global = CompanySetting::first();
            $this->theme = ThemeSetting::first();
            $this->languageSettings = LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get();

            config(['app.name' => $this->global->company_name]);
            config(['app.url' => url('/')]);
            config(['app.timezone' => $this->global->timezone]);
            config(['app.dateformat' => $this->global->dateformat]);

            App::setLocale($this->global->locale);
            Carbon::setLocale($this->global->locale);
            setlocale(LC_TIME,$this->global->locale.'_'.strtoupper($this->global->locale));
            
            //Carbon::setToStringFormat('Y-m-d H:i');


            view()->share('languages', $this->languageSettings);
            view()->share('theme', $this->theme);
            view()->share('global', $this->global);
            view()->share('build_version', $this->global->vbuild);
            view()->share('calling_codes', $this->getCallingCodes());
            }
             Blade::directive('trans', function ($string) {
            return lang($string);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    
    public function getCallingCodes()
    {
        $codes = [];
        foreach(config('calling_codes.codes') as $code) {
            $codes = Arr::add($codes, $code['code'], array('name' => $code['name'], 'dial_code' => $code['dial_code']));
        }
        return $codes;
    }
}
