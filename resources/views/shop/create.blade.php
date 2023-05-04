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
        <label for="userName" class="form-label">ユーザー名<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::text(
                'user[name]',
                old('user.name'),
                [
                    'class' => 'form-control',
                    'id' => 'userName'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('user.name') }}</span>
        </div>
        <label for="userCode" class="form-label">ユーザーID<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::text(
                'user[code]',
                old('user.code'),
                [
                    'class' => 'form-control',
                    'id' => 'userCode'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('user.code') }}</span>
        </div>
        <label for="userPassword" class="form-label">パスワード<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::password(
                'user[password]',
                [
                    'class' => 'form-control',
                    'id' => 'userPassword'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('user.password') }}</span>
        </div>
        <label for="userPassConfirm" class="form-label">パスワード（確認用）<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::password(
                'user[password_confirmation]',
                [
                    'class' => 'form-control',
                    'id' => 'userPassConfirm'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('user.password_confirmation') }}</span>
        </div>
        <label for="userEmail" class="form-label">メールアドレス<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::text(
                'user[email]',
                old('user.email'),
                [
                    'class' => 'form-control',
                    'id' => 'userEmail'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('user.email') }}</span>
        </div>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('shop.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">追加</button>
    </div>
</form>

@endsection
