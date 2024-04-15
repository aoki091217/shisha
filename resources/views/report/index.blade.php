@extends('layouts.parent')

@push('css')
@endpush

@section('content')
{{ Breadcrumbs::render('report.index') }}
<div>
    <form action="{{ route('report.index') }}" method="GET" autocomplete="off" id="form">
        <div id="searchForm">
            <div class="col-12 d-flex justify-content-between align-items-end gap-3">
                <div class="col-3">
                    <label for="year" class="form-label">年</label>
                    <div class="d-flex align-items-center gap-2">
                        <select name="year" id="year" class="form-select">
                            <option value=""></option>
                            @foreach (range(2023, now()->year + 1) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <label for="codeName" class="form-label">コード</label>
                    <select name="hash" id="codeName" class="form-select">
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
    </form>
    <div class="d-flex justify-content-center flex-wrap gap-3">
        <div class="row w-100">
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="fw-bold mb-0">コードクリック数</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="clickChart"></canvas>
                        <div class="table-wrapper mt-3">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th class="bg-light col-1" scope="col">No</th>
                                        <th class="bg-light col-5" scope="col">店舗</th>
                                        <th class="bg-light col-5" scope="col">クリック数</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($sales['year']['this'] as $sale)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sale->name }}</td>
                                        <td>{{ number_format($sale->amount) }}</td>
                                    </tr>
                                    @endforeach --}}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="bg-white"></th>
                                        <th class="bg-white">総計</th>
                                        <th class="bg-white">{{ number_format(0) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="fw-bold mb-0">友達追加数</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="followChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="fw-bold mb-0">ブロック数</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="blockChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row w-100">
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="fw-bold mb-0">チェックイン数</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="checkinChart"></canvas>
                    </div>
                </div>
            </div>
            {{-- <div class="card">
                <div class="card-header">
                    <h3 class="fw-bold mb-0">クーポン利用数</h3>
                </div>
                <div class="card-body">
                    <canvas id="followChart"></canvas>
                </div>
            </div> --}}
            {{-- <div class="card">
                <div class="card-header">
                    <h3 class="fw-bold mb-0">来店率</h3>
                </div>
                <div class="card-body">
                    <canvas id="followChart"></canvas>
                </div>
            </div> --}}
        </div>

    </div>
</div>

@push('jquery')
<script>

</script>
{{-- @vite('resources/js/home.js') --}}
@endpush

@endsection
