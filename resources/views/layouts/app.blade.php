<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>Hachi Petshop</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #10B981; /* Warna hijau tailwind */
        }

        input:checked + .slider:before {
            transform: translateX(30px);
        }

        .slider-text {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            font-weight: bold;
            color: white;
        }

        .slider-text.on {
            left: 8px;
            display: none;
        }

        .slider-text.off {
            right: 8px;
        }

        input:checked + .slider .slider-text.on {
            display: block;
        }

        input:checked + .slider .slider-text.off {
            display: none;
        }
    </style>
</head>
<body class="h-full">
    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        <aside class="w-60 bg-green-100 flex flex-col border-r border-green-300">
            <!-- Sidebar Content -->
            <div class="bg-green-500 text-white font-bold text-xl px-4 py-3 flex items-center space-x-2 shadow">
                <img src="{{ asset('images/logo.jpeg') }}" class="w-10 h-10 rounded-full" alt="Logo">
                <span>Hachi Petshop</span>
            </div>

            {{-- Menu Navigasi --}}
            <nav class="flex flex-col space-y-2 text-sm px-4 py-8">
                <a href="/dashboard" class="flex items-center space-x-2 p-2 rounded-md transition-all
                    {{ request()->is('dashboard') ? 'bg-white text-black shadow' : 'hover:bg-green-200 text-black' }}">
                    <i class="bi bi-house-door"></i><span>Dashboard</span>
                </a>
                <a href="/admin/users" class="flex items-center space-x-2 p-2 rounded-md transition-all
                    {{ request()->is('admin/users*') ? 'bg-white text-black shadow' : 'hover:bg-green-200 text-black' }}">
                    <i class="bi bi-person"></i><span>Pengguna</span>
                </a>
                <a href="/pesanan" class="flex items-center space-x-2 p-2 rounded-md transition-all
                    {{ request()->is('pesanan*') ? 'bg-white text-black shadow' : 'hover:bg-green-200 text-black' }}">
                    <i class="bi bi-card-checklist"></i><span>Lihat Pesanan</span>
                </a>
                <a href="/produk" class="flex items-center space-x-2 p-2 rounded-md transition-all
                    {{ request()->is('produk*') ? 'bg-white text-black shadow' : 'hover:bg-green-200 text-black' }}">
                    <i class="bi bi-box"></i><span>Produk</span>
                </a>
                <a href="/rekap" class="flex items-center space-x-2 p-2 rounded-md transition-all
                    {{ request()->is('rekap*') ? 'bg-white text-black shadow' : 'hover:bg-green-200 text-black' }}">
                    <i class="bi bi-graph-up"></i><span>Rekap Penjualan</span>
                </a>
            </nav>
        </aside>

        {{-- Main Section --}}
        <div class="flex-1 flex flex-col">
            {{-- Topbar --}}
            <nav class="navbar navbar-expand-lg bg-white shadow p-3">
                <div class="container-fluid">
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="🔍 Pencarian" aria-label="Search">
                    </form>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Status Toko -->
                        <div class="d-flex align-items-center me-3">
                            <span class="me-2 fw-medium">Status Toko:</span>
                            <label class="switch">
                                <input type="checkbox" id="storeStatusToggle" checked>
                                <span class="slider">
                                    <span class="slider-text on">Buka</span>
                                    <span class="slider-text off">Tutup</span>
                                </span>
                            </label>
                        </div>

                        <!-- Notification Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                <li><a class="dropdown-item" href="#">Pesanan Baru</a></li>
                                <li><a class="dropdown-item" href="#">Produk Habis</a></li>
                                <li><a class="dropdown-item" href="#">Promo Aktif</a></li>
                            </ul>
                        </div>

                        <!-- Dropdown Profil -->
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle shadow-sm" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="dropdownMenuButton">
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ url('/') }}">Keluar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            {{-- Main Content --}}
            <main class="flex-1 bg-light min-vh-100 p-4">
                @yield('content')
            </main>
        </div>

    </div>

    <!-- Bootstrap JS (wajib untuk dropdown jalan) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fungsi untuk toggle status toko
        document.getElementById('storeStatusToggle').addEventListener('change', function() {
            const status = this.checked ? 'buka' : 'tutup';

            // Kirim status ke server (contoh dengan fetch API)
            fetch('/api/store-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const statusText = status === 'buka' ? 'Toko sekarang BUKA' : 'Toko sekarang TUTUP';
                    alert(statusText);
                } else {
                    alert('Gagal mengubah status toko');
                    this.checked = !this.checked; // Kembalikan toggle ke posisi semula
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !this.checked; // Kembalikan toggle ke posisi semula
            });
        });

        // Cek status toko saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/store-status')
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'tutup') {
                        document.getElementById('storeStatusToggle').checked = false;
                    }
                });
        });
    </script>
</body>
</html>
