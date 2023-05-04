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
        <div class="col-4 mb-3">
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
        <div class="col-4 mb-3 ps-2">
            <label for="memberId" class="form-label">メイカー<span class="text-danger">※</span></label>
            <div>
                <select name="bill[member_id]" id="memberId" class="form-select">
                    <option value=""></option>
                </select>
                <span class="text-danger">{{ $errors->first('bill.member_id') }}</span>
            </div>
        </div>
        <div class="col-4 mb-3 ps-2 d-flex">
            <div>
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
        <div class="col-8 mb-3">
            <label for="customerId" class="form-label col-12 pe-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="col-1 text-center">顧客名</span>
                    <input type="search" class="form-control form-control-sm" id="searchName">
                    <span class="col-1 text-center">店舗名</span>
                    <select class="form-select form-select-sm" id="searchShop">
                        <option value=""></option>
                        @foreach ($shops as $shop)
                            <option value="{{ $shop->shop_id }}">
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="col-1 btn btn-sm btn-light border" id="reloadButton">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                    <button type="button" class="col-2 btn btn-sm btn-warning" id="searchButton">検索</button>
                </div>
            </label>
            <div class="table-wrapper pe-2">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="bg-light col-5" scope="col">名前</th>
                            <th class="bg-light col-5" scope="col">最終チェックイン日時</th>
                            <th class="bg-light col-2" scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customerShops as $customerShop)
                            <tr>
                                <td>{{ $customerShop->first()->customer->name }}</td>
                                <td>{{ $customerShop->first()->visited_at }}</td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary w-100"
                                        data-name="{{ $customerShop->first()->customer->name }}"
                                        data-customer-id="{{ $customerShop->first()->customer->id }}">
                                        選択
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-4 mb-3 d-flex">
            <div class="col-12 ps-2 mb-3">
                <span>顧客選択リスト</span>
                <div class="list-wrapper">
                    <ul class="list-group">
                        @foreach ($bill->billCustomers as $billCustomer)
                        <li class="list-group-item list-item-template">
                            <span class="text-wrap">{{ $billCustomer->customer->name }}</span>
                            <input type="hidden" name="bill[customers][]" value="{{ $billCustomer->id }}">
                            <button type="button" class="btn btn-sm btn-danger btn-cancel">外す</button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="d-none">
                    <li class="list-group-item list-item-template d-none">
                        <span class="text-wrap"></span>
                        <input type="hidden" value="">
                        <button type="button" class="btn btn-sm btn-danger btn-cancel">外す</button>
                    </li>
                </div>
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
                        @foreach (range(1, 5) as $j => $mixCount)
                            <select name="bill[orders][{{ $i }}][mixes][{{ $j }}]" class="col-3 form-select">
                                <option value="null"></option>
                                @foreach ($mixPresets as $preset)
                                    <option
                                        value="{{ $preset->id }}"
                                        {{ old("bill.orders.{$i}.mixes.{$j}") == $preset->id ? 'selected' : '' }}>
                                        {{ $preset->name }}
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
    window.getCustomers = "{{ route('bill.getCustomers') }}";
    window.getMembers = "{{ route('bill.getMembers') }}";
    window.requests = @json(old('bill'));
    window.member = @json($bill->member);

    $.each(@json($grouped_orders), function (i, orders) {
        $.each(orders, function (j, order) {
            $(`#tabContent${order.order_id} select`).eq(j).children(`[value=${order.mix_id}]`).prop('selected', true);
        });
    });
</script>
@vite('resources/js/bill.js')
@endpush

@endsection
