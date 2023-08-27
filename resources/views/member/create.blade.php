@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('member.create') }}
<form action="{{ route('member.store') }}" method="POST" autocomplete="off">
    @csrf
    <div>
        @if (auth()->user()->role_id === 1)
        <label for="shopName" class="form-label">店舗<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            <select name="member[shop_id]" id="shopName" class="form-select">
                <option value=""></option>
                @foreach ($shops as $shop)
                    <option value="{{ $shop->shop_id }}" {{ old('member.shop_id') == $shop->shop_id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('member.shop_id') }}</span>
        </div>
        @else
        <label for="shopName" class="form-label">店舗</label>
        <div class="col-8 mb-3">
            <span>{{ auth()->user()->member->shop->name }}</span>
        </div>
        @endif

        <label for="memberName" class="form-label">スタッフ名<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::text(
                'member[name]',
                old('member.name'),
                [
                    'class' => 'form-control',
                    'id' => 'memberName'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('member.name') }}</span>
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
        <small class="text-secondary ms-3">8文字以上の英数字のみ</small>
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
        <small class="text-secondary ms-3">8文字以上の英数字のみ</small>
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
        <label for="userRole" class="form-label">権限</label>
        <div class="col-8 mb-3">
            <select name="user[role_id]" id="userRole" class="form-select">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ old('user.role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('user.role_id') }}</span>
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
        <a href="{{ route('member.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">追加</button>
    </div>
</form>

@endsection
