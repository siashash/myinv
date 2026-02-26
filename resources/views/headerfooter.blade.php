<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="JCMarts Admin Panel">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>My Inventory</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    <!-- Master Stylesheet CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('styles')
</head>

<body>

<!-- Preloader -->
<div id="preloader-area">
    <div class="lds-ripple">
        <div></div>
        <div></div>
    </div>
</div>
<!-- Preloader -->

<!-- ======================================
********* Main Page Wrapper ***********
====================================== -->
<div class="main-container-wrapper">

    <!-- Top Navbar (Mobile) -->
    <div class="horizontal-menu sticky sticky-top">
        <nav class="navbar top-navbar col-lg-12 col-12 d-block d-sm-none p-0">
            <div class="container">
                <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                    <a class="navbar-brand brand-logo-mini">
                        <img src="{{ asset('images/jcmarts-logo.png') }}" alt="logo">
                    </a>
                </div>
                <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button">
                        <span class="ti-menu"></span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Bottom Navbar (Desktop) -->
        <nav class="bottom-navbar">
            <div class="container">
<ul class="nav page-navigation">

    <!-- Logo -->
    <li class="nav-item d-none d-sm-block">
        <span class="nav-link text-primary font-weight-bold">My Inventory</span>
    </li>

    <!-- Master -->
    <li class="nav-item">
        <a href="javascript:void(0)" class="nav-link">
            <i class="ti-layers menu-icon"></i>
            <span class="menu-title">Master</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="submenu">
            <ul>
                <li><a href="{{ route('categories.index') }}">Category</a></li>
                <li><a href="{{ route('subcategories.index') }}">Sub-category</a></li>
                <li><a href="{{ route('units.index') }}">Units</a></li>
                <li><a href="{{ route('products.index') }}">Product</a></li>
                <li><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                <li><a href="{{ route('customers.index') }}">Customer</a></li>
                <li><a href="{{ route('ac_heads.index') }}">Accounts head</a></li>
            </ul>
        </div>
    </li>

    <!-- Transaction -->
    <li class="nav-item">
        <a href="javascript:void(0)" class="nav-link">
            <i class="ti-receipt menu-icon"></i>
            <span class="menu-title">Transaction</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="submenu">
            <ul>
                <li><a href="{{ route('purchases.index') }}">Purchase</a></li>
                <li><a href="javascript:void(0)">Product Inward</a></li>
                <li><a href="javascript:void(0)">Sales</a></li>
            </ul>
        </div>
    </li>

    <!-- Return -->
    <li class="nav-item">
        <a href="javascript:void(0)" class="nav-link">
            <i class="ti-back-left menu-icon"></i>
            <span class="menu-title">Return</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="submenu">
            <ul>
                <li><a href="{{ route('purchase-returns.index') }}">Purchase Return</a></li>
                <li><a href="javascript:void(0)">Sales Return</a></li>
            </ul>
        </div>
    </li>

    <!-- Payments -->
    <li class="nav-item">
        <a href="javascript:void(0)" class="nav-link">
            <i class="ti-credit-card menu-icon"></i>
            <span class="menu-title">Payments</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="submenu">
            <ul>
                <li><a href="{{ route('purchase-payments.index') }}">Purchase</a></li>
                <li><a href="javascript:void(0)">Expenses</a></li>
            </ul>
        </div>
    </li>

    <!-- Receipts -->
    <li class="nav-item">
        <a href="javascript:void(0)" class="nav-link">
            <i class="ti-wallet menu-icon"></i>
            <span class="menu-title">Receipts</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="submenu">
            <ul>
                <li><a href="javascript:void(0)">Sales</a></li>
                <li><a href="javascript:void(0)">Cash</a></li>
            </ul>
        </div>
    </li>

    <!-- Reports -->
    <li class="nav-item">
        <a href="javascript:void(0)" class="nav-link">
            <i class="ti-bar-chart menu-icon"></i>
            <span class="menu-title">Reports</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="submenu">
            <ul>
                <li><a href="{{ route('reports.stock') }}">Stock</a></li>
                <li><a href="{{ route('purchases.details') }}">Purchase</a></li>
                <li><a href="javascript:void(0)">Sales</a></li>
                <li><a href="javascript:void(0)">Debtors</a></li>
                <li><a href="{{ route('reports.sundry-creditors') }}">Creditors</a></li>
            </ul>
        </div>
    </li>

    <!-- User Management -->
    <li class="nav-item">
        <a href="javascript:void(0)" class="nav-link">
            <i class="ti-id-badge menu-icon"></i>
            <span class="menu-title">User Management</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="submenu">
            <ul>
                <li><a href="{{ route('um.users.index') }}">User</a></li>
                <li><a href="{{ route('um.roles.index') }}">Roles</a></li>
                <li><a href="{{ route('um.permissions.index') }}">Permission</a></li>
                <li><a href="{{ route('um.role_permissions.index') }}">Role_Permission</a></li>
            </ul>
        </div>
    </li>

    <!-- Logout -->
    <li class="nav-item">
        <form action="" method="POST" class="nav-link p-0">
            @csrf
            <button type="submit" class="btn btn-link nav-link p-0">
                <i class="ti-power-off menu-icon"></i>
                <span class="menu-title">Logout</span>
            </button>
        </form>
    </li>

</ul>

            </div>
        </nav>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content">
        @yield('content')
    </div>

</div>
<!-- ======================================
********* Page Wrapper End ***********
====================================== -->

<!-- Scripts -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/bundle.js') }}"></script>

<script src="{{ asset('js/canvas.js') }}"></script>
<script src="{{ asset('js/collapse.js') }}"></script>
<script src="{{ asset('js/settings.js') }}"></script>
<script src="{{ asset('js/template.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

@stack('scripts')

</body>
</html>
