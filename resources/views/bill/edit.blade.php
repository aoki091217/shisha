@extends('layouts.parent')

@inject('billService', 'App\Services\BillService')

@push('css')
@vite('resources/css/bill/create.css')
@endpush

@section('content')
{{ Breadcrumbs::render('bill.edit', $bill->bill_id) }}
<form action="{{ route('bill.update', $bill->bill_id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <input type="hidden" name="bill[bill_order_id]" value="{{ $bill->bill_order_id }}">
    <div class="d-flex flex-wrap">
        <div class="col-6 mb-3">
            <label for="shopId" class="form-label">店舗<span class="text-danger">※</span></label>
            <div class="pe-2">
                <select name="bill[shop_id]" id="shopId" class="form-select">
                    <option value=""></option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->shop_id }}" {{ old('bill.shop_id', $bill->shop_id) == $shop->shop_id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
                <span class="text-danger">{{ $errors->first('bill.shop_id') }}</span>
            </div>
        </div>
        <div class="col-6 mb-3">
            <label for="memberId" class="form-label ps-2">メイカー<span class="text-danger">※</span></label>
            <div class="ps-2">
                <select name="bill[member_id]" id="memberId" class="form-select">
                    <option value=""></option>
                </select>
                <span class="text-danger">{{ $errors->first('bill.member_id') }}</span>
            </div>
        </div>
        <div class="col-6 mb-3">
            <label for="customerId" class="form-label">顧客ニックネーム<span class="text-danger">※</span></label>
            <div class="pe-2">
                <select name="bill[customer_id]" id="customerId" class="form-select">
                    <option value=""></option>
                    @foreach ($customers as $customer)
                        <option
                            value="{{ $customer->customer_id }}"
                            {{ old('bill.customer_id', $bill->customer_id) == $customer->customer_id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                <span class="text-danger">{{ $errors->first('bill.customer_id') }}</span>
            </div>
        </div>
        <div class="col-6 mb-3 d-flex">
            <div class="ps-2">
                <label class="form-label">シェア</label>
                <div class="d-flex">
                    <div class="radio-group">
                        {{ Form::radio(
                            'bill[share]',
                            0,
                            old('bill.share', $bill->share) == 0 ? 'checked' : '',
                            [
                                'class' => 'form-check-input form-check-radio',
                                'id' => 'shareN'
                            ]
                        ) }}
                        <label for="shareN" class="form-check-label">無</label>
                    </div>
                    <div class="radio-group">
                        {{ Form::radio(
                            'bill[share]',
                            1,
                            old('bill.share', $bill->share) == 1 ? 'checked' : '',
                            [
                                'class' => 'form-check-input form-check-radio',
                                'id' => 'shareY'
                            ]
                        ) }}
                        <label for="shareY" class="form-check-label">有</label>
                    </div>
                </div>
                <span class="text-danger">{{ $errors->first('bill.top_change') }}</span>
            </div>
            <div>
                <label for="topChange" class="form-label">トップ替え</label>
                <div class="d-flex align-items-center gap-2">
                    <select name="bill[top_change]" id="topChange" class="form-select">
                        @foreach (range(0, 10) as $value)
                            <option
                                value="{{ $value }}"
                                {{ old('bill.top_change', $bill->top_change) == $value ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    <span>回</span>
                </div>
                <span class="text-danger">{{ $errors->first('bill.top_change') }}</span>
            </div>
        </div>
        <div class="col-6 mb-3">
            <label for="amount" class="form-label">会計金額<span class="text-danger">※</span></label>
            <div class="input-group pe-2">
                <span class="input-group-text justify-content-center col-1">¥</span>
                {{ Form::text(
                    'bill[amount]',
                    old('bill.amount', $bill->amount),
                    [
                        'class' => 'form-control',
                        'id' => 'amount'
                    ]
                ) }}
            </div>
            <span class="text-danger">{{ $errors->first('bill.amount') }}</span>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label ps-2">会計日時<span class="text-danger">※</span></label>
            <div class="ps-2">
                <div class="input-group">
                    {{ Form::date(
                        'bill[date]',
                        old('bill.date', $bill->bill_day),
                        [
                            'class' => 'form-control'
                        ]
                    ) }}
                    {{ Form::time(
                        'bill[time]',
                        old('bill.time', $bill->bill_time),
                        [
                            'class' => 'form-control'
                        ]
                    ) }}
                </div>
                <div class="d-flex">
                    <span class="col-6 text-danger">{{ $errors->first('bill.date') }}</span>
                    <span class="col-6 text-danger">{{ $errors->first('bill.time') }}</span>
                </div>
            </div>
        </div>
        <div class="tab-wrapper">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @foreach (range(1, 10) as $orderTabCount)
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link @if($orderTabCount == 1) active @endif"
                        id="tab{{ $orderTabCount }}"
                        data-bs-toggle="tab"
                        data-bs-target="#tabContent{{ $orderTabCount }}"
                        type="button"
                        role="tab"
                        aria-controls="tabContent{{ $orderTabCount }}"
                        aria-selected="true">
                    {{ "オーダー{$orderTabCount}" }}
                    </button>
                </li>
                @endforeach
            </ul>
            <div class="tab-content border border-top-0 rounded-bottom" id="orderTabContents">
                @foreach (range(1, 10) as $i => $tabPaneCount)
                <div
                    class="tab-pane fade show @if($tabPaneCount == 1) active @endif"
                    id="tabContent{{ $tabPaneCount }}"
                    role="tabpanel"
                    aria-labelledby="tabContent{{ $tabPaneCount }}">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach (range(1, 10) as $j => $flavorCount)
                            <select name="bill[orders][{{ $i }}][flavors][{{ $j }}]" class="col-3 form-select">
                                <option value="null"></option>
                                @foreach ($flavors as $flavor)
                                    <option
                                        value="{{ $flavor->flavor_id }}"
                                        {{ old("bill.orders.{$i}.flavors.{$j}") == $flavor->flavor_id ? 'selected' : '' }}>
                                        {{ $flavor->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('bill.show', $bill->bill_id) }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
    </div>
</form>

@push('jquery')
<script type="module">
    window.getMembers = "{{ route('bill.getMembers') }}";
    window.requests = @json(old('bill'));
    window.member = @json($bill->member);

    $.each(@json($grouped_orders), function (i, orders) {
        $.each(orders, function (j, order) {
            $(`#tabContent${order.order_id} select`).eq(j).children(`[value=${order.flavor_id}]`).prop('selected', true);
        });
    });
</script>
@vite('resources/js/bill.js')
@endpush

@endsection
