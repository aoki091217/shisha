@extends('layouts.parent')

@push('css')
@endpush

@section('content')
{{ Breadcrumbs::render('report.index') }}
<div>
    {{-- <form action="{{ route('report.index') }}" method="GET" autocomplete="off" id="form">
        <div id="searchForm">
            <div class="col-12 d-flex justify-content-between align-items-end gap-3">
                <div class="col-4">
                    <label for="startDate" class="form-label">会計日時</label>
                    <div class="d-flex align-items-center gap-2">
                        {{ Form::date(
                            'start_date',
                            request('start_date'),
                            [
                                'id' => 'startDate',
                                'class' => 'form-control'
                            ]
                        ) }}
                        <span>～</span>
                        {{ Form::date(
                            'end_date',
                            request('end_date'),
                            [
                                'class' => 'form-control'
                            ]
                        ) }}
                    </div>
                </div>
                <div class="col-6">
                    <label for="codeName" class="form-label">コード</label>
                    <select name="getHash" id="codeName" class="form-select">
                        <option value=""></option>
                        @foreach ($codes as $code)
                            <option value="{{ $code->getHash() }}" {{ request('getHash') == $code->getHash() ? 'selected' : '' }}>
                                {{ $code->getParameter() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col btn-group">
                    <button type="submit" class="col-6 btn btn-warning">検索</button>
                    <a href="{{ route('report.index') }}" class="col-6 btn btn-secondary">リセット</a>
                </div>
            </div>
        </div>
    </form> --}}
    <div class="d-flex justify-content-around flex-wrap gap-3">
        <div class="card" style="width: 30%">
            <div class="card-header">
                <h3 class="fw-bold mb-0">コードクリック数</h3>
            </div>
            <div class="card-body">
                <canvas id="codeClickChart"></canvas>
            </div>
        </div>
        <div class="card" style="width: 30%">
            <div class="card-header">
                <h3 class="fw-bold mb-0">友達追加数</h3>
            </div>
            <div class="card-body">
                <canvas id="followChart"></canvas>
            </div>
        </div>
        <div class="card" style="width: 30%">
            <div class="card-header">
                <h3 class="fw-bold mb-0">ブロック数</h3>
            </div>
            <div class="card-body">
                <canvas id="followChart"></canvas>
            </div>
        </div>
        <div class="card" style="width: 30%">
            <div class="card-header">
                <h3 class="fw-bold mb-0">チェックイン数</h3>
            </div>
            <div class="card-body">
                <canvas id="followChart"></canvas>
            </div>
        </div>
        {{-- <div class="card" style="width: 30%">
            <div class="card-header">
                <h3 class="fw-bold mb-0">クーポン利用数</h3>
            </div>
            <div class="card-body">
                <canvas id="followChart"></canvas>
            </div>
        </div> --}}
        {{-- <div class="card" style="width: 30%">
            <div class="card-header">
                <h3 class="fw-bold mb-0">来店率</h3>
            </div>
            <div class="card-body">
                <canvas id="followChart"></canvas>
            </div>
        </div> --}}
    </div>
</div>

@push('jquery')
<script>

</script>
{{-- @vite('resources/js/home.js') --}}
@endpush

@endsection
