@extends('layouts.parent')

@push('css')
@endpush

@section('content')
{{ Breadcrumbs::render('report.index') }}
<div>
    <form action="{{ route('report.index') }}" method="GET" autocomplete="off" id="form">
        <div id="searchForm">
            <div class="col-12 d-flex justify-content-between align-items-end gap-3">
                <div class="col-1">
                    <label for="startYear" class="form-label">期間</label>
                    <div class="d-flex align-items-center gap-2">
                        <select name="start_year" id="startYear" class="form-select">
                            @foreach (range(2023, now()->year + 1) as $year)
                                <option value="{{ $year }}" @if(request()->get('start_year') == $year) selected @endif>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <span class="mb-2">～</span>
                <div class="col-1">
                    <div class="d-flex align-items-center gap-2">
                        <select name="end_year" id="endYear" class="form-select">
                            @foreach (range(2023, now()->year + 1) as $year)
                                <option value="{{ $year }}" @if(request()->get('end_year') == $year) selected @endif>{{ $year }}</option>
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
        <div class="w-100">
            <div>
                <h3 class="fw-bold">コードクリック数</h3>
                <div>
                    @foreach ($clickReport as $year => $reports)
                        <h3 class="fw-bold">{{ $year }}年</h3>
                        <table class="table table-bordered">
                            @foreach ($reports as $report)
                            @php
                                $shop = $report['shop'];
                            @endphp
                            @if ($loop->first)
                            <tr>
                                <th></th>
                                @foreach (collect($report['data'])->keys() as $month)
                                    <th>{{ $month }}月</th>
                                @endforeach
                            </tr>
                            @endif
                                <tr>
                                    <th>{{ $shop->name }}</th>
                                    @foreach ($report['data'] as $turn => $data)
                                        <td>{{ $data }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>
            </div>
            <div class="w-100 border mb-3"></div>
            <div>
                <h3 class="fw-bold">友達追加数</h3>
                <div>
                    @foreach ($followReport as $year => $reports)
                        <h3 class="fw-bold">{{ $year }}年</h3>
                        <table class="table table-bordered">
                            @foreach ($reports as $report)
                            @php
                                $shop = $report['shop'];
                            @endphp
                                @if ($loop->first)
                                <tr>
                                    <th></th>
                                    @foreach (collect($report['data'])->keys() as $month)
                                        <th>{{ $month }}月</th>
                                    @endforeach
                                </tr>
                                @endif
                                    <tr>
                                        <th>{{ $shop->name }}</th>
                                        @foreach ($report['data'] as $turn => $data)
                                            <td>{{ $data }}</td>
                                        @endforeach
                                    </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>
            </div>
            <div class="w-100 border mb-3"></div>
            <div>
                <h3 class="fw-bold">ブロック数</h3>
                <div>
                    @foreach ($blockReport as $year => $reports)
                        <h3 class="fw-bold">{{ $year }}年</h3>
                        <table class="table table-bordered">
                            @foreach ($reports as $report)
                            @php
                                $shop = $report['shop'];
                            @endphp
                                @if ($loop->first)
                                <tr>
                                    <th></th>
                                    @foreach (collect($report['data'])->keys() as $month)
                                        <th>{{ $month }}月</th>
                                    @endforeach
                                </tr>
                                @endif
                                    <tr>
                                        <th>{{ $shop->name }}</th>
                                        @foreach ($report['data'] as $turn => $data)
                                            <td>{{ $data }}</td>
                                        @endforeach
                                    </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>
            </div>
            <div class="w-100 border mb-3"></div>
            <div>
                <h3 class="fw-bold">チェックイン数</h3>
                <div>
                    @foreach ($visitedCountReport as $year => $reports)
                        <h3 class="fw-bold">{{ $year }}年</h3>
                        <table class="table table-bordered">
                            @foreach ($reports as $report)
                            @php
                                $shop = $report['shop'];
                            @endphp
                                @if ($loop->first)
                                <tr>
                                    <th></th>
                                    @foreach (collect($report['data'])->keys() as $month)
                                        <th>{{ $month }}月</th>
                                    @endforeach
                                </tr>
                                @endif
                                <tr>
                                    <th>{{ $shop->name }}</th>
                                    @foreach ($report['data'] as $turn => $data)
                                        <td>{{ $data }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>
            </div>
            <div class="w-100 border mb-3"></div>
            <div>
                <h3 class="fw-bold">来店率（%）</h3>
                <div>
                    @foreach ($visitRateReport as $year => $reports)
                        <h3 class="fw-bold">{{ $year }}年</h3>
                        <table class="table table-bordered">
                            @foreach ($reports as $report)
                            @php
                                $shop = $report['shop'];
                            @endphp
                                @if ($loop->first)
                                <tr>
                                    <th></th>
                                    @foreach (collect($report['data'])->keys() as $month)
                                        <th>{{ $month }}月</th>
                                    @endforeach
                                </tr>
                                @endif
                                    <tr>
                                        <th>{{ $shop->name }}</th>
                                        @foreach ($report['data'] as $turn => $data)
                                            <td>{{ $data }}</td>
                                        @endforeach
                                    </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('jquery')
<script>

</script>
{{-- @vite('resources/js/home.js') --}}
@endpush

@endsection
