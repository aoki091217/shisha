@extends('layouts.parent')

@push('css')
@vite('resources/css/bill/create.css')
@endpush

@section('content')
{{ Breadcrumbs::render('bill.show', $bill->bill_id) }}
<div class="d-flex flex-wrap">
    <div class="col-6 mb-3">
        <label class="form-label">店舗</label>
        <div class="pe-2">
            <span>{{ $bill->shop->name }}</span>
        </div>
    </div>
    <div class="col-6 mb-3">
        <label class="form-label ps-2">メイカー</label>
        <div class="ps-2">
            <span>{{ $bill->member->name }}</span>
        </div>
    </div>
    <div class="col-6 mb-3">
        <label class="form-label">顧客</label>
        <div class="pe-2">
            <ul>
                @foreach ($bill->billCustomers as $billCustomer)
                    <li>{{ $billCustomer->customer->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-6 mb-3 d-flex gap-3">
        <div class="ps-2">
            <label class="form-label">シェア</label>
            <div>
                <span>{{ $bill->share_name }}</span>
            </div>
        </div>
        <div>
            <label for="topChange" class="form-label">トップ替え</label>
            <div class="ps-1">
                <span>{{ $bill->top_change }}回</span>
            </div>
        </div>
    </div>
    <div class="col-6 mb-3">
        <label for="amount" class="form-label">会計金額</label>
        <div class="pe-2">
            <span>¥{{ $bill->amount }}</span>
        </div>
    </div>
    <div class="col-6 mb-3">
        <label class="form-label ps-2">会計日時</label>
        <div class="ps-2">
            <span>{{ $bill->bill_datetime }}</span>
        </div>
    </div>
    <div class="tab-wrapper">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            @foreach ($bill->billOrders->groupBy('order_id') as $i => $order)
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link @if($i == 1) active @endif"
                    id="tab{{ $i }}"
                    data-bs-toggle="tab"
                    data-bs-target="#tabContent{{ $i }}"
                    type="button"
                    role="tab"
                    aria-controls="tabContent{{ $i }}"
                    aria-selected="true">
                オーダー{{ $i }}
                </button>
            </li>
            @endforeach
        </ul>
        <div class="tab-content border border-top-0 rounded-bottom" id="orderTabContents">
            @foreach ($bill->billOrders->groupBy('order_id') as $i => $orders)
            <div
                class="tab-pane fade show @if($i == 1) active @endif"
                id="tabContent{{ $i }}"
                role="tabpanel"
                aria-labelledby="tabtabContent{{ $i }}">
                <ul class="mb-0">
                    @foreach ($orders as $order)
                        <li>{{ $order->mix->name }}</li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
    <a href="{{ route('bill.index') }}" class="btn btn-secondary">戻る</a>
    <button type="button"
            class="btn btn-danger"
            data-route="{{ route('bill.destroy', $bill->bill_id) }}"
            data-bs-toggle="modal"
            data-bs-target="#deleteModal">
            削除
    </button>
</div>

@endsection
