@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-500 text-white p-4 rounded shadow">
            <h2 class="text-lg">Total Pesanan</h2>
            <p class="text-2xl font-bold">{{ $data['total_pesanan'] }}</p>
        </div>
        <div class="bg-yellow-400 text-white p-4 rounded shadow">
            <h2 class="text-lg">Pesanan Tertunda</h2>
            <p class="text-2xl font-bold">{{ $data['pesanan_tertunda'] }}</p>
        </div>
        <div class="bg-green-500 text-white p-4 rounded shadow">
            <h2 class="text-lg">Pesanan Selesai</h2>
            <p class="text-2xl font-bold">{{ $data['pesanan_selesai'] }}</p>
        </div>
        <div class="bg-red-500 text-white p-4 rounded shadow">
            <h2 class="text-lg">Pesanan Dibatalkan</h2>
            <p class="text-2xl font-bold">{{ $data['pesanan_dibatalkan'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-bold mb-2">Total Pesanan Per Hari</h2>
            <canvas id="chartHarian"></canvas>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-bold mb-2">Total Pesanan Per Bulan</h2>
            <canvas id="chartBulanan"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-bold mb-2">Pengguna Paling Banyak Dipesan</h2>
            <table class="w-full">
                <thead><tr><th>Nama Pengguna</th><th>Total Pesanan</th></tr></thead>
                <tbody>
                    @foreach ($data['top_users'] as $user)
                        <tr><td>{{ $user['name'] }}</td><td>{{ $user['total'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-bold mb-2">Produk Paling Banyak Dipesan</h2>
            <table class="w-full">
                <thead><tr><th>Nama Produk</th><th>Total Pesanan</th></tr></thead>
                <tbody>
                    @foreach ($data['top_products'] as $product)
                        <tr><td>{{ $product['name'] }}</td><td>{{ $product['total'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxHarian = document.getElementById('chartHarian');
    const chartHarian = new Chart(ctxHarian, {
        type: 'line',
        data: {
            labels: {!! json_encode($data['harian']['labels']) !!},
            datasets: [{
                label: 'Pesanan',
                data: {!! json_encode($data['harian']['data']) !!},
                backgroundColor: 'rgba(0, 128, 0, 0.2)',
                borderColor: 'green',
                fill: true
            }]
        }
    });

    const ctxBulanan = document.getElementById('chartBulanan');
    const chartBulanan = new Chart(ctxBulanan, {
        type: 'bar',
        data: {
            labels: {!! json_encode($data['bulanan']['labels']) !!},
            datasets: [{
                label: 'Pesanan',
                data: {!! json_encode($data['bulanan']['data']) !!},
                backgroundColor: 'green'
            }]
        }
    });
</script>
@endsection
