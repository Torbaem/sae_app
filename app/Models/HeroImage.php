<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroImage extends Model
{
    protected $fillable = [
        'hero_content_id',
        'image_path',
        'type'
    ];

    public function heroContent()
    {
        return $this->belongsTo(HeroContent::class);
    }
}