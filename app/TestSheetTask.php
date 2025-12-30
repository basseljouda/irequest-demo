<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSheetTask extends Model
{
    protected $fillable = ['template_id', 'task', 'order'];
    
}
