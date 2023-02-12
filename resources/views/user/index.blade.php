@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('user.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('user.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    @method('DELETE')
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col-4">
                <label for="userCode" class="form-label">ユーザーID</label>
                {{ Form::text(
                    'user[code]',
                    request('user.code'),
                    [
                        'class' => 'form-control',
                        'id' => 'userCode'
                    ]
                ) }}
            </div>
            <div class="col-4">
                <label for="userName" class="form-label">ユーザー名</label>
                {{ Form::text(
                    'user[name]',
                    request('user.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'userName'
                    ]
                ) }}
            </div>
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('user.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('user.create') }}" class="col-10 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light" scope="col">ユーザID</th>
                    <th class="bg-light" scope="col">ユーザー名</th>
                    <th class="bg-light" scope="col">E-mail</th>
                    <th class="bg-light" scope="col">登録日</th>
                    <th class="bg-light col"></th>
                    <th class="bg-light col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->code }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_datetime }}</td>
                    <td>
                        <div class="col-12">
                            <a href="{{ route('user.edit', $user->code) }}" class="btn btn-sm btn-success w-100">編集</a>
                        </div>
                    </td>
                    <td>
                        <div class="col-12">
                            {{ Form::submit(
                                '削除',
                                [
                                    'class' => 'btn btn-sm btn-danger w-100 btn-delete',
                                    'formaction' => route('user.destroy', $user->code)
                                ]
                            ) }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>
<div class="mt-3" id="footer">
    {{ $users->links() }}
</div>

@endsection
