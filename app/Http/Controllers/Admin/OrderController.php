<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with([
            'order_items.product.product_images',
            'user',
            'user_address'
        ])->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'total_price' => $order->total_price,
                'status' => $order->status === 'paid' ? 'Pagado' : 'Sin Pagar',
                'shipping_status' => $order->shipping_status,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'user_name' => $order->user ? $order->user->name : 'N/A',
                'user_address' => $order->user_address ? [
                    'type' => $order->user_address->type,
                    'address1' => $order->user_address->address1,
                    'address2' => $order->user_address->address2,
                    'city' => $order->user_address->city,
                    'state' => $order->user_address->state,
                    'zipcode' => $order->user_address->zipcode,
                    'country_code' => $order->user_address->country_code,
                    ] : null,
                'items' => $order->order_items->map(function ($item) {
                    return [
                        'product_name' => $item->product->title,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total' => $item->quantity * $item->unit_price,
                        'image' => $item->product->product_images->first() ? 
                                $item->product->product_images->first()->image : null
                    ];
                })
            ];
        });

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders
        ]);
    }

    public function updateShippingStatus(Request $request, $id)
    {
        $request->validate([
            'shipping_status' => 'required|in:Pendiente,en proceso,cancelado,entregado'
        ]);

        $order = Order::findOrFail($id);
        $order->shipping_status = $request->shipping_status;
        $order->save();

        return redirect()->back()->with('success', 'Estado de envío actualizado correctamente');
    }
}