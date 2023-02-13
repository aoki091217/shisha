@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('shop.edit', $shop->shop_id) }}
<form action="{{ route('shop.update', $shop->shop_id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <div>
        <label for="shopName" class="form-label">店舗</label>
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
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('shop.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
    </div>
</form>

@endsection
