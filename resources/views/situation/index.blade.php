@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('situation.index') }}
<div class="text-success text-center fw-bold">{{ session('situation') }}</div>
<form action="{{ route('situation.index') }}" method="GET" autocomplete="off" id="form">
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
                <a href="{{ route('situation.index') }}" class="col-6 btn btn-secondary">リセット</a>
            </div>
            <div class="col">
                <a href="{{ route('situation.create') }}" class="col-12 btn btn-primary">登録</a>
            </div>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="bg-light col-4" scope="col">シチュエーション</th>
                    <th class="bg-light col-4" scope="col">受信イベント</th>
                    <th class="bg-light col-4" scope="col">登録日</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($situations as $situation)
                <tr>
                    <td>
                        <a href="{{ route('situation.show', $situation->id) }}" class="d-block w-100">
                            {{ $situation->name }}
                        </a>
                    </td>
                    <td>{{ $situation->receive_event }}</td>
                    <td>{{ $situation->created_datetime }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>
<div class="mt-3" id="footer">
    {{ $situations->links() }}
</div>

@endsection
