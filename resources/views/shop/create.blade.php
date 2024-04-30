@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('shop.create') }}
<form action="{{ route('shop.store') }}" method="POST" autocomplete="off">
    @csrf
    <div>
        <label for="shopName" class="form-label">店舗名</label>
        <div class="col-8 mb-3">
            {{ Form::text(
                'shop[name]',
                old('shop.name'),
                [
                    'class' => 'form-control',
                    'id' => 'shopName'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('shop.name') }}</span>
        </div>
        <label for="accountId" class="form-label">ボットベーシックID<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::text(
                'shop[account_id]',
                old('shop.account_id'),
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
                old('shop.line_token'),
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
                old('shop.channel_secret'),
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
                old('shop.liff_id'),
                [
                    'class' => 'form-control',
                    'id' => 'liffId'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('shop.liff_id') }}</span>
        </div>
        <label for="liffChannelId" class="form-label">Liff チャネルID<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::text(
                'shop[liff_channel_id]',
                old('shop.liff_channel_id'),
                [
                    'class' => 'form-control',
                    'id' => 'liffChannelId'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('shop.liff_channel_id') }}</span>
        </div>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('shop.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">追加</button>
    </div>
</form>

@endsection
