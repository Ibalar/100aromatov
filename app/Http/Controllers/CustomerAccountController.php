<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerAccountController extends Controller
{
    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        $ordersCount = $customer->orders()->count();
        $ordersTotal = (float) $customer->orders()->sum('total_byn');
        $recentOrders = $customer->orders()->latest()->take(5)->get();

        return view('customer.account.dashboard', compact('customer', 'ordersCount', 'ordersTotal', 'recentOrders'));
    }

    public function orders()
    {
        $customer = Auth::guard('customer')->user();
        $orders = $customer->orders()->with('items')->latest()->paginate(10);

        return view('customer.account.orders', compact('customer', 'orders'));
    }

    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.account.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
        ]);

        if (filled($data['phone'] ?? null) && ! isValidBelarusMobilePhone($data['phone'])) {
            return back()->withErrors([
                'phone' => __('Введите корректный номер телефона белорусского оператора.'),
            ])->withInput();
        }

        if (filled($data['phone'] ?? null)) {
            $data['phone'] = formatBelarusMobilePhone($data['phone']) ?? $data['phone'];
        }

        $customer->update($data);

        return back()->with('status', __('Профиль обновлен'));
    }

    public function security()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.account.security', compact('customer'));
    }

    public function updatePassword(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $customer->password)) {
            return back()->withErrors(['current_password' => __('Текущий пароль указан неверно')]);
        }

        $customer->update(['password' => $data['password']]);

        return back()->with('status', __('Пароль обновлен'));
    }

    public function addresses()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.account.addresses', compact('customer'));
    }
}
