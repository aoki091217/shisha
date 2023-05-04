@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('mix.create') }}
<form action="{{ route('mix.store') }}" method="POST" autocomplete="off">
    @csrf
    <div>
        <div class="col-12 mb-3">
            <label for="mixName" class="form-label">ミックス名<span class="text-danger">※</span></label>
            <div class="col-6">
                {{ Form::text(
                    'mix[name]',
                    old('mix.name', request('mix.name')),
                    [
                        'class' => 'form-control',
                        'id' => 'mixName',
                        'maxlength' => 50
                    ]
                ) }}
                <span class="text-danger">{{ $errors->first('mix.name') }}</span>
            </div>
        </div>
        @foreach (range(0, 4) as $index)
        <div class="col-12 d-flex align-items-center flex-wrap mb-3 select-wrapper">
            <div class="col-6">
                <label for="blandName_{{ $index }}" class="form-label">ブランド名</label>
                <div>
                    <select name="mix[presets][{{ $index }}][bland_id]" class="form-select border-end-0 bland-select" style="border-radius: 0.375rem 0 0 0.375rem;" id="blandName_{{ $index }}">
                        <option value=""></option>
                        @foreach ($blands as $bland)
                            <option
                                value="{{ $bland->bland_id }}"
                                {{ old("mix.presets.{$index}.bland_id", request("mix.presets.{$index}.bland_id")) == $bland->bland_id ? 'selected' : '' }}>
                                {{ $bland->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6">
                <label for="flavorName_{{ $index }}" class="form-label">フレーバー名</label>
                <div>
                    <select name="mix[presets][{{ $index }}][flavor_id]" class="form-select flavor-select" style="border-radius: 0 0.375rem 0.375rem 0;" id="flavorName_{{ $index }}">
                        <option value=""></option>
                        @if (!is_null(old("mix.presets.{$index}.bland_id")))
                        @foreach ($blands->find(old("mix.presets.{$index}.bland_id"))->flavors as $flavor)
                            <option
                                value="{{ $flavor->flavor_id }}"
                                {{ old("mix.presets.{$index}.flavor_id", request("mix.presets.{$index}.flavor_id")) == $flavor->flavor_id ? 'selected' : '' }}>
                                {{ $flavor->name }}
                            </option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <span class="col-6 text-danger">{{ $errors->first("mix.presets.{$index}.bland_id") }}</span>
            <span class="col-6 text-danger">{{ $errors->first("mix.presets.{$index}.flavor_id") }}</span>
        </div>
        @endforeach
    </div>
    <div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('mix.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">追加</button>
    </div>
</form>

@push('jquery')
@vite('resources/js/mix.js')
<script defer>
    window.old = @json(old('mix'));
    window.requestMix = @json(request('mix'));
    window.getFlavors = "{{ route('mix.getFlavors') }}"
</script>
@endpush

@endsection
