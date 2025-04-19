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
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                            <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Logout</a></li>
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

    <!-- Bootstrap JS (wajib untuk dropdown jalan) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
