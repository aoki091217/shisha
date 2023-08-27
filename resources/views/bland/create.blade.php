@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('bland.create') }}
<form action="{{ route('bland.store') }}" method="POST" autocomplete="off">
    @csrf
    <div>
        @if (auth()->user()->role_id === 1)
        <label for="shopName" class="form-label">店舗<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            <select name="bland[shop_id]" id="shopName" class="form-select">
                <option value=""></option>
                @foreach ($shops as $shop)
                    <option value="{{ $shop->shop_id }}" {{ old('bland.shop_id') == $shop->shop_id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('bland.shop_id') }}</span>
        </div>
        @else
        <label for="shopName" class="form-label">店舗</label>
        <div class="col-8 mb-3">
            <span>{{ auth()->user()->member->shop->name }}</span>
        </div>
        @endif
        <label for="blandName" class="form-label">ブランド名<span class="text-danger">※</span></label>
        <div class="col-8">
            {{ Form::text(
                'bland[name]',
                old('bland.name'),
                [
                    'class' => 'form-control',
                    'id' => 'blandName'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('bland.name') }}</span>
        </div>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('bland.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">追加</button>
    </div>
</form>

@endsection
