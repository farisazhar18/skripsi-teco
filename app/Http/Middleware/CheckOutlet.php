<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOutlet
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (
            $user &&
            in_array($user->role, ['kasir', 'barista']) &&
            !session()->has('outlet_aktif')
        ) {
            return redirect()->route('outlet.pilih');
        }

        return $next($request);
    }
}