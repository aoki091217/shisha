@extends('layouts.parent')

@push('css')
@vite('resources/css/home/index.css')
@endpush

@section('content')
<div class="d-flex gap-3">
    <div class="card w-50">
        <div class="card-header">
            <h3 class="fw-bold mb-0">売上実績</h3>
        </div>
        <div class="card-body">
            <h4>売上総額：{{ number_format($sales['year']['this']->sum('amount')) }}</h4>
            <canvas id="salesLineChart"></canvas>
            <div class="table-wrapper mt-3">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="bg-light col-1" scope="col">No</th>
                            <th class="bg-light col-5" scope="col">店舗</th>
                            <th class="bg-light col-5" scope="col">売上</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales['year']['this'] as $sale)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $sale->name }}</td>
                            <td>{{ number_format($sale->amount) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="bg-white"></th>
                            <th class="bg-white">総計</th>
                            <th class="bg-white">{{ number_format($sales['year']['this']->sum('amount')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="card w-50">
        <div class="card-header">
            <h3 class="fw-bold mb-0">来客実績</h3>
        </div>
        <div class="card-body">
            <h4>総来客数：{{ number_format($customers['year']['this']->sum('count')) }}</h4>
            <canvas id="customersLineChart"></canvas>
            <div class="table-wrapper mt-3">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="bg-light col-1" scope="col">No</th>
                            <th class="bg-light col-5" scope="col">店舗</th>
                            <th class="bg-light col-5" scope="col">来客数</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers['year']['this'] as $customer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ number_format($customer->count) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="bg-white"></th>
                            <th class="bg-white">総計</th>
                            <th class="bg-white">{{ number_format($customers['year']['this']->sum('count')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('jquery')
<script>
    window.sales = @json($sales);
    window.customers = @json($customers);
</script>
@vite('resources/js/home.js')
@endpush

@endsection
