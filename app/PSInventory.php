<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PSInventory extends Model {
    protected $table="parts_source_inventory";

    protected $fillable = ['id','thumbnailUrl','title','partNumber','brand','description',
        'detailUrl','models','price','images','oemListPrice','matchreason','user_id','qty','location'];
    protected $casts = [
        'images' => 'array',
        'models' => 'array',
        'options' => 'array',
        
    ];

}
