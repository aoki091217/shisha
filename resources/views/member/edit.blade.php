@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('member.edit', $member->id) }}
<form action="{{ route('member.update', $member->id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <div>
        @if (auth()->user()->role_id === 1)
        <label for="shopName" class="form-label">店舗<span class="text-danger">※</span></label>
        <div class="col-8 d-flex justify-content-between align-items-center mb-3">
            <select name="member[shop_id]" id="shopName" class="form-select">
                @foreach ($shops as $shop)
                    <option value="{{ $shop->shop_id }}"
                            {{ old('member.shop_id', $member->shop_id) == $shop->shop_id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @else
        <label for="shopName" class="form-label">店舗</label>
        <div class="col-8 mb-3">
            <span>{{ $member->shop->name }}</span>
            <input type="hidden" name="member[shop_id]" value="{{ $member->shop_id }}">
        </div>
        @endif

        <span class="text-danger">{{ $errors->first('member.shop_id') }}</span>
        <label for="memberName" class="form-label">スタッフ名<span class="text-danger">※</span></label>
        <div class="col-8 mb-3 d-flex justify-content-between align-items-center">
            {{ Form::text(
                'member[name]',
                old('member.name', $member->name),
                [
                    'class' => 'form-control',
                    'id' => 'memberName'
                ]
            ) }}
        </div>
        <span class="text-danger">{{ $errors->first('member.name') }}</span>
    </div>
    <label for="userCode" class="form-label">ユーザーID<span class="text-danger">※</span></label>
    <div class="col-8 mb-3">
        {{ Form::text(
            'user[code]',
            old('user.code', $member->user->code),
            [
                'class' => 'form-control',
                'id' => 'userCode'
            ]
        ) }}
        <span class="text-danger">{{ $errors->first('user.code') }}</span>
    </div>
    <div class="d-flex gap-3">
        <label for="userPassword" class="form-label">パスワード<span class="text-danger">※</span></label>
        <div class="d-flex gap-2">
            {{ Form::checkbox(
                'user[is_change]',
                1,
                request('user.password') ? 'checked' : '',
                [
                    'id' => 'isChange',
                    'class' => 'form-check-input form-checkbox'
                ]
            ) }}
            <label for="isChange" class="form-check-label">変更する</label>
        </div>
    </div>
    <div class="col-8 mb-3">
        {{ Form::password(
            'user[password]',
            [
                'class' => 'form-control',
                'id' => 'userPassword',
                'disabled'
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
                'id' => 'userPassConfirm',
                'disabled'
            ]
        ) }}
        <span class="text-danger">{{ $errors->first('user.password_confirmation') }}</span>
    </div>
    <label for="userRole" class="form-label">権限</label>
    <div class="col-8 mb-3">

        @if (in_array(auth()->user()->role_id, [1, 2]))

        @php
            if (auth()->user()->role_id === 2) {
                $roles = $roles->where('id', '<>', 1);
            }
        @endphp

        <select name="user[role_id]" id="userRole" class="form-select">
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" {{ old('user.role_id', $member->user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </select>
        @else
        <div>{{ $member->user->role->name }}</div>
        @endif

        <span class="text-danger">{{ $errors->first('user.role_id') }}</span>
    </div>
    <label for="userEmail" class="form-label">メールアドレス<span class="text-danger">※</span></label>
    <div class="col-8 mb-3">
        {{ Form::text(
            'user[email]',
            old('user.email', $member->user->email),
            [
                'class' => 'form-control',
                'id' => 'userEmail'
            ]
        ) }}
        <span class="text-danger">{{ $errors->first('user.email') }}</span>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('member.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
    </div>
</form>

@push('jquery')
@vite('resources/js/user.js')
@endpush

@endsection
