<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Inventory System'))</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700|Poppins:400,500,600,700" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-light">
    <div id="app" class="d-flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar bg-gradient-primary text-light">
            <div class="sidebar-header p-3 d-flex justify-content-between align-items-center">
                <h3 class="mb-0 fw-bold">{{ config('app.name', 'Inventory System') }}</h3>
                <button id="sidebarCollapseSmall" class="btn btn-link text-light d-md-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav>
                <ul class="nav flex-column">
                    @php
                        $menuItems = [
                            ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'url' => '#'],
                            ['icon' => 'fas fa-boxes', 'text' => 'Inventory', 'url' => '#', 'submenu' => [
                                ['text' => 'All Products', 'url' => '#'],
                                ['text' => 'Add New Product', 'url' => '#'],
                                ['text' => 'Categories', 'url' => '#'],
                                ['text' => 'Stock Levels', 'url' => '#'],
                            ]],
                            ['icon' => 'fas fa-exchange-alt', 'text' => 'Transactions', 'url' => '#', 'submenu' => [
                                ['text' => 'Inbound', 'url' => '#'],
                                ['text' => 'Outbound', 'url' => '#'],
                            ]],
                            ['icon' => 'fas fa-truck', 'text' => 'Suppliers', 'url' => '#'],
                            ['icon' => 'fas fa-chart-bar', 'text' => 'Reports', 'url' => '#', 'submenu' => [
                                ['text' => 'Inventory Valuation', 'url' => '#'],
                                ['text' => 'Stock Movement', 'url' => '#'],
                                ['text' => 'Low Stock Alert', 'url' => '#'],
                            ]],
                            ['icon' => 'fas fa-users', 'text' => 'Users', 'url' => '#'],
                            ['icon' => 'fas fa-cog', 'text' => 'Settings', 'url' => '#'],
                        ];
                    @endphp

                    @foreach($menuItems as $item)
                        <li class="nav-item">
                            <a class="nav-link text-light d-flex align-items-center justify-content-between"
                               href="{{ isset($item['submenu']) ? '#'.$item['text'].'Submenu' : $item['url'] }}"
                               @if(isset($item['submenu']))
                               data-bs-toggle="collapse"
                               aria-expanded="false"
                               @endif
                            >
                                <span><i class="{{ $item['icon'] }} me-3"></i> {{ $item['text'] }}</span>
                                @if(isset($item['submenu']))
                                    <i class="fas fa-chevron-down"></i>
                                @endif
                            </a>
                            @if(isset($item['submenu']))
                                <ul class="nav collapse" id="{{ $item['text'] }}Submenu">
                                    @foreach($item['submenu'] as $subItem)
                                        <li class="nav-item">
                                            <a class="nav-link text-light ps-5" href="{{ $subItem['url'] }}">{{ $subItem['text'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        <!-- Page Content -->
        <div id="content" class="content flex-grow-1 d-flex flex-column">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary d-none d-md-block">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button type="button" id="sidebarCollapseSmall" class="btn btn-primary d-md-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <img src="{{ Auth::user()->avatar ?? 'https://via.placeholder.com/32' }}" alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                <span class="fw-semibold">{{ Auth::user()->name }}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 animate__animated animate__fadeIn" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user-circle me-2 text-primary"></i> {{ __('Profile') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2 text-danger"></i> {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="p-4 flex-grow-1">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white text-center p-3 mt-auto">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Inventory System') }}. All rights reserved.</p>
            </footer>
        </div>
    </div>

    @stack('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            const sidebar = $('#sidebar');
            const content = $('#content');
            const sidebarCollapse = $('#sidebarCollapse');
            const sidebarCollapseSmall = $('#sidebarCollapseSmall');

            function toggleSidebar() {
                sidebar.toggleClass('active');
                content.toggleClass('active');
            }

            sidebarCollapse.on('click', toggleSidebar);
            sidebarCollapseSmall.on('click', toggleSidebar);

            // Close sidebar on small screens when clicking outside
            $(document).on('click', function(event) {
                const isSmallScreen = window.innerWidth < 768;
                const clickedOutsideSidebar = !sidebar.is(event.target) && sidebar.has(event.target).length === 0 && !sidebarCollapseSmall.is(event.target);

                if (isSmallScreen && clickedOutsideSidebar && !sidebar.hasClass('active')) {
                    sidebar.addClass('active');
                    content.addClass('active');
                }
            });

            // Add smooth scrolling to sidebar links
            $('.sidebar .nav-link').on('click', function(event) {
                if (this.hash !== "") {
                    event.preventDefault();
                    const hash = this.hash;
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top
                    }, 800, function(){
                        window.location.hash = hash;
                    });
                }
            });

            // Add active class to current menu item
            const currentUrl = window.location.href;
            $('.nav-link').each(function() {
                if (this.href === currentUrl) {
                    $(this).closest('li').addClass('active');
                    $(this).closest('.collapse').addClass('show');
                }
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Initialize popovers
            $('[data-bs-toggle="popover"]').popover();
        });
    </script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --text-color: #333;
            --bg-color: #ecf0f1;
            --sidebar-width: 250px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            background-color: var(--bg-color);
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            transition: all var(--transition-speed);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(44, 62, 80, 0.15);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .sidebar.active {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .content {
            width: 100%;
            min-height: 100vh;
            transition: all var(--transition-speed);
        }

        .content.active {
            margin-left: 0;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .nav-item.active > .nav-link {
            background-color: var(--accent-color);
            border-left: 4px solid #fff;
        }

        .nav-link {
            padding: 0.75rem 1.5rem;
            transition: all var(--transition-speed);
        }

        .nav-link:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }

        .dropdown-menu {
            border-radius: 0.35rem;
        }

        .dropdown-item {
            padding: 0.5rem 1.5rem;
        }

        .dropdown-item:hover {
            background-color: var(--accent-color);
            color: #fff;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .navbar {
            background-color: #fff;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
                position: fixed;
                z-index: 1000;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .content {
                margin-left: 0;
            }

            .content.active {
                margin-left: var(--sidebar-width);
            }
        }

        /* Custom scrollbar for WebKit browsers */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--secondary-color);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
        }

        /* Smooth transition for dropdown menu */
        .dropdown-menu {
            display: block;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
        }
    </style>
</body>
</html>
