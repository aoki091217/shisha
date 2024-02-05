@extends('layouts.parent')

@inject('codeService', 'App\Services\CodeService')

@section('content')
{{ Breadcrumbs::render('code.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('code.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    @method('DELETE')
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col-4">
                <label for="codeName" class="form-label">コード名</label>
                {{ Form::text(
                    'code[name]',
                    request('code.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'codeName'
                    ]
                ) }}
            </div>
            @if (auth()->user()->role_id === 1)
            <div class="col-4">
                <label for="shopName" class="form-label">店舗</label>
                <select name="code[shop_id]" id="shopName" class="form-select">
                    <option value=""></option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->shop_id }}" {{ request('code.shop_id') == $shop->shop_id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('code.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('code.create') }}" class="col-12 btn btn-primary">コード発行</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light" scope="col">コード名</th>
                    <th class="bg-light" scope="col">コード</th>
                    <th class="bg-light" scope="col">種別</th>
                    <th class="bg-light" scope="col">店舗</th>
                    <th class="bg-light" scope="col">送信メッセージ</th>
                    <th class="bg-light" scope="col">登録日</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($codes as $code)
                <tr>
                    <td>
                        <a href="{{ route('code.show', $code->getCodeId()) }}">
                            {{ $code->getName() }}
                        </a>
                    </td>
                    <td>{{ $codeService->getHashedUrl($code->getCodeId()) }}</td>
                    <td>{{ $code->getKind() == 1 ? '流入経路計測' : 'チェックイン' }}</td>
                    <td>{{ $code->shop->name }}</td>
                    <td>{{ $code->situation->name }}</td>
                    <td>{{ $code->getCreatedAt() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>
<div class="mt-3" id="footer">
    {{ $codes->appends(request()->all())->links() }}
</div>

@endsection
