@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('shop.create') }}
<form action="{{ route('shop.store') }}" method="POST" autocomplete="off">
    @csrf
    <div>
        <label for="shopName" class="form-label">店舗</label>
        <div class="col-8">
            {{ Form::text(
                'shop[name]',
                old('shop.name'),
                [
                    'class' => 'form-control',
                    'id' => 'shopName'
                ]
            ) }}
            <span class="text-danger">{{ $errors->first('shop.name') }}</span>
        </div>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('shop.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">追加</button>
    </div>
</form>

@endsection
