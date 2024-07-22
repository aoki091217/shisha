@extends('layouts.parent')

@push('css')
@endpush

@section('content')
{{ Breadcrumbs::render('report.code') }}
<div>
    <form action="{{ route('report.customer') }}" method="GET" autocomplete="off" id="form">
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
                    <div class="my-1">
                        <button type="submit" class="col-6 btn btn-warning">検索</button>
                    </div>
                    <div class="my-1">
                        <a href="{{ route('report.customer') }}" class="col-6 btn btn-secondary">リセット</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="d-flex justify-content-center flex-wrap gap-3">
        <div class="w-100">
            <div>
                <h3 class="fw-bold">顧客分析</h3>
                <div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>日付</th>
                            <th>チェックイン数</th>
                            <th>リピーターチェックイン数</th>
                            <th>新規ユニーク客数</th>
                            <th>リピーターユニーク客数</th>
                            <th>14日以内ユニークリピート数</th>
                            <th>月間ユニーク客数</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $total = ['first_time_visitors' => 0, 'new_unique_customers' => 0, 'repeater_count' => 0, 'repeater_within_14_days' => 0, 'unique_visitors_in_month' => 0]
                        @endphp
                        @foreach($report as $row)
                            @php
                                $total['first_time_visitors'] += $row->first_time_visitors;
                                $total['new_unique_customers'] += $row->new_unique_customers;
                                $total['repeater_count'] += $row->repeater_count;
                                $total['repeater_within_14_days'] += $row->repeater_within_14_days;
                                $total['unique_visitors_in_month'] += $row->unique_visitors_in_month;
                            @endphp
                            <tr>
                                <td>{{ $row->min_date }}~{{ $row->max_date }}</td>
                                <td>{{ $row->first_time_visitors }}</td>
                                <td>{{ $row->first_time_visitors-$row->new_unique_customers }}</td>
                                <td>{{ $row->new_unique_customers }}</td>
                                <td>{{ $row->repeater_count }}</td>
                                <td>{{ $row->repeater_within_14_days }}</td>
                                <td>{{ $row->unique_visitors_in_month }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>合計</th>
                                <th>{{ $total['first_time_visitors'] }}</th>
                                <th>{{ $total['first_time_visitors']-$total['new_unique_customers'] }}</th>
                                <th>{{ $total['new_unique_customers'] }}</th>
                                <th>{{ $total['repeater_count'] }}</th>
                                <th>{{ $total['repeater_within_14_days'] }}</th>
                                <th>{{ $total['unique_visitors_in_month'] }}</th>
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
