@extends('layouts.parent')

@push('css')
@vite('resources/css/shop/index.css')
@endpush

@section('content')
{{ Breadcrumbs::render('shop.index') }}
<form action="" id="shopForm" method="POST" autocomplete="off">
    @csrf
    @method('DELETE')
    <div>
        <label for="shopName" class="form-label">店舗名</label>
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="col-8">
                {{ Form::text(
                    'shop[name]',
                    old('shop.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'shopName'
                    ]
                ) }}
            </div>
            <div class="col-4">
                <button
                    type="button"
                    class="col-3 btn btn-outline-primary ms-3"
                    id="createButton"
                    data-route="{{ route('shop.store') }}">
                    追加
                </button>
            </div>
        </div>
        <span class="text-danger">{{ $errors->first('shop.name') }}</span>
    </div>
</form>
<div class="table-shop">
    <table class="table mb-0">
        <thead>
            <tr>
                <th class="bg-light col-2" scope="col">ID</th>
                <th class="bg-light col-5" scope="col">店舗名</th>
                <th class="bg-light col-3" scope="col">登録日</th>
                <th class="bg-light col"></th>
                <th class="bg-light col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shops as $shop)
            <tr>
                <td>{{ $shop->shop_id }}</td>
                <td>{{ $shop->name }}</td>
                <td>{{ $shop->created_datetime }}</td>
                <td>
                    <div class="col-12">
                        <a href="{{ route('shop.edit', $shop->shop_id) }}" class="btn btn-sm btn-outline-success w-100">編集</a>
                    </div>
                </td>
                <td>
                    <div class="col-12">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger w-100"
                            data-route="{{ route('shop.destroy', $shop->shop_id) }}">
                            削除
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-3">
    {{ $shops->links() }}
</div>

@push('jquery')
@vite('resources/js/shop/index.js')
@endpush

@endsection
