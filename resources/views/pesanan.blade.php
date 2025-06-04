@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 text-2xl font-bold text-gray-800">Lihat Pesanan</h2> {{-- Mengganti "Rekap Penjualan" menjadi "Lihat Pesanan" --}}
        </div>
    </div>

    <div class="card shadow-sm mb-6 bg-white rounded-lg">
        <div class="card-body p-6">
            <div class="flex flex-wrap items-center mb-4 gap-4">
                <form class="flex-grow-1 flex flex-wrap items-center gap-3" method="GET" action="{{ route('pesanan.index') }}"> {{-- Menggunakan route('pesanan.index') --}}
                    <div class="relative w-full md:w-auto flex-grow max-w-xs">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input type="text" class="form-input block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" name="order_id" placeholder="Cari Order ID di sini" value="{{ request('order_id') }}">
                    </div>

                    <div class="relative w-full md:w-auto flex-grow max-w-sm flex items-center">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-calendar text-gray-400"></i>
                        </div>
                        <input type="date" class="form-input block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-l-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" name="start_date" value="{{ request('start_date') }}">
                        <span class="bg-gray-50 border-y border-gray-300 text-gray-500 px-3 py-2 text-sm">-</span>
                        <input type="date" class="form-input block w-full pl-3 pr-3 py-2 border border-gray-300 rounded-r-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" name="end_date" value="{{ request('end_date') }}">
                    </div>

                    <button type="submit" class="btn btn-primary bg-green-500 hover:bg-green-600 border border-green-500 text-white rounded-md shadow-sm px-4 py-2 text-sm font-medium transition ease-in-out duration-150">Filter</button>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-md shadow-sm px-4 py-2 text-sm font-medium transition ease-in-out duration-150">Reset Filter</a>
                </form>
                <form action="{{ route('pesanan.export') }}" method="GET">
                    {{-- Meneruskan parameter filter yang sedang aktif sebagai hidden input --}}
                    <input type="hidden" name="order_id" value="{{ request('order_id') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-success bg-green-500 hover:bg-green-600 border border-green-500 text-white rounded-md shadow-sm px-4 py-2 text-sm font-medium flex items-center space-x-2 transition ease-in-out duration-150">
                        <i class="bi bi-download"></i><span>Ekspor</span>
                    </button>
                </form>
            </div>

            @if(request('start_date') && request('end_date'))
                <p class="text-sm text-gray-600 mb-3">Periode tanggal: <span class="font-semibold">{{ Carbon\Carbon::parse(request('start_date'))->format('d M Y') }}</span> - <span class="font-semibold">{{ Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}</span></p>
            @endif
            <p class="text-sm text-gray-600 mb-4">Menampilkan <span class="font-semibold">{{ $transactions->firstItem() ?? 0 }}</span> - <span class="font-semibold">{{ $transactions->lastItem() ?? 0 }}</span> dari total <span class="font-semibold">{{ $transactions->total() }}</span> hasil</p>


            <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal & Waktu</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Transaksi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Channel</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Nilai</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Pelanggan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No Telepon</th> {{-- Kolom baru --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th> {{-- Kolom baru --}}
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    {{ $transaction->order_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    Pembayaran
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    Online Payment
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = '';
                                        switch (strtolower($transaction->status)) {
                                            case 'settlement':
                                                $statusClass = 'bg-green-100 text-green-800';
                                                break;
                                            case 'pending':
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'expire':
                                            case 'cancel':
                                                $statusClass = 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                break;
                                        }
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                    Rp{{ number_format($transaction->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $transaction->user ? $transaction->user->username : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"> {{-- Data No Telepon --}}
                                    {{ $transaction->phone_number ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"> {{-- Data Alamat --}}
                                    {{ $transaction->address ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada transaksi ditemukan untuk filter ini.</td> {{-- colspan disesuaikan --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection