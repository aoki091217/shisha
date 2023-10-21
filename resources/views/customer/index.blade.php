@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('customer.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('customer.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    @method('DELETE')
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col-2">
                <label for="customerName" class="form-label">顧客名</label>
                {{ Form::text(
                    'customer[name]',
                    request('customer.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'customerName'
                    ]
                ) }}
            </div>
            <div class="col-2">
                <label for="shopName" class="form-label">店舗</label>
                <select name="customer[shop_id]" id="shopName" class="form-select">
                    <option value=""></option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->shop_id }}" {{ request('customer.shop_id') == $shop->shop_id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-2">
                <label for="visitedDate" class="form-label">チェックイン：日付</label>
                {{ Form::date(
                    'customer[visited_date]',
                    request('customer.visited_date'),
                    [
                        'class' => 'form-control',
                        'id' => 'visitedDate'
                    ]
                ) }}
            </div>
            <div class="col-2">
                <label for="visitedHour" class="form-label">チェックイン：時間</label>
                <div class="d-flex align-items-center gap-2">
                    <select name="customer[visited_hour]" id="visitedHour" class="form-select">
                        <option value=""></option>
                        @foreach (range(0, 23) as $hour)
                            <option
                                value="{{ $hour }}"
                                {{ !is_null(request('customer.visited_hour')) && request('customer.visited_hour') == $hour ? 'selected' : '' }}>
                                {{ $hour }}
                            </option>
                        @endforeach
                    </select>
                    <span>時</span>
                    <select name="customer[visited_minute]" class="form-select">
                        <option value=""></option>
                        @foreach (range(0, 59) as $minute)
                            <option
                                value="{{ $minute }}"
                                {{ !is_null(request('customer.visited_minute')) && request('customer.visited_minute') == $minute ? 'selected' : '' }}>
                                {{ $minute }}
                            </option>
                        @endforeach
                    </select>
                    <span>分</span>
                </div>
            </div>
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('customer.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col-3" scope="col">顧客名</th>
                    <th class="bg-light col-3" scope="col">チェックイン店舗</th>
                    <th class="bg-light col-2" scope="col">最終チェックイン日時</th>
                    <th class="bg-light col-2" scope="col">登録日</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $customersForView = $customers->groupBy('id')->map(function ($items) {
                        return $items->first();
                    });
                @endphp

                @foreach ($customersForView as $customer)
                <tr>
                    <td>
                        <a href="{{ route('customer.show', $customer->id) }}">
                            {{ $customer->name }}
                        </a>
                    </td>
                    <td>{{ $customer->shop_name }}</td>
                    <td>{{ $customer->checkin_datetime }}</td>
                    <td>{{ $customer->format_created_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>
<div class="mt-3" id="footer">
    {{ $customers->links() }}
</div>

@endsection
