@extends('layouts.parent')

@push('css')
@vite('resources/css/mix/mix.css')
@endpush

@section('content')
{{ Breadcrumbs::render('mix.show', $mixPreset->id) }}
<div>
    <div class="col-12 mb-3">
        <label class="form-label">ミックス名</label>
        <div>
            <span>{{ $mixPreset->name }}</span>
        </div>
    </div>
    <ul class="d-flex flex-wrap">
        @foreach ($mixPreset->mixes as $mix)
        <li class="col-3 mb-3">
            <div>セット{{ $loop->iteration }}</div>
            <div class="mix-group">
                <span class="{{ $mix->bland->trashed() ? 'text-danger text-decoration-line-through' : '' }}">
                    {{ $mix->bland->name }}
                </span>
                <span class="mx-3">/</span>
                <span class="{{ $mix->flavor->trashed() ? 'text-danger text-decoration-line-through' : '' }}">
                    {{ $mix->flavor->name }}
                </span>
            </div>
        </li>
        @endforeach
    </ul>
</div>
<div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
    <a href="{{ route('mix.index') }}" class="btn btn-secondary">戻る</a>
    <a href="{{ route('mix.edit', $mixPreset->id) }}" class="btn btn-success">編集</a>
    <button type="button" class="btn btn-danger" id="deleteButton" data-route="{{ route('mix.destroy', $mixPreset->id) }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
        削除
    </button>
</div>

@endsection
