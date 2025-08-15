<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Dunia Coating</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


    <style>
        body { overflow-x: hidden; }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #0d47a1;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: white;
        }

        .sidebar .nav-link.active {
            background-color: #1565c0;
            font-weight: bold;
        }

        .sidebar .nav-link:hover {
            background-color: #1976d2;
        }

        .content-wrapper {
            margin-left: 250px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .topbar {
            height: 60px;
            background-color: white;
            border-bottom: 1px solid #ddd;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .main-content {
            padding: 1.5rem;
        }

        .logout-box {
            padding: 1rem;
        }
    </style>
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar p-3">
        <div>
            <div class="mb-4 text-center">
                <div class="bg-white p-2 rounded mb-2">
                    <i class="bi bi-buildings text-primary" style="font-size: 2rem;"></i>
                </div>
                <h5 class="fw-bold mb-0">Dunia Coating</h5>
                <p class="small mb-0">Production Management</p>
            </div>

            @php $role = Auth::user()->Role ?? null; @endphp

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door me-2"></i> Dashboard
                    </a>
                </li>

                @if($role === 'admin' || $role === 'gudang')
                <li>
                    <a href="{{ route('inventory.index') }}" class="nav-link {{ Request::is('inventory*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam me-2"></i> Inventory
                    </a>
                </li>
                @endif

                @if($role === 'admin' || $role === 'manajer_produksi')
                <li>
                    <a href="{{ route('procurement.index') }}" class="nav-link {{ Request::is('procurement*') ? 'active' : '' }}">
                        <i class="bi bi-cart-plus me-2"></i> Procurement
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ Request::is('reports*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up me-2"></i> Reports
                    </a>
                </li>
                @endif

                @if($role === 'admin' || $role === 'pembelian')
                <li>
                    <a href="{{ route('pelanggan.index') }}" class="nav-link {{ Request::is('pelanggan*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i> Pelanggan
                    </a>
                </li>
                <li>
                    <a href="{{ route('production.index') }}" class="nav-link {{ Request::is('production*') ? 'active' : '' }}">
                        <i class="bi bi-hammer me-2"></i> Production
                    </a>
                </li>
                @endif

                <li>
                    <a href="{{ route('pesanan_produksi.index') }}" class="nav-link {{ Request::is('pesanan_produksi*') ? 'active' : '' }}">
                        <i class="bi bi-list-check me-2"></i> Order
                    </a>
                </li>

                <li>
                    <a href="{{ route('settings.index') }}" class="nav-link {{ Request::is('settings*') ? 'active' : '' }}">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </li>
            </ul>
        </div>

        {{-- Logout --}}
        <div class="logout-box">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="content-wrapper">
        {{-- Topbar --}}
        <div class="topbar">
            <input type="text" class="form-control w-25" placeholder="Search...">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <span class="me-2 fw-bold">{{ Auth::user()->Nama ?? 'User' }}</span>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->Nama ?? 'User') }}&background=0D8ABC&color=fff" width="32" class="rounded-circle" alt="Avatar">
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Your Profile</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item" type="submit">Sign out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Content from child view --}}
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    @stack('scripts')
</body>
</html>