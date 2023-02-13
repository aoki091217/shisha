@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('bland.create') }}
<form action="{{ route('bland.store') }}" method="POST" autocomplete="off">
    @csrf
    <div>
        <label for="blandName" class="form-label">ブランド名</label>
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
