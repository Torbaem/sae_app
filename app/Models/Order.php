<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['total_price', 'status', 'shipping_status', 'session_id', 'user_address_id', 'created_by', 'updated_by'];
    
    use HasFactory;
    
    public function order_items() {
        return $this->hasMany(OrderItem::class);
    }
    
    public function user_address() {
        return $this->belongsTo(UserAddress::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
