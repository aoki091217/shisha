@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('customer.show', $customer->id) }}
<div class="d-flex flex-wrap">
    <div class="col-12 mb-3">
        <label class="form-label">顧客名</label>
        <div>
            <span>{{ $customer->name }}</span>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">チェックイン履歴</label>
        <div class="table-wrapper" style="height: 10rem; width: 80%;">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="bg-light col-6" scope="col">チェックイン店舗</th>
                        <th class="bg-light col-6" scope="col">最終チェックイン日時</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer->customerShops as $customerShop)
                    <tr>
                        <td>{{ $customerShop->shop->name }}</td>
                        <td>{{ $customerShop->checkin_datetime }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">ミックス履歴</label>
        <div class="table-wrapper" style="height: 10rem; width: 80%;">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="bg-light col-2" scope="col">ミックス名</th>
                        <th class="bg-light col-5" scope="col">ブランド：フレーバー</th>
                        <th class="bg-light col-2" scope="col">来店名</th>
                        <th class="bg-light col-3" scope="col">会計日時</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!is_null($billCustomer))
                    @foreach ($billCustomer->billOrders as $billOrder)
                    <tr>
                        <td>{{ $billOrder->mix->name }}</td>
                        <td>
                            @foreach ($billOrder->mix->mixes as $mix)
                            @php
                                $format = !$loop->last ? '%s：%s, ' : '%s：%s';
                            @endphp
                                <span>
                                    {{ sprintf($format, $mix->bland->name, $mix->flavor->name) }}
                                </span>
                            @endforeach
                        </td>
                        <td>{{ $billOrder->bill?->shop->name }}</td>
                        <td>{{ $billOrder->bill?->bill_datetime }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
    <a href="{{ route('customer.index') }}" class="btn btn-secondary">戻る</a>
</div>

@endsection
