@extends('layouts.parent')

@push('css')
@endpush

@section('content')
{{ Breadcrumbs::render('report.customer') }}
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
                            $total = [
                                'check_in_count' => 0,
                                'new_unique_customers' => 0,
                                'repeater_unique_customers' => 0,
                                'unique_repeater_within_14_days' => 0,
                                'monthly_unique_visitors' => 0
                            ];
                        @endphp
                        @foreach($report as $row)
                            @php
                                $total['check_in_count'] += $row->check_in_count;
                                $total['new_unique_customers'] += $row->new_unique_customers;
                                $total['repeater_unique_customers'] += $row->repeater_unique_customers;
                                $total['unique_repeater_within_14_days'] += $row->unique_repeater_within_14_days;
                                $total['monthly_unique_visitors'] += $row->monthly_unique_visitors;
                            @endphp
                            <tr>
                                <td>{{ $row->min_date }}~{{ $row->max_date }}</td>
                                <td>{{ $row->check_in_count }}</td>
                                <td>{{ $row->check_in_count - $row->new_unique_customers }}</td>
                                <td>{{ $row->new_unique_customers }}</td>
                                <td>{{ $row->repeater_unique_customers }}</td>
                                <td>{{ $row->unique_repeater_within_14_days }}</td>
                                <td>{{ $row->monthly_unique_visitors }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>合計</th>
                                <th>{{ $total['check_in_count'] }}</th>
                                <th>{{ $total['check_in_count'] - $total['new_unique_customers'] }}</th>
                                <th>{{ $total['new_unique_customers'] }}</th>
                                <th>{{ $total['repeater_unique_customers'] }}</th>
                                <th>{{ $total['unique_repeater_within_14_days'] }}</th>
                                <th>{{ $total['monthly_unique_visitors'] }}</th>
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
