<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_content_id',
        'image_path',
        'alt_text'
    ];

    public function content()
    {
        return $this->belongsTo(ContactContent::class, 'contact_content_id');
    }
}