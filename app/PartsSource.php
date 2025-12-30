<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartsSource extends Model {
    protected $table="parts_source";

    protected $fillable = ['id','thumbnailUrl','title','partNumber','brand','description',
        'detailUrl','models','price','images','oemListPrice','matchreason','user_id','qty','location'];
    protected $casts = [
        'images' => 'array',
        'models' => 'array',
        'options' => 'array',
        
    ];

}
