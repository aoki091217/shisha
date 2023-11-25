@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('bland.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('bland.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    @method('DELETE')
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col">
                <label for="shopName" class="form-label">店舗</label>
                @if (auth()->user()->role_id === 1)
                <select name="bland[shop_id]" id="shopName" class="form-select">
                    <option value=""></option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->shop_id }}" {{ request('bland.shop_id') == $shop->shop_id ? 'selected' : '' }}>
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
                <label for="blandName" class="form-label">ブランド名</label>
                {{ Form::text(
                    'bland[name]',
                    request('bland.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'blandName'
                    ]
                ) }}
            </div>
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('bland.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('bland.create') }}" class="col-12 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col-1" scope="col">ID</th>
                    <th class="bg-light col-2" scope="col">店舗</th>
                    <th class="bg-light col-4" scope="col">ブランド</th>
                    <th class="bg-light col-3" scope="col">登録日</th>
                    <th class="bg-light col"></th>
                    <th class="bg-light col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($blands as $bland)
                <tr>
                    <td>{{ $bland->bland_id }}</td>
                    <td class="{{ $bland->shop->trashed() ? 'text-danger text-decoration-line-through' : '' }}">
                        {{ $bland->shop->name }}
                    </td>
                    <td>{{ $bland->name }}</td>
                    <td>{{ $bland->created_datetime }}</td>
                    <td>
                        <div class="col-12">
                            <a href="{{ route('bland.edit', $bland->bland_id) }}" class="btn btn-sm btn-success w-100">編集</a>
                        </div>
                    </td>
                    <td>
                        <div class="col-12">
                            <button type="button" class="btn btn-sm btn-danger w-100 btn-delete" data-route="{{ route('bland.destroy', $bland->bland_id) }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
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
    {{ $blands->appends(request()->all())->links() }}
</div>

@endsection
