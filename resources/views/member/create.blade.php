@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('member.create') }}
<form action="{{ route('member.store') }}" method="POST" autocomplete="off">
    @csrf
    <div>
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
        <label for="memberName" class="form-label">スタッフ<span class="text-danger">※</span></label>
        <div class="col-8">
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
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('member.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">追加</button>
    </div>
</form>

@endsection
