@extends('layouts.dashboard.app', ['datatable' => true])

@section('title')
    التحويلات
@endsection

@section('content')
    @component('partials._breadcrumb')
        @slot('title', ['التحويلات'])
        @slot('url', ['#'])
        @slot('icon', ['list'])
    @endcomponent
    <div class="box box-primary">
        <div class="box-header">
            قائمة التحويلات
            <a class="btn btn-primary btn-sm pull-right" href="{{ route('transferstores.create') }}"><i class="fa fa-reply"></i> تحويل</a>
        </div>
        <div class="box-body">
            <table class="table text-center datatable table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>من مخزن</th>
                        <th>الى مخزن</th>
                        <th>عدد الاصناف</th>
                        <th>التاريخ</th>
                        <th>خيارات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfers as $index=>$transfer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transfer->fromStore->name }}</td>
                            <td>{{ $transfer->toStore->name }}</td>
                            <td>{{ $transfer->items }}</td>
                            <td>{{ $transfer->created_at }}</td>
                            <td>
                                
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
