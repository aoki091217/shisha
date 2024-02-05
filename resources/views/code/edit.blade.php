@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('code.edit', $code->getCodeId()) }}
<form action="{{ route('code.update', $code->getCodeId()) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <div>
        @if (auth()->user()->role_id === 1)
        <label for="shopName" class="form-label">店舗<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            <select name="codes[shop_id]" id="shopName" class="form-select">
                <option value=""></option>
                @foreach ($shops as $shop)
                    <option value="{{ $shop->shop_id }}" {{ old('codes.shop_id', $code->getShopId()) == $shop->shop_id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('codes.shop_id') }}</span>
        </div>
        @else
        <label for="shopName" class="form-label">店舗</label>
        <div class="col-8 mb-3">
            <span>{{ $code->shop->name }}</span>
        </div>
        @endif
        <label for="codeName" class="form-label">コード名<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::text(
                'codes[name]',
                old('codes.name', $code->getName()),
                [
                    'class' => 'form-control',
                    'id' => 'codeName'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('codes.name') }}</span>
        </div>
        <label class="form-label">コード種別<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            <div class="radio-group">
                {{ Form::radio(
                    'codes[kind]',
                    1,
                    old('codes.kind', $code->getKind()) == 1,
                    [
                        'class' => 'form-check-input form-check-radio',
                        'id' => "kind_route"
                    ]
                ) }}
                <label for="kind_route" class="form-check-label">
                    流入経路計測：<span class="kind_route_text">sid={{ $shops->first()->shop_id }}&</span>
                </label>
            </div>

            <div class="radio-group">
                {{ Form::radio(
                    'codes[kind]',
                    2,
                    old('codes.kind', $code->getKind()) == 2,
                    [
                        'class' => 'form-check-input form-check-radio',
                        'id' => "kind_checkin"
                    ]
                ) }}
                <label for="kind_checkin" class="form-check-label">
                    チェックイン：<span class="kind_checkin_text">shop_id={{ $shops->first()->shop_id }}</span>
                </label>
            </div>
        </div>
        <label for="codeSituation" class="form-label">友達追加時送信メッセージ<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            <select name="codes[situation_id]" id="codeSituation" class="form-select">
                <option value=""></option>
                @foreach ($situations as $situation)
                    <option value="{{ $situation->id }}" {{ old('codes.situation_id', $code->getSituationId()) == $situation->id ? 'selected' : '' }}>
                        {{ $situation->name }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('codes.situation_id') }}</span>
        </div>
        <label for="codeParameter" class="form-label">パラメータ<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            {{ Form::text(
                'codes[parameter]',
                old('codes.parameter', $code->getParameter()),
                [
                    'class' => 'form-control',
                    'id' => 'codeParameter'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('codes.parameter') }}</span>
        </div>
        <label for="codeScript" class="form-label">埋め込みスクリプト<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            <textarea maxlength="5000" id="codeScript" name="codes[script]" class="form-control" style="height: 10rem;" data-name="text">{{ old('codes.script', $code->getScript()) }}</textarea>
            <span class="text-danger">{{ $errors->first('codes.script') }}</span>
        </div>
        <label for="codeNotes" class="form-label">備考欄</label>
        <div class="col-8 mb-3">
            <textarea maxlength="5000" id="codeNotes" name="codes[notes]" class="form-control" style="height: 10rem;" data-name="text">{{ old('codes.notes', $code->getNotes()) }}</textarea>
            <span class="text-danger">{{ $errors->first('codes.notes') }}</span>
        </div>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('code.show', $code->getCodeId()) }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
    </div>
</form>

@endsection

@push('jquery')
    @vite('resources/js/code.js')
@endpush
