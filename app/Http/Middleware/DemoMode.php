<?php

namespace App\Http\Middleware;

use App\Demo\DemoData;
use App\Demo\DemoPages;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Stops demo requests before authentication, model binding, or real controllers. */
class DemoMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled')) {
            return $next($request);
        }

        $data = new DemoData;
        $name = $request->route()->getName() ?? '';
        $role = $request->session()->get('demo.role', 'customer');
        if (str_starts_with($name, 'admin.')) {
            $role = 'super_admin';
        } elseif (str_starts_with($name, 'owner.')) {
            $role = 'hotel_owner';
        } elseif (str_starts_with($name, 'customer.')) {
            $role = 'customer';
        }
        Auth::guard('web')->setUser($data->user($role));

        if ($name === 'demo.role') {
            $role = $request->route('role');
            abort_unless(in_array($role, ['customer', 'hotel_owner', 'super_admin'], true), 404);
            $request->session()->put('demo.role', $role);

            return redirect()->route($this->landing($role));
        }

        if (! $request->isMethodSafe()) {
            if ($request->is('login', 'register')) {
                return redirect()->route($this->landing($role));
            }
            if ($name === 'logout') {
                $request->session()->forget('demo.role');

                return redirect()->route('home');
            }
            if ($name === 'customer.bookings.store') {
                return redirect()->route('customer.bookings.show', 1)
                    ->with('success', 'Demo: booking simulation complete. No reservation or payment was made.');
            }

            return redirect()->back(fallback: route($this->landing($role)))
                ->with('success', 'Demo: action simulated. Sample data has not been changed.');
        }

        if ($name === 'dashboard') {
            return redirect()->route($this->landing($role));
        }
        if ($name === 'notifications.read' || $name === 'verification.verify') {
            return redirect()->route($this->landing($role));
        }

        return app(DemoPages::class)->render($request, $data, $role);
    }

    private function landing(string $role): string
    {
        return match ($role) {
            'super_admin' => 'admin.dashboard',
            'hotel_owner' => 'owner.dashboard',
            default => 'home',
        };
    }
}
