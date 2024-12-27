<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producer extends Model
{
    protected $fillable = [
        'name',
        'content',
        'youtube_url'
    ];

    // Relación con las imágenes
    public function images()
    {
        return $this->hasMany(ProducerImage::class);
    }
}
