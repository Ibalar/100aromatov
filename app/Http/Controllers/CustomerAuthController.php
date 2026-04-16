<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => __('Неверный e-mail или пароль')])
                ->withInput($request->only('email'));
        }

        /** @var Customer|null $customer */
        $customer = Auth::guard('customer')->user();
        if ($customer) {
            $customer->forceFill(['last_login_at' => now()])->save();
        }

        $request->session()->regenerate();

        app(WishlistService::class)->syncSessionToCustomer($customer);

        return redirect()->route('customer.account.dashboard');
    }

    public function showRegister()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:customers,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $customer = Customer::create([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        app(WishlistService::class)->syncSessionToCustomer($customer);

        return redirect()->route('customer.account.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}
