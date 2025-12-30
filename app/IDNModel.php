<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IDNModel extends Model {

    public static function boot() {
        parent::boot();
        
        // SuperAdmin bypasses all scoping
        if (auth()->check() && auth()->user()->role && auth()->user()->role->role_id == 1) {
            return; // Don't apply any scope restrictions
        }
        
        if (auth()->check()) {
            $col = 'company_id';
            $companies = array();
            $staff = HospitalsStuff::where("user_id", auth()->user()->id)->first();
            
            if ($staff) {
                array_push($companies, $staff->hospital->company_id);
            }
            $user_companies = Company::WhereIn('id', auth()->user()->idns??[])
                    ->pluck('id')->toArray();
            
            if (sizeof($user_companies) > 0){
                $companies = array_merge($companies, $user_companies);
            }
            
            if (sizeof($companies) > 0) {
                if (static::class == "App\Company") {
                    $col = 'companies.id';
                }
                static::addGlobalScope(function ($query) use($col,$companies) {
                    $query->whereIn($col, $companies);
                });
            }
        }
    }

}
