@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@push('styles')
<style>
    .stat-card { border-left: 4px solid; }
    .stat-card.blue   { border-color: #0d6efd; }
    .stat-card.green  { border-color: #198754; }
    .stat-card.orange { border-color: #fd7e14; }
    .stat-card.purple { border-color: #6f42c1; }
    .chart-container { position: relative; height: 300px; }
</style>
@endpush

@section('content')
{{-- Filter Bar --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label fw-semibold small mb-1">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('admin.reports.export', ['year' => $year]) }}"
                   class="btn btn-outline-success btn-sm ms-1">
                    <i class="bi bi-download me-1"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card blue h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-file-earmark-check fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Applications</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalApps) }}</div>
                    <div class="text-muted small">{{ $year }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card green h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="bi bi-heart-fill fs-4 text-success"></i>
                </div>
                <div>
                    <div class="text-muted small">Successful Adoptions</div>
                    <div class="fs-3 fw-bold">{{ number_format($successfulAdopt) }}</div>
                    @if($totalApps > 0)
                    <div class="text-success small">
                        {{ number_format(($successfulAdopt / $totalApps) * 100, 1) }}% success rate
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card orange h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                    <i class="bi bi-currency-dollar fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="text-muted small">Revenue Collected</div>
                    <div class="fs-3 fw-bold">₱{{ number_format($totalRevenue, 2) }}</div>
                    <div class="text-muted small">{{ $year }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card purple h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-purple bg-opacity-10 p-3" style="background-color: #6f42c120!important">
                    <i class="bi bi-people fs-4" style="color:#6f42c1"></i>
                </div>
                <div>
                    <div class="text-muted small">New Adopters</div>
                    <div class="fs-3 fw-bold">{{ number_format($newAdopters) }}</div>
                    <div class="text-muted small">{{ $year }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 1 --}}
<div class="row g-4 mb-4">
    {{-- Applications Trend --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-bar-chart me-2 text-primary"></i>Monthly Applications — {{ $year }}
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="appChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Pie --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-pie-chart me-2 text-success"></i>Application Status
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="max-width:260px; width:100%">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 2 --}}
<div class="row g-4 mb-4">
    {{-- Revenue Line --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-graph-up me-2 text-warning"></i>Monthly Revenue (₱) — {{ $year }}
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Doughnut --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-donut me-2 text-info"></i>Pets by Category
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="max-width:260px; width:100%">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Tables --}}
<div class="row g-4">
    {{-- Top Breeds --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-trophy me-2 text-warning"></i>Top Adopted Breeds
                </h6>
            </div>
            <div class="card-body p-0">
                @forelse($topBreeds as $i => $breed)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary rounded-circle" style="width:28px;height:28px;line-height:16px">
                            {{ $i + 1 }}
                        </span>
                        <span class="fw-medium">{{ $breed->name }}</span>
                    </div>
                    <span class="badge bg-success-subtle text-success">{{ $breed->total }} adopted</span>
                </div>
                @empty
                <div class="text-center text-muted py-4">No data yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Adoptions --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-2 text-info"></i>Recent Successful Adoptions
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>App #</th>
                            <th>Pet</th>
                            <th>Adopter</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAdoptions as $app)
                        <tr>
                            <td>
                                <a href="{{ route('admin.applications.show', $app) }}"
                                   class="text-primary fw-medium text-decoration-none">
                                    {{ $app->application_number }}
                                </a>
                            </td>
                            <td>{{ $app->pet->name ?? '—' }}</td>
                            <td>{{ $app->adopter->full_name ?? '—' }}</td>
                            <td>{{ optional($app->completed_at)->format('M d, Y') ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No adoptions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const COLORS  = ['#0d6efd','#198754','#fd7e14','#dc3545','#6f42c1','#0dcaf0','#ffc107','#20c997'];

// Applications Bar Chart
new Chart(document.getElementById('appChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Applications',
            data: @json($applicationChartData),
            backgroundColor: 'rgba(13,110,253,0.15)',
            borderColor: '#0d6efd',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Status Pie Chart
const statusData = @json(array_values($statusBreakdown));
const statusLabels = @json(array_keys($statusBreakdown));
new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
        labels: statusLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
        datasets: [{ data: statusData, backgroundColor: COLORS, borderWidth: 2 }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// Revenue Line Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Revenue (₱)',
            data: @json($revenueChartData),
            fill: true,
            backgroundColor: 'rgba(253,126,20,0.1)',
            borderColor: '#fd7e14',
            tension: 0.4,
            pointBackgroundColor: '#fd7e14',
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Category Doughnut
const catData   = @json(array_values($categoryDist));
const catLabels = @json(array_keys($categoryDist));
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catLabels,
        datasets: [{ data: catData, backgroundColor: COLORS, borderWidth: 2 }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush