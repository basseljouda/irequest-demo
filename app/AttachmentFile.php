<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class AttachmentFile extends Model
{
    use SoftDeletes;
    
    protected $table = 'attachment_files';
    
    protected $fillable = ['attachment_id', 'filename','extension'];
    
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            
            if (! $model->isForceDeleting()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

}
