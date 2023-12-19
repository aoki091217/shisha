@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('situation.follow.index') }}
<div class="text-success text-center fw-bold">{{ session('situation') }}</div>
<form action="{{ route('situation.follow.index') }}" method="GET" autocomplete="off" id="form">
    @csrf
    <div id="searchForm">
        <div class="col-12 d-flex justify-content-between align-items-end gap-3">
            <div class="col-4">
                <label for="situationName" class="form-label">シチュエーション</label>
                {{ Form::text(
                    'situation[name]',
                    request('situation.name'),
                    [
                        'class' => 'form-control',
                        'id' => 'situationName'
                    ]
                ) }}
            </div>
            <div class="col-4">
                <label for="situationEvent" class="form-label">受信イベント</label>
                <select name="situation[event_type]" id="situationEvent" class="form-select">
                    <option value=""></option>
                    @foreach (config('situation.event_type') as $event)
                        <option value="{{ $loop->iteration }}">{{ $event }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col btn-group">
                <button type="submit" class="col-6 btn btn-warning">検索</button>
                <a href="{{ route('situation.follow.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('situation.follow.create') }}" class="col-12 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col-3" scope="col">シチュエーション</th>
                    <th class="bg-light col-3" scope="col">受信イベント</th>
                    <th class="bg-light col-3" scope="col">店舗</th>
                    <th class="bg-light col-3" scope="col">登録日</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($situations as $situation)
                <tr>
                    <td>
                        <a href="{{ route('situation.follow.show', $situation->id) }}" class="d-block w-100">
                            {{ $situation->name }}
                        </a>
                    </td>
                    <td>{{ $situation->receive_event }}</td>
                    <td class="{{ $situation->shop->trashed() ? 'text-danger text-decoration-line-through' : '' }}">
                        {{ $situation->shop->name }}
                    </td>
                    <td>{{ $situation->created_datetime }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>
<div class="mt-3" id="footer">
    {{ $situations->appends(request()->all())->links() }}
</div>

@endsection
