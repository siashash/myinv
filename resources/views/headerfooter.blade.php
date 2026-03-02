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
@php
    $loggedInUserName = session('managed_user_name', 'User');
    $loggedInRoleName = strtolower((string) session('managed_user_role_name', ''));
    $isAdminUser = $loggedInRoleName === 'admin';
    $access = app(\App\Support\RolePermissionAccess::class);
    $hasAccess = function (string $module) use ($access): bool {
        return $access->allows($module, 'view')
            || $access->allows($module, 'add')
            || $access->allows($module, 'edit')
            || $access->allows($module, 'delete');
    };
    $canCategoryMenu = $hasAccess('category') || $hasAccess('masters');
    $canSubCategoryMenu = $hasAccess('sub-category') || $hasAccess('masters');
    $canUnitsMenu = $hasAccess('units') || $hasAccess('masters');
    $canProductMenu = $hasAccess('product') || $hasAccess('masters');
    $canSupplierMenu = $hasAccess('supplier') || $hasAccess('masters');
    $canCustomerMenu = $hasAccess('customer') || $hasAccess('masters');
    $canAcHeadMenu = $hasAccess('ac-head') || $hasAccess('accounts head') || $hasAccess('masters');
    $canShowMasterMenu = $canCategoryMenu || $canSubCategoryMenu || $canUnitsMenu || $canProductMenu || $canSupplierMenu || $canCustomerMenu || $canAcHeadMenu;
    $canPurchaseMenu = $hasAccess('purchase') || $hasAccess('transaction-purchase');
    $canSalesMenu = $hasAccess('sales') || $hasAccess('transaction-sales');
    $canPurchaseReturnMenu = $hasAccess('purchase-return') || $hasAccess('return-purchase');
    $canSalesReturnMenu = $hasAccess('sales-return') || $hasAccess('return-sales');
    $canPurchasePaymentMenu = $hasAccess('purchase-payment') || $hasAccess('payment-purchase');
    $canSalesReceiptMenu = $hasAccess('sales-receipt') || $hasAccess('receipt-sales') || $hasAccess('sales') || $hasAccess('transaction-sales');
    $canStockReportMenu = $hasAccess('stock-report') || $hasAccess('stock') || $hasAccess('reports');
    $canSalesReportMenu = $hasAccess('sales-report') || $hasAccess('reports') || $hasAccess('sales') || $hasAccess('transaction-sales');
    $canAccountBookMenu = $hasAccess('account-book') || $hasAccess('reports');
    $canCashBookMenu = $hasAccess('cash-book') || $hasAccess('reports');
    $canBankBookMenu = $hasAccess('bank-book') || $hasAccess('reports');
    $canPurchaseReportMenu = $hasAccess('purchase') || $hasAccess('transaction-purchase');
    $canCreditorsReportMenu = $hasAccess('sundry-creditors') || $hasAccess('sundry-debtors') || $hasAccess('reports');
    $canShowTransactionMenu = $canPurchaseMenu || $canSalesMenu;
    $canShowReturnMenu = $canPurchaseReturnMenu || $canSalesReturnMenu;
    $canShowPaymentMenu = $canPurchasePaymentMenu;
    $canShowReportsMenu = $canStockReportMenu || $canSalesReportMenu || $canAccountBookMenu || $canCashBookMenu || $canBankBookMenu || $canPurchaseReportMenu || $canCreditorsReportMenu;
