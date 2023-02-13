@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('bland.index') }}
<div class="text-success text-center fw-bold">{{ session('message') }}</div>
<form action="{{ route('bland.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    @method('DELETE')
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col-8">
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
                <a href="{{ route('bland.create') }}" class="col-10 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col-2" scope="col">ID</th>
                    <th class="bg-light col-5" scope="col">ブランド名</th>
                    <th class="bg-light col-3" scope="col">登録日</th>
                    <th class="bg-light col"></th>
                    <th class="bg-light col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($blands as $bland)
                <tr>
                    <td>{{ $bland->bland_id }}</td>
                    <td>{{ $bland->name }}</td>
                    <td>{{ $bland->created_datetime }}</td>
                    <td>
                        <div class="col-12">
                            <a href="{{ route('bland.edit', $bland->bland_id) }}" class="btn btn-sm btn-success w-100">編集</a>
                        </div>
                    </td>
                    <td>
                        <div class="col-12">
                            {{ Form::submit(
                                '削除',
                                [
                                    'class' => 'btn btn-sm btn-danger w-100 btn-delete',
                                    'formaction' => route('bland.destroy', $bland->bland_id)
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
    {{ $blands->links() }}
</div>

@endsection
