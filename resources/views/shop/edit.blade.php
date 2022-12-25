@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('shop.edit', $shop->shop_id) }}
<form action="{{ route('shop.update', $shop->shop_id) }}" id="shopForm" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <div>
        <label for="shopName" class="form-label">店舗名</label>
        <div class="col-8 d-flex justify-content-between align-items-center">
            {{ Form::text(
                'shop[name]',
                old('shop.name', $shop->name),
                [
                    'class' => 'form-control',
                    'id' => 'shopName'
                ]
            ) }}
        </div>
        <span class="text-danger">{{ $errors->first('shop.name') }}</span>
    </div>
</form>
<div class="col-8 d-flex align-items-center justify-content-between mt-3">
    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary col-2">戻る</a>
    <button type="button" class="btn btn-outline-primary col-2" id="updateButton">更新</button>
</div>

@push('jquery')
@vite('resources/js/shop/edit.js')
@endpush

@endsection