@endphp

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
    @if ($canShowMasterMenu)
        <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link">
                <i class="ti-layers menu-icon"></i>
                <span class="menu-title">Master</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="submenu">
                <ul>
                    @if ($canCategoryMenu)
                        <li><a href="{{ route('categories.index') }}">Category</a></li>
                    @endif
                    @if ($canSubCategoryMenu)
                        <li><a href="{{ route('subcategories.index') }}">Sub-category</a></li>
                    @endif
                    @if ($canUnitsMenu)
                        <li><a href="{{ route('units.index') }}">Units</a></li>
                    @endif
                    @if ($canProductMenu)
                        <li><a href="{{ route('products.index') }}">Product</a></li>
                    @endif
                    @if ($canSupplierMenu)
                        <li><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                    @endif
                    @if ($canCustomerMenu)
                        <li><a href="{{ route('customers.index') }}">Customer</a></li>
                    @endif
                    @if ($canAcHeadMenu)
                        <li><a href="{{ route('ac_heads.index') }}">Accounts head</a></li>
                    @endif
                </ul>
            </div>
        </li>
    @endif

    <!-- Transaction -->
    @if ($canShowTransactionMenu)
        <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link">
                <i class="ti-receipt menu-icon"></i>
                <span class="menu-title">Transaction</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="submenu">
                <ul>
                    @if ($canPurchaseMenu)
                        <li><a href="{{ route('purchases.index') }}">Purchase</a></li>
                    @endif
                    @if ($canSalesMenu)
                        <li><a href="{{ route('sales.index') }}">Sales</a></li>
                    @endif
                </ul>
            </div>
        </li>
    @endif

    <!-- Return -->
    @if ($canShowReturnMenu)
        <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link">
                <i class="ti-back-left menu-icon"></i>
                <span class="menu-title">Return</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="submenu">
                <ul>
                    @if ($canPurchaseReturnMenu)
                        <li><a href="{{ route('purchase-returns.index') }}">Purchase Return</a></li>
                    @endif
                    @if ($canSalesReturnMenu)
                        <li><a href="{{ route('sales-returns.index') }}">Sales Return</a></li>
                    @endif
                </ul>
            </div>
        </li>
    @endif

    <!-- Payments -->
    @if ($canShowPaymentMenu)
        <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link">
                <i class="ti-credit-card menu-icon"></i>
                <span class="menu-title">Payments</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="submenu">
                <ul>
                    @if ($canPurchasePaymentMenu)
                        <li><a href="{{ route('purchase-payments.index') }}">Purchase</a></li>
                    @endif
                </ul>
            </div>
        </li>
    @endif

    <!-- Receipts -->
    @if ($canSalesReceiptMenu)
        <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link">
                <i class="ti-wallet menu-icon"></i>
                <span class="menu-title">Receipts</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="submenu">
                <ul>
                    <li><a href="{{ route('sales-receipts.index') }}">Sales</a></li>
                </ul>
            </div>
        </li>
    @endif

    <!-- Reports -->
    @if ($canShowReportsMenu)
        <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link">
                <i class="ti-bar-chart menu-icon"></i>
                <span class="menu-title">Reports</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="submenu">
                <ul>
                    @if ($canStockReportMenu)
                        <li><a href="{{ route('reports.stock') }}">Stock</a></li>
                    @endif
                    @if ($canSalesReportMenu)
                        <li><a href="{{ route('reports.sales') }}">Sales</a></li>
                    @endif
                    @if ($canPurchaseReportMenu)
                        <li><a href="{{ route('purchases.details') }}">Purchase</a></li>
                    @endif
                    @if ($canAccountBookMenu)
                        <li><a href="{{ route('reports.account-book') }}">Account Book</a></li>
                    @endif
                    @if ($canCashBookMenu)
                        <li><a href="{{ route('reports.cash-book') }}">Cash Book</a></li>
                    @endif
                    @if ($canBankBookMenu)
                        <li><a href="{{ route('reports.bank-book') }}">Bank Book</a></li>
                    @endif
                    @if ($canCreditorsReportMenu)
                        <li><a href="{{ route('reports.sundry-creditors') }}">Creditors</a></li>
                    @endif
                </ul>
            </div>
        </li>
    @endif

    @if ($isAdminUser)
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
    @endif

    <!-- Logged In User -->
    <li class="nav-item">
        <span class="nav-link">
            <i class="ti-user menu-icon"></i>
            <span class="menu-title">{{ $loggedInUserName }}</span>
        </span>
    </li>

    <!-- Logout -->
    <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST" class="nav-link p-0">
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
