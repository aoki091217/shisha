@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('mix.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('mix.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col">
                <label for="shopName" class="form-label">店舗</label>
                @if (auth()->user()->role_id === 1)
                <select name="mix[shop_id]" id="shopName" class="form-select">
                    <option value=""></option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->shop_id }}" {{ request('mix.shop_id') == $shop->shop_id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
                @else
                <div style="padding: 0.375rem 0">
                    <span>{{ auth()->user()->member->shop->name }}</span>
                </div>
                @endif
            </div>
            <div class="col-6">
                <label for="mixName" class="form-label">ミックス名</label>
                {{ Form::text(
                    'mix[name]',
                    request('mix.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'mixName'
                    ]
                ) }}
            </div>
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('mix.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('mix.create') }}" class="col-12 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col-2" scope="col">ID</th>
                    <th class="bg-light col-2" scope="col">店舗</th>
                    <th class="bg-light col-4" scope="col">ミックス名</th>
                    <th class="bg-light col-4" scope="col">登録日</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mixPresets as $mixPreset)
                <tr>
                    <td>
                        <a href="{{ route('mix.show', $mixPreset->id) }}" class="d-block w-100">
                            {{ $mixPreset->id }}
                        </a>
                    </td>
                    <td class="{{ $mixPreset->shop->trashed() ? 'text-danger text-decoration-line-through' : '' }}">
                        {{ $mixPreset->shop->name }}
                    </td>
                    <td>{{ $mixPreset->name }}</td>
                    <td>{{ $mixPreset->created_datetime }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>
<div class="mt-3" id="footer">
    {{ $mixPresets->appends(request()->all())->links() }}
</div>

@endsection
