<?php

namespace App\CMMS;

class InventoryItems extends CMMSModel {

    protected $table = 'inventory_items';
    
    protected $dates = ['received_date'];


    public function assetno() {
        return $this->cat->category_id . $this->cat->subcategory_id . '-' . $this->id;
    }

    public function name() {
        return $this->cat->title . ', ' . $this->model_name . ', ' . (isset($this->manuf->title) ? $this->manuf->title : '');
    }
    
    public function RentalDesc() {
        return $this->cat->title . ' ' . $this->model_name . ' ' . (isset($this->manuf->title) ? $this->manuf->title : '').
                ' '.$this->serial.' '.$this->rental_old;
    }

    public function cat() {
        return $this->belongsTo(Categories::class, 'category');
    }

    public function manuf() {
        return $this->belongsTo(Manufacturer::class, 'manufacturer');
    }

    public function warehouse() {
        return $this->belongsTo(Locations::class, 'warehouse_location');
    }
    

}
