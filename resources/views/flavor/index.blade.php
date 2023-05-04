@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('flavor.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('flavor.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    @method('DELETE')
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col-4">
                <label for="blandName" class="form-label">ブランド名</label>
                <select name="flavor[bland_id]" id="blandName" class="form-select">
                    <option value=""></option>
                    @foreach ($blands as $bland)
                        <option value="{{ $bland->bland_id }}" {{ request('flavor.bland_id') == $bland->bland_id ? 'selected' : '' }}>
                            {{ $bland->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-4">
                <label for="flavorName" class="form-label">フレーバー名</label>
                {{ Form::text(
                    'flavor[name]',
                    request('flavor.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'flavorName'
                    ]
                ) }}
            </div>
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('flavor.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('flavor.create') }}" class="col-12 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col-2" scope="col">ID</th>
                    <th class="bg-light col-3" scope="col">ブランド名</th>
                    <th class="bg-light col-3" scope="col">フレーバー名</th>
                    <th class="bg-light col-2" scope="col">登録日</th>
                    <th class="bg-light col"></th>
                    <th class="bg-light col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($flavors as $flavor)
                <tr>
                    <td>{{ $flavor->flavor_id }}</td>
                    <td class="{{ $flavor->bland->trashed() ? 'text-danger text-decoration-line-through' : '' }}">
                        {{ $flavor->bland->name }}
                    </td>
                    <td>{{ $flavor->name }}</td>
                    <td>{{ $flavor->created_datetime }}</td>
                    <td>
                        <div class="col-12">
                            <a href="{{ route('flavor.edit', $flavor->flavor_id) }}" class="btn btn-sm btn-success w-100">編集</a>
                        </div>
                    </td>
                    <td>
                        <div class="col-12">
                            <button type="button" class="btn btn-sm btn-danger w-100 btn-delete" data-route="{{ route('flavor.destroy', $flavor->flavor_id) }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                削除
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>
<div class="mt-3" id="footer">
    {{ $flavors->links() }}
</div>

@endsection
