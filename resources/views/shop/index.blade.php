@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('shop.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('shop.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    @method('DELETE')
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col-8">
                <label for="shopName" class="form-label">店舗</label>
                {{ Form::text(
                    'shop[name]',
                    request('shop.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'shopName'
                    ]
                ) }}
            </div>
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('shop.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('shop.create') }}" class="col-12 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col-2" scope="col">ID</th>
                    <th class="bg-light col-4" scope="col">店舗</th>
                    <th class="bg-light col-2" scope="col">登録日</th>
                    <th class="bg-light col-1">QR</th>
                    <th class="bg-light col-1"></th>
                    <th class="bg-light col-1"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($shops as $shop)
                <tr>
                    <td>{{ $shop->shop_id }}</td>
                    <td>{{ $shop->name }}</td>
                    <td>{{ $shop->created_datetime }}</td>
                    <td>
                        <div class="col-12">
                            <a href="{{ route('shop.download', ['shop_id' => $shop->shop_id]) }}" class="btn btn-sm btn-info w-100">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="col-12">
                            <a href="{{ route('shop.edit', $shop->shop_id) }}" class="btn btn-sm btn-success w-100">編集</a>
                        </div>
                    </td>
                    <td>
                        <div class="col-12">
                            <button type="button" class="btn btn-sm btn-danger w-100 btn-delete" data-route="{{ route('shop.destroy', $shop->shop_id) }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
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
    {{ $shops->appends(request()->all())->links() }}
</div>

@endsection
