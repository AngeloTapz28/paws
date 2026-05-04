@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Reports</li>
@endsection

@push('styles')
<style>
    /* ── Filter bar ── */
    .filter-bar {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1rem 1.25rem;
        margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);
    }

    /* ── Summary stat cards ── */
    .report-stat {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.35rem 1.5rem;
        box-shadow: var(--shadow-sm);
        display: flex; align-items: center; gap: 1.1rem;
        transition: transform .2s, box-shadow .2s;
    }
    .report-stat:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .report-stat .rs-icon {
        width: 50px; height: 50px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .report-stat .rs-value  { font-size: 1.85rem; font-weight: 800; line-height: 1; color: var(--navy); }
    .report-stat .rs-label  { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); margin-top: .25rem; }
    .report-stat .rs-sub    { font-size: .73rem; color: var(--muted); margin-top: .2rem; }

    /* ── Chart cards ── */
    .chart-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .chart-card .cc-header {
        background: var(--white); border-bottom: 1px solid var(--border);
        padding: 1rem 1.25rem; display: flex; align-items: center; gap: .5rem;
    }
    .chart-card .cc-header h6 { font-size: .9rem; font-weight: 700; color: var(--navy); margin: 0; }
    .cc-body { padding: 1.25rem; }
    .chart-container { position: relative; height: 300px; }

    /* ── Section pill ── */
    .section-pill {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .65rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .22rem .65rem; border-radius: 20px;
        background: var(--coral-subtle); color: var(--coral);
    }

    /* ── Top breeds ── */
    .breed-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: .7rem 1.25rem; border-bottom: 1px solid var(--border);
        transition: background .12s;
    }
    .breed-row:last-child { border-bottom: none; }
    .breed-row:hover { background: var(--coral-subtle); }
    .breed-rank {
        width: 26px; height: 26px; border-radius: 50%;
        background: var(--coral-light); color: var(--coral);
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 700; flex-shrink: 0;
    }
    .breed-rank.rank-1 { background: var(--gold-light);      color: #7A5A1A; }
    .breed-rank.rank-2 { background: rgba(45,49,71,.08);     color: var(--navy); }
    .breed-rank.rank-3 { background: var(--sage-light);      color: #2D5A3D; }

    /* ── Table ── */
    .table th { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); border-bottom:2px solid var(--border); padding:.75rem 1rem; }
    .table td { padding:.75rem 1rem; font-size:.855rem; border-color:var(--border); color:var(--text); vertical-align:middle; }
    .table tbody tr { transition: background .12s; }
    .table tbody tr:hover td { background: var(--coral-subtle); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown   { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeUp     { from { opacity:0; transform:translateY(16px);  } to { opacity:1; transform:translateY(0); } }
    @keyframes slideLeft  { from { opacity:0; transform:translateX(-20px); } to { opacity:1; transform:translateX(0); } }
    @keyframes slideRight { from { opacity:0; transform:translateX(20px);  } to { opacity:1; transform:translateX(0); } }

    .filter-bar { animation: fadeDown .4s ease both; }

    .report-stat { opacity: 0; }
    .report-stat.animated { animation: fadeUp .45s ease both; }

    .cc-app-bar   { opacity: 0; animation: slideLeft  .45s ease .50s both; }
    .cc-status    { opacity: 0; animation: slideRight .45s ease .60s both; }
    .cc-revenue   { opacity: 0; animation: slideLeft  .45s ease .65s both; }
    .cc-category  { opacity: 0; animation: slideRight .45s ease .75s both; }
    .cc-breeds    { opacity: 0; animation: slideLeft  .45s ease .85s both; }
    .cc-adoptions { opacity: 0; animation: slideRight .45s ease .90s both; }

    .breed-row  { opacity: 0; }
    .breed-row.visible  { animation: fadeUp .35s ease both; }
    .adopt-row  { opacity: 0; }
    .adopt-row.visible  { animation: fadeUp .35s ease both; }
</style>
@endpush

@section('content')

{{-- ── Year Filter ── --}}
<div class="filter-bar d-flex align-items-center gap-3 flex-wrap">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="d-flex align-items-center gap-2">
        <div>
            <label class="form-label mb-1" style="font-size:.73rem; font-weight:600; color:var(--navy);">Year</label>
            <select name="year" class="form-select form-select-sm" style="width:110px;">
                @foreach($years as $y)
                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div style="padding-top:1.4rem;">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
    </form>
    <div style="padding-top:1.4rem;">
        <a href="{{ route('admin.reports.export', ['year' => $year]) }}"
           class="btn btn-sm" style="background:var(--sage-light); color:#2D5A3D; border:1px solid var(--sage);">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="report-stat" data-delay="0">
            <div class="rs-icon" style="background:rgba(45,49,71,.07); color:var(--navy);">
                <i class="bi bi-file-earmark-check-fill"></i>
            </div>
            <div>
                <div class="rs-value" data-count="{{ $totalApps }}">0</div>
                <div class="rs-label">Total Applications</div>
                <div class="rs-sub">{{ $year }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="report-stat" data-delay="100">
            <div class="rs-icon" style="background:var(--sage-light); color:var(--sage);">
                <i class="bi bi-heart-fill"></i>
            </div>
            <div>
                <div class="rs-value" style="color:var(--sage);" data-count="{{ $successfulAdopt }}">0</div>
                <div class="rs-label">Successful Adoptions</div>
                @if($totalApps > 0)
                <div class="rs-sub" style="color:var(--sage);">
                    {{ number_format(($successfulAdopt / $totalApps) * 100, 1) }}% success rate
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="report-stat" data-delay="200">
            <div class="rs-icon" style="background:var(--gold-light); color:#B8892A;">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div>
                <div class="rs-value" style="color:var(--coral);" data-amount="{{ $totalRevenue }}">₱0</div>
                <div class="rs-label">Revenue Collected</div>
                <div class="rs-sub">{{ $year }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="report-stat" data-delay="300">
            <div class="rs-icon" style="background:rgba(111,66,193,.1); color:#6f42c1;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="rs-value" style="color:#6f42c1;" data-count="{{ $newAdopters }}">0</div>
                <div class="rs-label">New Adopters</div>
                <div class="rs-sub">{{ $year }}</div>
            </div>
        </div>
    </div>

</div>

{{-- ── Charts Row 1 ── --}}
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="chart-card cc-app-bar">
            <div class="cc-header">
                <span class="section-pill"><i class="bi bi-bar-chart"></i></span>
                <h6>Monthly Applications — {{ $year }}</h6>
            </div>
            <div class="cc-body">
                <div class="chart-container"><canvas id="appChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card cc-status">
            <div class="cc-header">
                <span class="section-pill" style="background:var(--sage-light);color:#2D5A3D;"><i class="bi bi-pie-chart"></i></span>
                <h6>Application Status</h6>
            </div>
            <div class="cc-body d-flex align-items-center justify-content-center" style="min-height:330px;">
                <div style="max-width:260px;width:100%;"><canvas id="statusChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Charts Row 2 ── --}}
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="chart-card cc-revenue">
            <div class="cc-header">
                <span class="section-pill" style="background:var(--gold-light);color:#7A5A1A;"><i class="bi bi-graph-up"></i></span>
                <h6>Monthly Revenue (₱) — {{ $year }}</h6>
            </div>
            <div class="cc-body">
                <div class="chart-container"><canvas id="revenueChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card cc-category">
            <div class="cc-header">
                <span class="section-pill" style="background:rgba(45,49,71,.07);color:var(--navy);"><i class="bi bi-tags"></i></span>
                <h6>Pets by Category</h6>
            </div>
            <div class="cc-body d-flex align-items-center justify-content-center" style="min-height:330px;">
                <div style="max-width:260px;width:100%;"><canvas id="categoryChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Bottom Tables ── --}}
<div class="row g-3">

    {{-- Top Breeds ── --}}
    <div class="col-lg-4">
        <div class="chart-card cc-breeds">
            <div class="cc-header">
                <span class="section-pill" style="background:var(--gold-light);color:#7A5A1A;"><i class="bi bi-trophy"></i></span>
                <h6>Top Adopted Breeds</h6>
            </div>
            <div id="breedList">
                @forelse($topBreeds as $i => $breed)
                <div class="breed-row" data-index="{{ $i }}">
                    <div class="d-flex align-items-center gap-2">
                        <div class="breed-rank rank-{{ $i + 1 }}">{{ $i + 1 }}</div>
                        <span style="font-weight:600; font-size:.875rem; color:var(--navy);">{{ $breed->name }}</span>
                    </div>
                    <span style="font-size:.72rem; font-weight:700; padding:.28em .75em; border-radius:20px;
                                 background:var(--sage-light); color:#2D5A3D;">
                        {{ $breed->total }} adopted
                    </span>
                </div>
                @empty
                <div class="text-center py-4" style="color:var(--muted); font-size:.83rem;">No data yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Adoptions ── --}}
    <div class="col-lg-8">
        <div class="chart-card cc-adoptions">
            <div class="cc-header">
                <span class="section-pill" style="background:rgba(45,49,71,.07);color:var(--navy);"><i class="bi bi-clock-history"></i></span>
                <h6>Recent Successful Adoptions</h6>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="padding-left:1.25rem;">App #</th>
                            <th>Pet</th>
                            <th>Adopter</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAdoptions as $i => $app)
                        <tr class="adopt-row" data-index="{{ $i }}">
                            <td style="padding-left:1.25rem;">
                                <a href="{{ route('admin.applications.show', $app) }}"
                                   style="font-weight:700; color:var(--coral); text-decoration:none; font-size:.85rem;">
                                    {{ $app->application_number }}
                                </a>
                            </td>
                            <td style="font-weight:600; font-size:.855rem;">{{ $app->pet->name ?? '—' }}</td>
                            <td style="font-size:.855rem;">{{ $app->adopter->full_name ?? $app->adopter->name ?? '—' }}</td>
                            <td style="font-size:.8rem; color:var(--muted);">
                                {{ optional($app->completed_at)->format('M d, Y') ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state py-4">
                                    <span class="empty-icon">📊</span>
                                    <h5>No adoptions found for {{ $year }}</h5>
                                </div>
                            </td>
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
document.addEventListener('DOMContentLoaded', () => {

    // ── 1. Stat cards stagger ──
    document.querySelectorAll('.report-stat').forEach(card => {
        setTimeout(() => card.classList.add('animated'), parseInt(card.dataset.delay ?? 0));
    });

    // ── 2. Integer count-up ──
    function countUpInt(el) {
        const target = parseInt(el.dataset.count);
        if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
        const dur = 900, step = 16, inc = target / (dur / step);
        let cur = 0;
        const t = setInterval(() => {
            cur += inc;
            if (cur >= target) { clearInterval(t); el.textContent = target; }
            else el.textContent = Math.floor(cur);
        }, step);
    }

    // ── 3. Amount count-up ──
    function countUpAmount(el) {
        const target = parseFloat(el.dataset.amount);
        if (isNaN(target) || target === 0) { el.textContent = '₱0.00'; return; }
        const dur = 1000, steps = 50, inc = target / steps;
        let cur = 0;
        const t = setInterval(() => {
            cur += inc;
            if (cur >= target) {
                clearInterval(t);
                el.textContent = '₱' + target.toLocaleString('en-PH', { minimumFractionDigits: 2 });
            } else {
                el.textContent = '₱' + cur.toLocaleString('en-PH', { minimumFractionDigits: 2 });
            }
        }, dur / steps);
    }

    setTimeout(() => {
        document.querySelectorAll('[data-count]').forEach(countUpInt);
        document.querySelectorAll('[data-amount]').forEach(countUpAmount);
    }, 350);

    // ── 4. Breed rows stagger ──
    document.querySelectorAll('.breed-row').forEach(row => {
        setTimeout(() => row.classList.add('visible'), 950 + (parseInt(row.dataset.index) * 100));
    });

    // ── 5. Adoption rows stagger ──
    document.querySelectorAll('.adopt-row').forEach(row => {
        setTimeout(() => row.classList.add('visible'), 1000 + (parseInt(row.dataset.index) * 70));
    });

    // ── 6. Charts ──
    const months  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const CORAL   = '#D97757';
    const SAGE    = '#8FAF9A';
    const GOLD    = '#E6C27A';
    const NAVY    = '#2D3147';
    const PALETTE = [CORAL, SAGE, GOLD, NAVY, '#6f42c1', '#0dcaf0', '#ffc107', '#20c997'];
    const GRID    = '#EDE8E3';
    const anim    = { duration: 800, easing: 'easeOutQuart' };

    new Chart(document.getElementById('appChart'), {
        type: 'bar',
        data: { labels: months, datasets: [{ label: 'Applications', data: @json($applicationChartData),
            backgroundColor: 'rgba(217,119,87,.15)', borderColor: CORAL, borderWidth: 2, borderRadius: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, animation: anim,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: GRID } }, x: { grid: { display: false } } } }
    });

    const statusData   = @json(array_values($statusBreakdown));
    const statusLabels = @json(array_keys($statusBreakdown));
    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: { labels: statusLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
            datasets: [{ data: statusData, backgroundColor: PALETTE, borderWidth: 2, borderColor: '#fff' }] },
        options: { responsive: true, animation: { ...anim, animateRotate: true },
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } } } }
    });

    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: { labels: months, datasets: [{ label: 'Revenue (₱)', data: @json($revenueChartData),
            fill: true, backgroundColor: 'rgba(230,194,122,.15)', borderColor: GOLD,
            tension: 0.4, pointBackgroundColor: GOLD, pointRadius: 4 }] },
        options: { responsive: true, maintainAspectRatio: false, animation: anim,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: GRID } }, x: { grid: { display: false } } } }
    });

    const catData   = @json(array_values($categoryDist));
    const catLabels = @json(array_keys($categoryDist));
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: { labels: catLabels, datasets: [{ data: catData, backgroundColor: PALETTE, borderWidth: 2, borderColor: '#fff' }] },
        options: { responsive: true, animation: { ...anim, animateRotate: true },
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } } } }
    });

});
</script>
@endpush