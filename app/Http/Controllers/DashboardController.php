<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_pesanan' => 2000,
            'pesanan_tertunda' => 100,
            'pesanan_selesai' => 5000,
            'pesanan_dibatalkan' => 500,

            'harian' => [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'data' => [200, 150, 300, 100, 400, 250, 200]
            ],

            'bulanan' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
                'data' => [400, 300, 500, 450, 600, 700, 500]
            ],

            'top_users' => [
                ['name' => 'Chidi Nurseni', 'total' => 10],
                ['name' => 'Kafidul Aulis', 'total' => 5],
                ['name' => 'M. Naufal', 'total' => 3],
            ],

            'top_products' => [
                ['name' => 'Kalung Pita', 'total' => 100],
                ['name' => 'Aks. Kandang', 'total' => 80],
                ['name' => 'obat antikutu', 'total' => 60],
            ]
        ];

        return view('dashboard', compact('data'));
    }
}
