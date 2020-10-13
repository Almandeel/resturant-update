@extends('layouts.dashboard.app')

@section('title')
    التحويلات | اضافة
@endsection

@section('content')
    @component('partials._breadcrumb')
        @slot('title', ['التحويلات', 'اضافة'])
        @slot('url', [route('transferstores.index'), '#'])
        @slot('icon', ['list', 'reply'])
    @endcomponent
    <div class="box box-primary">
        <div class="box-header">
            <h3>تحويل</h3>
        </div>
        <div class="box-body">
            <form action="{{ route('transferstores.store') }}" method="post">
                @csrf 

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>من مخزن</label>
                            <select name="from_store" class="form-control">
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>الى مخزن</label>
                            <select name="to_store" class="form-control">
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
