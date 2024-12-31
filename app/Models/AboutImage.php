<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'about_content_id',
        'image_path',
        'alt_text'
    ];

    /**
     * Relación con el contenido
     */
    public function content()
    {
        return $this->belongsTo(AboutContent::class, 'about_content_id');
    }

}