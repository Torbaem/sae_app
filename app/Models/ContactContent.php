<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'map_url',
        'type',
        'styles'
    ];

    public function images()
    {
        return $this->hasMany(ContactImage::class);
    }
}