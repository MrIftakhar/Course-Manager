<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Content extends Model
{
    protected $fillable = ['module_id','type','title','body','file_path','link','meta','position'];
    protected $casts = ['meta' => 'array'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }
}
