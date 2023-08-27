@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('member.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('member.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    @method('DELETE')
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            @if (auth()->user()->role_id === 1)
            <div class="col-4">
                <label for="shopName" class="form-label">店舗</label>
                <select name="member[shop_id]" id="shopName" class="form-select">
                    <option value=""></option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->shop_id }}" {{ request('member.shop_id') == $shop->shop_id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-4 @if (auth()->user()->role_id !== 1) col-6 @endif">
                <label for="memberName" class="form-label">スタッフ</label>
                {{ Form::text(
                    'member[name]',
                    request('member.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'memberName'
                    ]
                ) }}
            </div>
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('member.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('member.create') }}" class="col-12 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col-2" scope="col">ID</th>
                    <th class="bg-light col-3" scope="col">所属店舗</th>
                    <th class="bg-light col-3" scope="col">スタッフ</th>
                    <th class="bg-light col-2" scope="col">登録日</th>
                    <th class="bg-light col"></th>
                    <th class="bg-light col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                <tr>
                    <td>{{ $member->id }}</td>
                    <td class="{{ $member->shop->trashed() ? 'text-danger text-decoration-line-through' : '' }}">
                        {{ $member->shop->name }}
                    </td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->created_datetime }}</td>
                    <td>
                        <div class="col-12">
                            <a href="{{ route('member.edit', $member->id) }}" class="btn btn-sm btn-success w-100">編集</a>
                        </div>
                    </td>
                    <td>
                        <div class="col-12">
                            <button type="button" class="btn btn-sm btn-danger w-100 btn-delete" data-route="{{ route('member.destroy', $member->id) }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
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
    {{ $members->links() }}
</div>

@endsection
