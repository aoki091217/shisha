@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('mix.edit', $mixPreset->id) }}
<form action="{{ route('mix.update', $mixPreset->id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <div>
        <div class="col-12 mb-3">
            <label for="mixName" class="form-label">ミックス名<span class="text-danger">※</span></label>
            <div class="col-6">
                {{ Form::text(
                    'mix[name]',
                    old('mix.name', $mixPreset->name),
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
                        @php
                            $mix = isset($mixPreset->mixes[$index]) ? $mixPreset->mixes[$index] : null;
                            $bland_id = $mix?->bland_id;
                        @endphp
                            <option
                                value="{{ $bland->bland_id }}"
                                {{ old("mix.presets.{$index}.bland_id", $bland_id) == $bland->bland_id ? 'selected' : '' }}>
                                {{ $bland->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6">
                <label for="flavorName_{{ $index }}" class="form-label">フレーバー名</label>
                <div>
                    @php
                        if (!is_null(old("mix.presets.{$index}.bland_id"))) {
                            $bland_id = old("mix.presets.{$index}.bland_id");
                        }
                        $flavors = !is_null($blands->find($bland_id)) ? $blands->find($bland_id)->flavors : [];
                    @endphp
                    <select name="mix[presets][{{ $index }}][flavor_id]" class="form-select flavor-select" style="border-radius: 0 0.375rem 0.375rem 0;" id="flavorName_{{ $index }}">
                        <option value=""></option>
                        @foreach ($flavors as $flavor)
                            <option
                                value="{{ $flavor->flavor_id }}"
                                {{ old("mix.presets.{$index}.flavor_id", $mix?->flavor_id) == $flavor->flavor_id ? 'selected' : '' }}>
                                {{ $flavor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <span class="col-6 text-danger">{{ $errors->first("mix.presets.{$index}.bland_id") }}</span>
            <span class="col-6 text-danger">{{ $errors->first("mix.presets.{$index}.flavor_id") }}</span>
            <input type="hidden" name="mix[presets][{{ $index }}][mix_id]" value="{{ $mix?->id }}">
        </div>
        @endforeach
    </div>
    <div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('mix.show', $mixPreset->id) }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
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
