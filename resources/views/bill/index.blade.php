@extends('layouts.parent')

@push('css')
@vite('resources/css/bill/index.css')
@endpush

@section('content')
{{ Breadcrumbs::render('bill.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('bill.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col-4">
                <label for="shopName" class="form-label">店舗</label>
                <select name="bill[shop_id]" id="shopName" class="form-select">
                    <option value=""></option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->shop_id }}" {{ request('bill.shop_id') == $shop->shop_id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-4">
                <div class="d-flex align-items-start gap-4">
                    <label for="startDate" class="form-label">会計日時</label>
                    <div>
                        {{ Form::checkbox(
                            'bill[is_period]',
                            1,
                            request('bill.is_period'),
                            [
                                'class' => 'form-check-input form-checkbox',
                                'id' => 'isPeriod'
                            ]
                        ) }}
                        <label for="isPeriod" class="form-check-label">期間指定</label>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    {{ Form::date(
                        'bill[start_date]',
                        request('bill.start_date'),
                        [
                            'class' => 'form-control'
                        ]
                    ) }}
                    <span>～</span>
                    {{ Form::date(
                        'bill[end_date]',
                        request('bill.end_date'),
                        [
                            'id' => 'endDate',
                            'class' => 'form-control',
                            'disabled'
                        ]
                    ) }}
                </div>
            </div>
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('bill.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('bill.create') }}" class="col-12 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col" scope="col">ID</th>
                    <th class="bg-light col-2" scope="col">店舗</th>
                    <th class="bg-light col-1" scope="col">メイカー</th>
                    <th class="bg-light col-1" scope="col">会計金額</th>
                    <th class="bg-light col" scope="col">会計日時</th>
                    <th class="bg-light col" scope="col">登録日</th>
                    <th class="bg-light col-1" scope="col">ステータス</th>
                    <th class="bg-light col-1" scope="col">削除</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $bill)
                <tr>
                    <td>
                        <a href="{{ route('bill.show', $bill->bill_id) }}" class="d-block w-100">
                            {{ $bill->bill_id }}
                        </a>
                    </td>
                    <td>{{ $bill->shop->name }}</td>
                    <td>{{ $bill->member?->name }}</td>
                    <td>¥{{ $bill->amount }}</td>
                    <td>{{ $bill->bill_datetime }}</td>
                    <td>{{ $bill->created_datetime }}</td>
                    <td>
                        @if ($bill->is_draft)
                        <div class="col-12">
                            <a href="{{ route('bill.edit', $bill->bill_id) }}" class="btn btn-sm btn-info rounded-pill w-100">
                                下書き
                            </a>
                        </div>
                        @else
                        <div class="col-12">
                            <a class="btn btn-sm btn-success rounded-pill w-100" style="pointer-events: none">
                                登録済み
                            </a>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="col-12">
                            <button type="button"
                                class="btn btn-sm btn-danger w-100 btn-delete"
                                data-route="{{ route('bill.destroy', $bill->bill_id) }}"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal">
                                削除
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>
<div class="mt-3" id="footer">
    {{ $bills->links() }}
</div>

@push('jquery')
@vite('resources/js/bill.js')
@endpush

@endsection
