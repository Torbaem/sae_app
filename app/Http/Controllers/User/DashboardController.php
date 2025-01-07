<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Order::with([
            'order_items.product.product_images',
            'order_items.product.brand', 
            'order_items.product.category',
            'user_address'
        ])
        ->where('created_by', auth()->id())
        ->latest()
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->id,
                'total_price' => $order->total_price,
                'shipping_status' => $order->shipping_status,
                'payment_status' => $order->status, // 'paid' o 'unpaid'
                'created_at' => $order->created_at,
                'user_address' => $order->user_address ? [
                    'address1' => $order->user_address->address1,
                    'city' => $order->user_address->city,
                    'state' => $order->user_address->state,
                    'zipcode' => $order->user_address->zipcode,
                ] : null,
                'order_items' => $order->order_items->map(function ($item) {
                    $firstImage = $item->product->product_images->first();
                    return [
                        'id' => $item->id,
                        'product' => [
                            'title' => $item->product->title,
                            'price' => $item->product->price,
                            'brand' => [
                                'name' => $item->product->brand->name
                            ],
                            'category' => [
                                'name' => $item->product->category->name
                            ],
                            'image_url' => $firstImage ? '/' . $firstImage->image : null
                        ]
                    ];
                })
            ];
        });

        return Inertia::render('User/Dashboard', ['orders' => $orders]);
    }

    public function updateShippingStatus(Request $request, Order $order)
    {
        if ($order->created_by !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($order->shipping_status !== 'en proceso') {
            return response()->json(['message' => 'Solo se pueden marcar como entregados los pedidos en proceso'], 400);
        }

        $order->update([
            'shipping_status' => 'entregado'
        ]);
        
        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
}