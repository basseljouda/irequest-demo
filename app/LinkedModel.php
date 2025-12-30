<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkedModel extends Model {

    public static function boot() {
        parent::boot();
        
        // SuperAdmin bypasses all scoping
        if (auth()->check() && auth()->user()->role && auth()->user()->role->role_id == 1) {
            return; // Don't apply any scope restrictions
        }
        
        $restricted_sites = array();
        $table = (new static)->getTable();
        $hospitalColumn = $table.'.hospital_id';
        if (auth()->check()) {
            $staff = HospitalsStuff::where("user_id", auth()->user()->id)->first();
            if ($staff) {
                array_push($restricted_sites, $staff->hospital_id);
            }
            $sites = \App\Hospitals::whereIn('id', auth()->user()->sites??[])
                    ->orWhereIn('company_id', auth()->user()->idns??[])
                    ->orWhereIn('city', auth()->user()->regions??[])
                    ->pluck('id')->toArray();
            
            if (sizeof($sites) > 0){
                $restricted_sites = array_merge($restricted_sites, $sites);
            }
            
            if (sizeof($restricted_sites) > 0) {
                if (static::class == "App\Hospitals") {
                    $hospitalColumn = $table.'.id';
                }
                static::addGlobalScope(function ($query) use($hospitalColumn, $restricted_sites) {
                    $query->whereIn($hospitalColumn, $restricted_sites);
                });
            }
        }
    }

}
