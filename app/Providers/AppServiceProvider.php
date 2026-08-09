<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        // Bagian ini akan jalan di semua controller
        $this->middleware(function ($request, $next) {
            $outlet = $request->route('outlet');
            if ($outlet) {
                $keranjang = session('keranjang_' . $outlet, []);
                $jumlahKeranjang = collect($keranjang)->sum('jumlah');
                $totalKeranjang = collect($keranjang)->sum('subtotal');

                // Share ke semua view
                View::share('jumlahKeranjang', $jumlahKeranjang);
                View::share('totalKeranjang', $totalKeranjang);
            }
            return $next($request);
        });
    }
}