@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('flavor.edit', $flavor->flavor_id) }}
<form action="{{ route('flavor.update', $flavor->flavor_id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <div>
        <label for="blandName" class="form-label">ブランド名<span class="text-danger">※</span></label>
        <div class="col-8 d-flex justify-content-between align-items-center mb-3">
            <select name="flavor[bland_id]" id="blandName" class="form-select">
                @foreach ($blands as $bland)
                    <option value="{{ $bland->bland_id }}"
                            {{ old('flavor.bland_id', $flavor->bland_id) == $bland->bland_id ? 'selected' : '' }}>
                        {{ $bland->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <span class="text-danger">{{ $errors->first('flavor.bland_id') }}</span>
        <label for="flavorName" class="form-label">フレーバー名<span class="text-danger">※</span></label>
        <div class="col-8 d-flex justify-content-between align-items-center">
            {{ Form::text(
                'flavor[name]',
                old('flavor.name', $flavor->name),
                [
                    'class' => 'form-control',
                    'id' => 'flavorName'
                ]
            ) }}
        </div>
        <span class="text-danger">{{ $errors->first('flavor.name') }}</span>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('flavor.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
    </div>
</form>


@endsection
