@extends('layouts.parent')

@push('css')
@vite('resources/css/bill/create.css')
@endpush

@section('content')
{{ Breadcrumbs::render('bill.show', $bill->bill_id) }}
<div class="d-flex flex-wrap">
    @if ($bill->is_draft)
        <div class="col-12 text-center text-danger fw-bold mb-3">この会計情報は下書きです。</div>
    @endif
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
    <div class="col-8 mb-3">
        <label class="form-label">オーダー</label>
        @foreach ($bill->billOrders as $billOrder)
        <div>ミックス{{ $loop->iteration }}：{{ $billOrder->mix->name }}</div>
        @endforeach
    </div>
</div>
<div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
    <a href="{{ route('bill.index') }}" class="btn btn-secondary">戻る</a>
    <a href="{{ route('bill.edit', $bill->bill_id) }}" class="btn btn-success">編集</a>
    <button type="button"
            class="btn btn-danger"
            data-route="{{ route('bill.destroy', $bill->bill_id) }}"
            data-bs-toggle="modal"
            data-bs-target="#deleteModal">
            削除
    </button>
</div>

@endsection
