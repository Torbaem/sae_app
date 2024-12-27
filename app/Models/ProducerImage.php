<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProducerImage extends Model
{
    use HasFactory;

    protected $fillable = ['producer_id', 'image_path'];

    // Relación con productores
    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }
}
