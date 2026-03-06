<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request, OrderService $service)
    {
        $request->validate([
            'phone' => 'required|string',
            'items' => 'required|array',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $order = $service->create($request->all());

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
        ]);
    }
}
