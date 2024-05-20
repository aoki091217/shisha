@extends('layouts.parent')

@push('css')
@endpush

@section('content')
{{ Breadcrumbs::render('report.code') }}
<div>
    <form action="{{ route('report.code') }}" method="GET" autocomplete="off" id="form">
        <div id="searchForm">
            <div class="col-12 d-flex justify-content-between align-items-end gap-3">
                <div class="col-3">
                    <label for="startDate" class="form-label">期間</label>
                    <div>
                        <input type="date" name="startDate" id="startDate" class="form-control" value="{{ request()->get('startDate') }}" max="{{now()->format('Y-m-d')}}">
                        <div class="text-center">〜</div>
                        <input type="date" name="endDate" id="endDate" class="form-control" value="{{ request()->get('endDate') }}" max="{{now()->format('Y-m-d')}}">
                    </div>
                </div>
                <div class="col-3">
                    <label for="shop" class="form-label">店舗</label>
                    <select name="shopIds[]" id="shop" class="form-select" multiple>
                        <option value=""></option>
                        @foreach ($shops as $shop)
                            <option value="{{ $shop->shop_id }}" {{ in_array($shop->shop_id, (array)request('shopIds', [])) ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <label for="codeName" class="form-label">コード</label>
                    <select name="codeIds[]" id="codeName" class="form-select" multiple>
                        <option value=""></option>
                        @foreach ($codes as $code)
                            <option value="{{ $code->code_id }}" {{ in_array($code->code_id, (array)request('codeIds', [])) ? 'selected' : '' }}>
                                {{$shops[$code->shop_id]?->name}}/{{ $code->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <div class="my-1">
                        <button type="submit" class="col-6 btn btn-warning">検索</button>
                    </div>
                    <div class="my-1">
                        <a href="{{ route('report.code') }}" class="col-6 btn btn-secondary">リセット</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="d-flex justify-content-center flex-wrap gap-3">
        <div class="w-100">
            <div>
                <h3 class="fw-bold">コードクリック数</h3>
                <div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>日付</th>
                            <th>クリック数</th>
                            <th>友だち登録数</th>
                            <th>来店数</th>
                            <th>来店率</th>
                            <th>ブロック数</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $total = ['click_count' => 0, 'follow_count' => 0, 'visit_count' => 0, 'block_count' => 0]
                        @endphp
                        @foreach($report as $row)
                            @php
                                $total['click_count'] += $row->click_count;
                                $total['follow_count'] += $row->follow_count;
                                $total['visit_count'] += $row->visit_count;
                                $total['block_count'] += $row->block_count;
                            @endphp
                            <tr>
                                <td>{{ $row->date }}</td>
                                <td>{{ $row->click_count }}</td>
                                <td>{{ $row->follow_count }}</td>
                                <td>{{ $row->visit_count }}</td>
                                <td>{{ round($row->visited_rate * 100, 1) }}%</td>
                                <td>{{ $row->block_count }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>合計</th>
                                <th>{{ $total['click_count'] }}</th>
                                <th>{{ $total['follow_count'] }}</th>
                                <th>{{ $total['visit_count'] }}</th>
                                <th>{{ $total['follow_count'] ? round($total['visit_count'] / $total['follow_count'] * 100, 1) : 0 }}%</th>
                                <th>{{ $total['block_count'] }}</th>
                            </tr>
                        </tfoot>
                    </table>
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
