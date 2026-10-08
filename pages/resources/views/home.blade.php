@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

<div class="card p-3 mb-3">
    <form method="GET" class="row">

        <!-- YEAR -->
        <div class="col-md-3">
            <label>Year</label>
            <select name="year" class="form-control">
                @for ($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- MONTH -->
        <div class="col-md-3">
            <label>Month</label>
            <select name="month" class="form-control">
                <option value="">All Months</option>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- SUBMIT -->
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100">Apply Filter</button>
        </div>

        <!-- RESET -->
        <div class="col-md-3 d-flex align-items-end">
            <a href="{{ route('home') }}" class="btn btn-secondary w-100">Reset</a>
        </div>

    </form>
</div>

<div class="row">

    <!-- BLOG BOX -->
    <div class="col-md-6">
        <div class="small-box bg-info shadow">
            <div class="inner">
                <h3>{{ $currentMonthBlogs }}</h3>
                <p>Blogs</p>
            </div>
        </div>
    </div>

    <!-- PAGE BOX -->
    <div class="col-md-6">
        <div class="small-box bg-success shadow">
            <div class="inner">
                <h3>{{ $currentMonthPages }}</h3>
                <p>Pages</p>
            </div>
        </div>
    </div>

</div>

<div class="card shadow">
    <div class="card-header">
        <h3 class="card-title">
            Monthly Analytics ({{ $year }})
            @if($month)
                - {{ \Carbon\Carbon::create()->month($month)->format('F') }}
            @endif
        </h3>
    </div>

    <div class="card-body">
        <canvas id="dashboardChart" height="100"></canvas>
    </div>
</div>

@stop

@section('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('dashboardChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [
                {
                    label: 'Blogs',
                    data: @json($blogData),
                    borderWidth: 2
                },
                {
                    label: 'Pages',
                    data: @json($pageData),
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>

@stop
