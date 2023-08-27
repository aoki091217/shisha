@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('bland.edit', $bland->bland_id) }}
<form action="{{ route('bland.update', $bland->bland_id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <div>
        @if (auth()->user()->role_id === 1)
        <label for="shopName" class="form-label">店舗<span class="text-danger">※</span></label>
        <div class="col-8 d-flex justify-content-between align-items-center mb-3">
            <select name="bland[shop_id]" id="shopName" class="form-select">
                @foreach ($shops as $shop)
                    <option value="{{ $shop->shop_id }}"
                            {{ old('bland.shop_id', $bland->shop_id) == $shop->shop_id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @else
        <label for="shopName" class="form-label">店舗</label>
        <div class="col-8 mb-3">
            <span>{{ $bland->shop->name }}</span>
            <input type="hidden" name="bland[shop_id]" value="{{ $bland->shop_id }}">
        </div>
        @endif
        <label for="blandName" class="form-label">ブランド名</label>
        <div class="col-8">
            {{ Form::text(
                'bland[name]',
                old('bland.name', $bland->name),
                [
                    'class' => 'form-control',
                    'id' => 'blandName'
                ]
            ) }}
        </div>
        <span class="text-danger">{{ $errors->first('bland.name') }}</span>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('bland.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
    </div>
</form>

@endsection
