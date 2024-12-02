<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Inventory System'))</title>

    <!-- Add the favicon link here -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📦</text></svg>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <!-- Add your custom CSS file -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <!-- Add the preloader HTML structure -->
    <div class="preloader">
        <div class="preloader-content">
            <div class="inventory-icon">
                <div class="inventory-box"></div>
                <div class="inventory-box"></div>
                <div class="inventory-box"></div>
                <div class="inventory-box"></div>
            </div>
            <div class="loading-text">Loading Inventory System</div>
        </div>
    </div>

    <div class="loading-bar" id="loadingBar"></div>
    <div class="fade-overlay" id="overlay"></div>

    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="position-sticky pt-3">
            <div class="d-flex align-items-center mb-4 ps-3">
                <i class="bi bi-box-seam fs-4 text-light me-2"></i>
                <h3 class="mb-0 d-none d-lg-block">{{ __('IMS') }}</h3>
                <h3 class="mb-0 d-lg-none">{{ __('IMS') }}</h3>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('inventories*') ? 'active' : '' }}" href="{{ route('inventories.index') }}">
                        <i class="bi bi-box-seam me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Inventory') }}</span>
                    </a>
                </li>
                @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('categories*') ? 'active' : '' }}" href="{{ route('categories.index')}}">
                        <i class="bi bi-tags me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Categories') }}</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="bi bi-cart me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Products') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('stock-ins*') ? 'active' : '' }}" href="{{ route('stock_ins.index')}}">
                        <i class="bi bi-box-arrow-in-down me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Stock In') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('stock-outs*') ? 'active' : '' }}" href="{{ route('stock_outs.index')}}">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Stock Out') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('stock_transfers*') ? 'active' : '' }}" href="{{ route('stock_transfers.index')}}">
                        <i class="bi bi-arrow-left-right me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Stock Transfer') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('receiving-reports*') ? 'active' : '' }}" href="{{ route('receiving-reports.index')}}">
                        <i class="bi bi-file-earmark-check me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Receiving Reports') }}</span>
                    </a>
                </li>
                @if(Auth::user()->canAccess())
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                        <i class="bi bi-people me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Customers') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('vendors*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">
                        <i class="bi bi-people me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Vendors') }}</span>
                    </a>
                </li>
                @endif
                @if(Auth::user()->canAccessBranches())
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('branches*') ? 'active' : '' }}" href="{{ route('branches.index')}}">
                        <i class="bi bi-building me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Branches') }}</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('barcodes*') ? 'active' : '' }}" href="{{ route('barcodes.index')}}">
                        <i class="bi bi-upc-scan me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Barcodes') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('tools*') ? 'active' : '' }}" href="{{ route('tools.index')}}">
                        <i class="bi bi-tools me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Tools') }}</span>
                    </a>
                </li>
                @if(Auth::user()->canManageReports())
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('reports*') ? 'active' : '' }}" href="{{ route('reports.index')}}">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Reports') }}</span>
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="{{ route('users.index')}}">
                        <i class="bi bi-people me-2"></i>
                        <span class="d-none d-lg-inline">{{ __('Users') }}</span>
                    </a>
                </li>
                @endif
            </ul>
            <hr class="my-3">
        </div>
    </nav>

    <!-- Main content -->
    <div class="main-content">
        <!-- Top bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white top-bar mb-4">
            <div class="container-fluid">
                <button id="sidebarToggle" class="btn btn-link d-lg-none">
                    <i class="bi bi-list"></i>
                </button>
                @if(Auth::user()->branch)
                    <div class="ms-3 text-muted d-none d-lg-block">
                        <i class="bi bi-building me-1"></i>
                        {{ Auth::user()->branch->name }}
                    </div>
                @endif
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarContent">
                    <!-- Replace the existing search form with this -->
                    <div class="d-flex ms-auto me-3 my-2 my-lg-0 position-relative">
                        <div class="input-group">
                            <input class="form-control" type="search" placeholder="Search" aria-label="Search" id="globalSearch" autocomplete="off">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                        </div>
                        <div id="searchResults" class="position-absolute bg-white shadow-sm rounded-bottom" style="display: none; z-index: 1000; width: 100%; max-height: 400px; overflow-y: auto; top: 100%; left: 0;"></div>
                    </div>
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </a>

                            <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationsDropdown" style="width: 320px; max-height: 500px; overflow-y: auto;">
                                <div class="border-bottom p-3 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Notifications</h6>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">
                                                Mark all as read
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="notifications-list">
                                    @forelse(auth()->user()->notifications()->latest()->limit(10)->get() as $notification)
                                        <div class="notification-item border-bottom p-3 {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">
                                            @if($notification->type === 'App\Notifications\StockTransferApprovedNotification')
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="mb-1">
                                                            <strong>Stock Transfer Approved #{{ $notification->data['stock_transfer_id'] }}</strong>
                                                        </div>
                                                        <p class="mb-1 small text-muted">
                                                            {{ $notification->data['message'] }}
                                                        </p>
                                                        <div class="small mb-2">
                                                            <div>Product: {{ $notification->data['product_name'] }}</div>
                                                            <div>Quantity: {{ $notification->data['quantity'] }}</div>
                                                            <div>From: {{ $notification->data['from_branch'] }}</div>
                                                            <div>To: {{ $notification->data['to_branch'] }}</div>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ route('stock_transfers.show', $notification->data['stock_transfer_id']) }}"
                                                               class="btn btn-sm btn-primary">
                                                                View Details
                                                            </a>
                                                            @unless($notification->read_at)
                                                                <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                        Mark as Read
                                                                    </button>
                                                                </form>
                                                            @endunless
                                                        </div>
                                                    </div>
                                                    <small class="text-muted ms-2">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            @elseif($notification->type === 'App\Notifications\StockTransferRejectedNotification')
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="mb-1">
                                                            <strong>Stock Transfer Rejected #{{ $notification->data['stock_transfer_id'] }}</strong>
                                                        </div>
                                                        <p class="mb-1 small text-muted">
                                                            {{ $notification->data['message'] }}
                                                        </p>
                                                        <div class="small mb-2">
                                                            <div>Product: {{ $notification->data['product_name'] }}</div>
                                                            <div>Quantity: {{ $notification->data['quantity'] }}</div>
                                                            <div>From: {{ $notification->data['from_branch'] }}</div>
                                                            <div>To: {{ $notification->data['to_branch'] }}</div>
                                                            <div class="text-danger">Reason: {{ $notification->data['rejection_reason'] }}</div>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ route('stock_transfers.show', $notification->data['stock_transfer_id']) }}"
                                                               class="btn btn-sm btn-primary">
                                                                View Details
                                                            </a>
                                                            @unless($notification->read_at)
                                                                <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                        Mark as Read
                                                                    </button>
                                                                </form>
                                                            @endunless
                                                        </div>
                                                    </div>
                                                    <small class="text-muted ms-2">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            @elseif($notification->type === 'App\Notifications\StockTransferRequestNotification')
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="mb-1">
                                                            <strong>New Stock Transfer Request</strong>
                                                        </div>
                                                        <p class="mb-1 small text-muted">
                                                            {{ $notification->data['message'] }}
                                                        </p>
                                                        <div class="small mb-2">
                                                            <div>Product: {{ $notification->data['product'] }}</div>
                                                            <div>Quantity: {{ $notification->data['quantity'] }}</div>
                                                            <div>Requested by: {{ $notification->data['created_by'] }}</div>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ route('stock_transfers.show', $notification->data['stock_transfer_id']) }}"
                                                               class="btn btn-sm btn-primary">
                                                                Review Request
                                                            </a>
                                                            @unless($notification->read_at)
                                                                <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                        Mark as Read
                                                                    </button>
                                                                </form>
                                                            @endunless
                                                        </div>
                                                    </div>
                                                    <small class="text-muted ms-2">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="p-3 text-center text-muted">
                                            No notifications
                                        </div>
                                    @endforelse
                                </div>

                                @if(auth()->user()->notifications->count() > 10)
                                    <div class="p-2 text-center border-top">
                                        <a href="#" class="text-decoration-none small">View All Notifications</a>
                                    </div>
                                @endif
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle me-2" style="width: 32px; height: 32px;">
                                @else
                                    <div class="rounded-circle me-2 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="d-none d-lg-inline">{{ Auth::user()->username }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item py-2" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>{{ __('My Profile') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                        @csrf
                                        <a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Mobile search form -->
        <div class="container-fluid d-sm-none mb-3" id="mobileSearch" style="display: none;">
            <form action="#" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="query" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Page content -->
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/datatables-init.js') }}"></script>
    @stack('scripts')
    <!-- Add Select2 JS (make sure it's after jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mainContent = document.querySelector('.main-content');
            const overlay = document.getElementById('overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('sidebar-active');
                if (window.innerWidth <= 992) {
                    overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
                }
            }

            sidebarToggle.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);

            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('sidebar-active');
                    overlay.style.display = 'none';
                }
            });

            // Loading bar
            const loadingBar = document.getElementById('loadingBar');

            function showLoadingBar() {
                loadingBar.style.width = '70%';
            }

            function hideLoadingBar() {
                loadingBar.style.width = '100%';
                setTimeout(() => {
                    loadingBar.style.width = '0';
                }, 300);
            }

            // Intercept all link clicks and form submissions
            document.addEventListener('click', (e) => {
                if (e.target.tagName === 'A' && !e.target.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    showLoadingBar();
                    window.location = e.target.href;
                }
            });

            document.addEventListener('submit', (e) => {
                showLoadingBar();
            });

            window.addEventListener('load', hideLoadingBar);

            // Smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Add animation to cards
            const cards = document.querySelectorAll('.card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
        entry.target.style.transform = 'translateY(0)';
    }
        });
        }, { threshold: 0.1 });

        cards.forEach(card => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s, transform 0.5s';
        observer.observe(card);
        });

        // Responsive sidebar collapse
        const sidebarCollapseBreakpoint = 992; // Breakpoint for collapsing sidebar
        let sidebarCollapsed = false;

        function checkSidebarCollapse() {
        if (window.innerWidth <= sidebarCollapseBreakpoint && !sidebarCollapsed) {
            collapseSidebar();
        } else if (window.innerWidth > sidebarCollapseBreakpoint && sidebarCollapsed) {
            expandSidebar();
        }
        }

        function collapseSidebar() {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        sidebarCollapsed = true;
        }

        function expandSidebar() {
        sidebar.classList.remove('collapsed');
        mainContent.classList.remove('expanded');
        sidebarCollapsed = false;
        }

        window.addEventListener('resize', checkSidebarCollapse);
        checkSidebarCollapse();

        // Add hover effect for collapsed sidebar
        sidebar.addEventListener('mouseenter', () => {
        if (sidebarCollapsed) {
            expandSidebar();
        }
        });

        sidebar.addEventListener('mouseleave', () => {
        if (window.innerWidth <= sidebarCollapseBreakpoint) {
            collapseSidebar();
        }
        });

        // Dropdown menu positioning
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        const toggle = dropdown.querySelector('.dropdown-toggle');

        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            menu.classList.toggle('show');
            positionDropdown(menu);
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
        });

        function positionDropdown(menu) {
        const rect = menu.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;
        if (spaceBelow < 0) {
            menu.style.top = 'auto';
            menu.style.bottom = '100%';
        } else {
            menu.style.top = '100%';
            menu.style.bottom = 'auto';
        }
        }

        // Add custom scrollbar to sidebar
        const sidebarContent = sidebar.querySelector('.position-sticky');
        if (typeof SimpleBar === 'function') {
        new SimpleBar(sidebarContent, { autoHide: true });
        }

        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
        });

        // Add fade-in animation to alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
        alert.style.opacity = 0;
        alert.style.transform = 'translateY(-20px)';
        alert.style.transition = 'opacity 0.5s, transform 0.5s';
        setTimeout(() => {
            alert.style.opacity = 1;
            alert.style.transform = 'translateY(0)';
        }, 100);
        });

        // Add scroll to top button
        const scrollTopButton = document.createElement('button');
        scrollTopButton.innerHTML = '<i class="bi bi-arrow-up"></i>';
        scrollTopButton.classList.add('btn', 'btn-primary', 'rounded-circle', 'position-fixed');
        scrollTopButton.style.bottom = '80px';
        scrollTopButton.style.right = '20px';
        scrollTopButton.style.display = 'none';
        scrollTopButton.style.zIndex = '1000';
        document.body.appendChild(scrollTopButton);

        window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollTopButton.style.display = 'block';
        } else {
            scrollTopButton.style.display = 'none';
        }
        });

        scrollTopButton.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Add responsive font sizes
        function adjustFontSize() {
        const baseFontSize = 16;
        const scaleFactor = Math.min(window.innerWidth / 1200, 1);
        document.documentElement.style.fontSize = `${baseFontSize * scaleFactor}px`;
        }

        window.addEventListener('resize', adjustFontSize);
        adjustFontSize();

        // Lazy loading for images
        if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.src = img.dataset.src;
        });
        } else {
        // Fallback to a library like lozad.js for browsers that don't support lazy loading
        }

        function updateSidebarContent() {
            const isNarrow = window.innerWidth <= sidebarCollapseBreakpoint;
            document.querySelector('.sidebar h3.d-none').classList.toggle('d-lg-block', !isNarrow);
            document.querySelector('.sidebar h3.d-lg-none').classList.toggle('d-block', isNarrow);
        }

        // Call this function in your existing code
        window.addEventListener('resize', updateSidebarContent);
        updateSidebarContent();

            // Add this new code for the preloader
            const preloader = document.querySelector('.preloader');
            const loadingText = document.querySelector('.loading-text');

            let dots = '';
            let dotCount = 0;
            // Animate loading text
            const textInterval = setInterval(() => {
                dotCount = (dotCount + 1) % 4;
                dots = '.'.repeat(dotCount);
                loadingText.textContent = `Loading Inventory System${dots}`;
            }, 300);

            // Hide preloader after animation completes
            setTimeout(() => {
                clearInterval(textInterval);
                preloader.style.opacity = '0';
                preloader.style.visibility = 'hidden';
            }, 1000); // Changed from 2000 to 1000 milliseconds (1 second)
            // Check if notifications are enabled
            const notificationsEnabled = document.querySelector('input[name="notificationsEnabled"]:checked') ? 'true' : 'false';
            const emailNotificationsEnabled = document.querySelector('input[name="emailNotificationsEnabled"]:checked') ? 'true' : 'false';
            const pushNotificationsEnabled = document.querySelector('input[name="pushNotificationsEnabled"]:checked') ? 'true' : 'false';

            if (notificationsEnabled === 'true') {
                if (emailNotificationsEnabled) {
                    // Enable email notifications functionality
                    console.log('Email notifications are enabled');
                }

                if (pushNotificationsEnabled) {
                    // Enable push notifications functionality
                    console.log('Push notifications are enabled');
                }
            }

            // Initialize Select2
            function initializeSelect2() {
                // Target all select elements with class 'select2' or just 'select' elements
                $('select, .select2').each(function() {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: $(this).data('placeholder') || 'Select an option',
                        allowClear: true,
                        minimumInputLength: 0,
                        dropdownParent: $('body'),
                        language: {
                            noResults: function() {
                                return "No results found";
                            },
                            searching: function() {
                                return "Searching...";
                            }
                        },
                        escapeMarkup: function(markup) {
                            return markup;
                        },
                        templateResult: formatOption,
                        templateSelection: formatOption,
                        // Add matcher for better search functionality
                        matcher: function(params, data) {
                            // If there are no search terms, return all of the data
                            if ($.trim(params.term) === '') {
                                return data;
                            }

                            // Do not display the item if there is no 'text' property
                            if (typeof data.text === 'undefined') {
                                return null;
                            }

                            // `params.term` should be the term that is used for searching
                            // `data.text` is the text that is displayed for the data object
                            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                                return data;
                            }

                            // Return `null` if the term should not be displayed
                            return null;
                        }
                    });
                });

                // Custom styling for Select2 dropdown
                $('.select2-container--bootstrap-5 .select2-selection--single').css({
                    'height': 'calc(2.5rem + 2px)',
                    'padding': '0.375rem 0.75rem',
                    'font-size': '1rem',
                    'border-radius': '0.25rem'
                });

                $('.select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow').css({
                    'height': 'calc(2.5rem + 2px)',
                    'top': '0',
                    'right': '0.75rem'
                });
            }

            // Format option to highlight matched text
            function formatOption(option) {
                if (!option.id) {
                    return option.text;
                }
                var $option = $(
                    '<span>' + highlightMatch(option.text, option.element.value) + '</span>'
                );
                return $option;
            }

            // Highlight matched text
            function highlightMatch(text, term) {
                if (!term) {
                    return text;
                }
                return text.replace(new RegExp("(" + escapeRegExp(term) + ")", "gi"), "<strong>$1</strong>");
            }

            // Escape special characters in search term
            function escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
            }

            // Initialize Select2 on page load
            initializeSelect2();

            // Reinitialize Select2 after AJAX content load
            $(document).ajaxComplete(function() {
                initializeSelect2();
            });

            const searchInput = document.getElementById('globalSearch');
            const searchResults = document.getElementById('searchResults');
            let debounceTimer;

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const query = this.value.trim();
                    if (query.length >= 1) { // Changed from 2 to 1 to start searching from the first character
                        fetch(`{{ route('global.search') }}?query=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                displayResults(data);
                            });
                    } else {
                        searchResults.style.display = 'none';
                    }
                }, 200); // Reduced debounce time from 300ms to 200ms for faster response
            });

            function displayResults(results) {
                if (results.length === 0) {
                    searchResults.innerHTML = '<div class="p-3 text-muted">No results found</div>';
                    searchResults.style.display = 'block';
                    return;
                }

                let html = '<ul class="list-group list-group-flush">';
                results.forEach(result => {
                    html += `
                        <li class="list-group-item list-group-item-action">
                            <a href="${result.url}" class="text-decoration-none">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary">${highlightMatch(result.text, searchInput.value)}</span>
                                    <span class="badge bg-secondary">${result.model}</span>
                                </div>
                                <small class="text-muted">${result.subtext}</small>
                            </a>
                        </li>
                    `;
                });
                html += '</ul>';

                searchResults.innerHTML = html;
                searchResults.style.display = 'block';
            }

            function highlightMatch(text, query) {
                if (!query) return text;
                const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
                return text.replace(regex, '<strong class="bg-warning">$1</strong>');
            }

            function escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }

            // Close search results when clicking outside
            document.addEventListener('click', function(event) {
                if (!searchResults.contains(event.target) && event.target !== searchInput) {
                    searchResults.style.display = 'none';
                }
            });
        });
    </script>
    <script>
        @if(session('logout_success'))
            Swal.fire({
                icon: 'success',
                title: 'Goodbye!',
                text: 'You have been successfully logged out.',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        @endif
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize if user is authenticated
        @auth
            // Listen for private notifications channel
            window.Echo.private(`notifications.{{ auth()->user()->branch_id }}`)
                .listen('StockTransferEvent', (e) => {
                    // Create notification element
                    const notification = createNotificationElement(e);

                    // Update notification counter
                    updateNotificationCounter();

                    // Add to notification list
                    const notificationsList = document.querySelector('.notifications-list');
                    if (notificationsList) {
                        notificationsList.insertBefore(notification, notificationsList.firstChild);
                    }

                    // Show push notification
                    showPushNotification(e);
                });

            // Function to create notification element
            function createNotificationElement(data) {
                const div = document.createElement('div');
                div.className = 'notification-item border-bottom p-3 bg-white';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="mb-1">
                                <strong>${data.message}</strong>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="${data.url}" class="btn btn-sm btn-primary">
                                    View Details
                                </a>
                                <form action="/notifications/${data.id}/mark-as-read" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        Mark as Read
                                    </button>
                                </form>
                            </div>
                        </div>
                        <small class="text-muted ms-2">Just now</small>
                    </div>
                `;
                return div;
            }

            // Function to update notification counter
            function updateNotificationCounter() {
                const counter = document.querySelector('#notificationsDropdown .badge');
                if (counter) {
                    const currentCount = parseInt(counter.textContent) || 0;
                    counter.textContent = currentCount + 1;
                } else {
                    const badge = document.createElement('span');
                    badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                    badge.textContent = '1';
                    document.querySelector('#notificationsDropdown').appendChild(badge);
                }
            }

            // Function to show push notification
            function showPushNotification(data) {
                if (!("Notification" in window)) {
                    return;
                }

                if (Notification.permission === "granted") {
                    new Notification("Stock Transfer Notification", {
                        body: data.message,
                        icon: '/path/to/your/icon.png'
                    });
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(function (permission) {
                        if (permission === "granted") {
                            new Notification("Stock Transfer Notification", {
                                body: data.message,
                                icon: '/path/to/your/icon.png'
                            });
                        }
                    });
                }
            }

            // Request notification permission on page load
            if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }
        @endauth
    });
    </script>
</body>
</html>
