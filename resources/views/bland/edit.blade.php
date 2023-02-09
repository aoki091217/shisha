@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('bland.edit', $bland->bland_id) }}
<form action="{{ route('bland.update', $bland->bland_id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <div>
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
