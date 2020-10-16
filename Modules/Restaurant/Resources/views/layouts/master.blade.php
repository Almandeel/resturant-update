@extends('layouts.dashboard.dashboard', [
    'skin' => 'yellow-light'
])
@push('head')
    <style>
        .box.box-primary {
            border-top-color: #f39c12;
        }

        .bg-primary {
            background-color: #f39c12;
        }

        .text-primary,
        .box-title {
            color: #f39c12;
        }
    </style>
@endpush
@push('dashboard_navbar_items')
    @stack('navbar_items')
@endpush
@push('dashboard_navbar_left_items')
    @stack('navbar_left_items')
@endpush
@push('dashboard_sidebar_items')
    @permission('menus-read')
        <li class="{{ (request()->segment(2) == 'menus') ? 'active' : '' }}"><a href="{{ route('menus.index') }}"><i class="icon-menu"></i><span>قوائم الطعام</span></a></li>
    @endpermission
    @permission('tables-read')
        <li class="{{ (request()->segment(2) == 'tables') ? 'active' : '' }}"><a href="{{ route('tables.index') }}"><i class="icon-table"></i><span>الطاولات</span></a></li>
    @endpermission
    @permission('waiters-read')
        <li class="{{ (request()->segment(2) == 'waiters') ? 'active' : '' }}"><a href="{{ route('waiters.index') }}"><i class="icon-waiter"></i><span>الكباتن</span></a></li>
    @endpermission
    @permission('drivers-read')
        <li class="{{ (request()->segment(2) == 'drivers') ? 'active' : '' }}"><a href="{{ route('drivers.index') }}"><i class="icon-driver"></i><span>السائقين</span></a></li>
    @endpermission
    @permission('employees-read')
        <li class="{{ (request()->segment(2) == 'cashiers') ? 'active' : '' }}"><a href="{{ route('restaurant.cashiers.index') }}"><i class="fa fa-users"></i><span>موظفو الكاشير</span></a></li>
    @endpermission
    @permission('halls-read')
        <li class="{{ (request()->segment(2) == 'halls') ? 'active' : '' }}"><a href="{{ route('halls.index') }}"><i class="fa fa-users"></i><span>الصالات</span></a></li>
    @endpermission
    @stack('sidebar_items')
@endpush
@push('dashboard_content')
    @stack('content')
@endpush