@extends('layouts.app') {{-- Pastikan ini menunjuk ke layout utama Anda --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Rekap Penjualan</h1>

    {{-- Filter Tanggal --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Filter Periode</h2>
        <form action="{{ route('rekap-penjualan.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="relative flex-grow max-w-sm">
                <label for="start_date_rekap" class="sr-only">Tanggal Mulai</label>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-calendar text-gray-400"></i>
                </div>
                <input type="date" id="start_date_rekap" name="start_date" class="form-input block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" value="{{ request('start_date') }}">
            </div>
            <div class="relative flex-grow max-w-sm">
                <label for="end_date_rekap" class="sr-only">Tanggal Akhir</label>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-calendar text-gray-400"></i>
                </div>
                <input type="date" id="end_date_rekap" name="end_date" class="form-input block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" value="{{ request('end_date') }}">
            </div>
            <button type="submit" class="btn btn-primary bg-green-500 hover:bg-green-600 border border-green-500 text-white rounded-md shadow-sm px-4 py-2 text-sm font-medium transition ease-in-out duration-150">Filter</button>
            <a href="{{ route('rekap-penjualan.index') }}" class="btn btn-outline-secondary border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-md shadow-sm px-4 py-2 text-sm font-medium transition ease-in-out duration-150">Reset Filter</a>
        </form>
    </div>

    {{-- Ringkasan Penjualan (Cards) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Penjualan</p>
                <p class="text-2xl font-bold text-gray-900">Rp{{ number_format($totalSales, 0, ',', '.') }}</p>
            </div>
            <i class="bi bi-cash-stack text-4xl text-green-500 opacity-75"></i>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Jumlah Transaksi</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($transactionCount, 0, ',', '.') }}</p>
            </div>
            <i class="bi bi-cart-fill text-4xl text-blue-500 opacity-75"></i>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Rata-rata Pesanan</p>
                <p class="text-2xl font-bold text-gray-900">Rp{{ number_format($averageOrderValue, 0, ',', '.') }}</p>
            </div>
            <i class="bi bi-graph-up-arrow text-4xl text-yellow-500 opacity-75"></i>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Transaksi Settlement</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($settlementCount, 0, ',', '.') }}</p>
            </div>
            <i class="bi bi-check-circle-fill text-4xl text-purple-500 opacity-75"></i>
        </div>
    </div>

    {{-- Penjualan per Status (Tabel) --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Penjualan per Status Transaksi</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Transaksi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($formattedSalesByStatus as $status => $data)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ucfirst($status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($data['count'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp{{ number_format($data['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm text-gray-500 text-center">Tidak ada data penjualan untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bagian Grafik Penjualan --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Tren Penjualan (Harian)</h2>
        <div style="height: 300px;"> {{-- Berikan tinggi agar grafik terlihat --}}
            <canvas id="salesChart"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
 
        const salesData = {
            labels: @json($salesChartData['labels']), // Array tanggal/label
            datasets: [{
                label: 'Total Penjualan (Rp)',
                data: @json($salesChartData['values']), // Array nilai penjualan
                backgroundColor: 'rgba(75, 192, 192, 0.5)', // Warna area bawah garis
                borderColor: 'rgba(75, 192, 192, 1)', // Warna garis
                borderWidth: 1,
                fill: true, // Mengisi area di bawah garis
                tension: 0.3 // Kehalusan garis pada grafik
            }]
        };

        new Chart(ctx, {
            type: 'line', // Jenis grafik: 'line' untuk tren
            data: salesData,
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting agar grafik menyesuaikan tinggi div parent
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Penjualan (Rp)'
                        },
                        ticks: {
                            // Fungsi callback untuk memformat label sumbu Y sebagai mata uang
                            callback: function(value, index, values) {
                                return 'Rp' + value.toLocaleString('id-ID'); 
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            // Fungsi callback untuk memformat tooltip saat hover
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush