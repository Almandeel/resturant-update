@extends('layouts.dashboard.app', ['modals' => ['subscription'],'datatable' => true])

@section('title')
    عرض
@endsection

@push('css')
    <style>
        .barcode {
            position: relative;
            right: 39%;
        }
    </style>

@endpush

@section('content')
    @component('partials._breadcrumb')
        @slot('title', ['الاشتراكات','عرض'])
        @slot('url', [route('subscriptions.index'), '#'])
        @slot('icon', ['users', 'eye'])
    @endcomponent
    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-header">
                    <h3>اشتراك العميل : {{ $subscription->customer->name }}</h3>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="">
                        <div class="box">
                            <div class="box-header">
                                <table class="table table-borderd">
                                    <tr>
                                        <th>اسم العميل</th>
                                        <td>{{ $subscription->customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>الباقة</th>
                                        <td>{{ $subscription->plan->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ الاشتراك</th>
                                        <td>{{ $subscription->start_date }}</td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ الانتهاء</th>
                                        <td>{{ $subscription->end_date }}</td>
                                    </tr>
                                    <tr>
                                        <th>طريقة الدفع</th>
                                        <td>{{ $subscription->payment_type ? 'الكتروني' : 'نقدا' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="box-footer">
                                @permission('subscriptions-update')
                                    <button class="btn btn-warning btn-xs subscription update" data-action="{{ route('subscriptions.update', $subscription->id) }}" data-customer="{{ $subscription->customer_id }}" data-plan="{{ $subscription->plan_id }}" data-payment="{{ $subscription->payment_type }}" data-toggle="modal" data-target="#subscription"> <i class="fa fa-eye">تعديل</i></button>
                                @endpermission
        
                                @if(!Carbon\Carbon::parse(date('Y-m-d'))->lte($subscription->end_date))
                                    @permission('subscriptions-update')
                                        <button class="btn btn-success btn-xs subscription refresh" data-id="{{ $subscription->id }}" data-action="{{ route('subscriptions.update', $subscription->id) }}?type=resubscribe" data-customer="{{ $subscription->customer_id }}" data-plan="{{ $subscription->plan_id }}"  data-toggle="modal" data-target="#subscription"> <i class="fa fa-refresh"> اعادة الاشتراك</i></button>
                                    @endpermission
                                @endif
        
                                @if(Carbon\Carbon::parse(date('Y-m-d'))->lte($subscription->end_date))
                                    @permission('subscriptions-delete')
                                    <form style="display:inline-block" action="{{ route('subscriptions.destroy', $subscription->id) }}" method="post">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> الغاء الاشتراك</button>
                                    </form>
                                    @endpermission
                                @endif

                                @permission('subscriptions-print')
                                    <button onclick="window.print()" class="btn btn-default btn-xs print" > <i class="fa fa-print"> طباعة </i></button>
                                @endpermission
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="box-header">
                    {!! DNS1D::getBarcodeHTML($subscription->id, "C39",1.4,44) !!}
                </div>
            </div>
        </div>
    </div>

@endsection