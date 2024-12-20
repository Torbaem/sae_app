<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditableContent extends Model
{
    protected $fillable = [
        'section_key', 
        'page', 
        'content_type', 
        'item_order', 
        'title', 
        'description', 
        'image_path', 
        'video_url', 
        'video_type',
        'additional_data',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'additional_data' => 'array',
        'is_active' => 'boolean'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}