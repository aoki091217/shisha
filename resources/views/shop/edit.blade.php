@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('shop.edit', $shop->shop_id) }}
<form action="{{ route('shop.update', $shop->shop_id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <input type="hidden" name="user[user_id]" value="{{ $shop->user_id }}">
    <label for="shopName" class="form-label">店舗</label>
    <div class="col-8 mb-3">
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
    <label for="accountId" class="form-label">ボットベーシックID<span class="text-danger">※</span></label>
    <div class="col-8 mb-3">
        {{ Form::text(
            'shop[account_id]',
            old('shop.account_id', $shop->account_id),
            [
                'class' => 'form-control',
                'id' => 'accountId'
            ]
        ) }}
        <span class="text-danger">{{ $errors->first('shop.account_id') }}</span>
    </div>
    <label for="lineToken" class="form-label">アクセストークン<span class="text-danger">※</span></label>
    <div class="col-8 mb-3">
        {{ Form::text(
            'shop[line_token]',
            old('shop.line_token', $shop->line_token),
            [
                'class' => 'form-control',
                'id' => 'lineToken'
            ]
        ) }}
        <span class="text-danger">{{ $errors->first('shop.line_token') }}</span>
    </div>
    <label for="channelSecret" class="form-label">チャネルシークレット<span class="text-danger">※</span></label>
    <div class="col-8 mb-3">
        {{ Form::text(
            'shop[channel_secret]',
            old('shop.channel_secret', $shop->channel_secret),
            [
                'class' => 'form-control',
                'id' => 'channelSecret'
            ]
        ) }}
        <span class="text-danger">{{ $errors->first('shop.channel_secret') }}</span>
    </div>
    <label for="liffId" class="form-label">Liff ID<span class="text-danger">※</span></label>
    <div class="col-8 mb-3">
        {{ Form::text(
            'shop[liff_id]',
            old('shop.liff_id', $shop->liff_id),
            [
                'class' => 'form-control',
                'id' => 'liffId'
            ]
        ) }}
        <span class="text-danger">{{ $errors->first('shop.liff_id') }}</span>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('shop.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
    </div>
</form>

@push('jquery')
@vite('resources/js/user.js')
@endpush

@endsection
