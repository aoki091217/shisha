@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('flavor.create') }}
<form action="{{ route('flavor.store') }}" method="POST" autocomplete="off">
    @csrf
    <div>
        <label for="blandName" class="form-label">ブランド名<span class="text-danger">※</span></label>
        <div class="col-8 mb-3">
            <select name="flavor[bland_id]" id="blandName" class="form-select">
                <option value=""></option>
                @foreach ($blands as $bland)
                    <option value="{{ $bland->bland_id }}" {{ old('flavor.bland_id') == $bland->name ? 'selected' : '' }}>
                        {{ $bland->name }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('flavor.bland_id') }}</span>
        </div>
        <div>
            @foreach (range(0, 5) as $index)
            <label for="{{ "flavorName{$index}" }}" class="form-label mb-0">
                フレーバー名@if ($index === 0)<span class="text-danger">※</span>@endif
            </label>
            <div class="col-8 mb-2">
                {{ Form::text(
                    "flavor[names][{$index}]",
                    old("flavor.names.{$index}"),
                    [
                        'class' => 'form-control',
                        'id' => "flavorName{$index}"
                    ]
                ) }}
                <span class="text-danger">{{ $errors->first("flavor.names.{$index}") }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('flavor.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">追加</button>
    </div>
</form>

@endsection
