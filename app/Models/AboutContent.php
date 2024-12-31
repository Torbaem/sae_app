<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'styles'
    ];

    /**
     * Los tipos de contenido disponibles
     */
    const TYPES = [
        'CARD' => 'card',
        'CONTAINER' => 'container'
    ];

    /**
     * Relación con las imágenes
     */
    public function images()
    {
        return $this->hasMany(AboutImage::class);
    }

    /**
     * Scope para obtener solo las cards
     */
    public function scopeCards($query)
    {
        return $query->where('type', self::TYPES['CARD']);
    }

    /**
     * Scope para obtener solo los containers
     */
    public function scopeContainers($query)
    {
        return $query->where('type', self::TYPES['CONTAINER']);
    }
}