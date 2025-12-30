<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachment';
    
    protected $fillable = ['user_id', 'folder'];
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function files() {
        return $this->hasMany(AttachmentFile::class, 'attachment_id');
    }
    
    public static function orders()
    {
        return self::with('files')
            ->where('folder', 'orders')
            ->get();
    }
    
    public static function tickets()
    {
        return self::with('files')
            ->where('folder', 'tickets')
            ->get();
    }
    
}
