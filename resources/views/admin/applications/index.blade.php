@extends('layouts.app')

@section('title', 'Adoption Applications')
@section('page-title', 'Adoption Applications')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Applications</li>
@endsection

@push('styles')
<style>
    /* ── Filter bar ── */
    .filter-bar {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-sm);
    }
    .search-wrap { position: relative; }
    .search-wrap .bi-search {
        position: absolute; left: .75rem; top: 50%;
        transform: translateY(-50%); color: var(--muted);
        font-size: .85rem; pointer-events: none;
    }
    .search-wrap input { padding-left: 2.1rem; }

    /* ── App number ── */
    .app-number {
        font-weight: 700; color: var(--coral); font-size: .85rem;
        text-decoration: none; transition: color .15s;
    }
    .app-number:hover { color: var(--coral-dark); text-decoration: underline; }

    /* ── Pet thumb ── */
    .pet-thumb {
        width: 36px; height: 36px; border-radius: 8px;
        object-fit: cover; border: 2px solid var(--border);
        flex-shrink: 0; transition: transform .2s, border-color .2s;
    }

    /* ── Action button ── */
    .action-btn {
        width: 30px; height: 30px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); background: var(--white);
        color: var(--muted); text-decoration: none; font-size: .85rem;
        transition: all .15s;
    }
    .action-btn:hover { background: var(--coral-subtle); color: var(--coral); border-color: var(--coral-light); }

    /* ── Row hover ── */
    .app-row { transition: background .12s; }
    .app-row:hover td { background: var(--coral-subtle); }
    .app-row:hover .pet-thumb { transform: scale(1.08); border-color: var(--coral); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInRow {
        from { opacity: 0; transform: translateX(-16px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* Filter bar drops down */
    .filter-bar { animation: fadeDown .4s ease both; }

    /* Table card fades up */
    .table-card { opacity: 0; animation: fadeUp .45s ease .2s both; }

    /* Table header */
    .table-card thead tr { opacity: 0; animation: fadeDown .35s ease .35s both; }

    /* Rows — JS staggers them */
    .app-row { opacity: 0; }
    .app-row.visible { animation: slideInRow .4s ease both; }
</style>
@endpush

@section('content')

{{-- ── Filter Bar ── --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-sm-6 col-md-5">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}"
                       placeholder="Search by application # or applicant...">
            </div>
        </div>
        <div class="col-sm-3 col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                @foreach(['pending','submitted','reviewing','interview','approved','rejected','completed','withdrawn'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- ── Table Card ── --}}
<div class="card table-card">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
        <div>
            <h6 class="mb-0 fw-bold" style="color:var(--navy);">All Applications</h6>
            <p class="mb-0 mt-1" style="font-size:.75rem; color:var(--muted);">
                {{ $applications->total() }} total {{ Str::plural('application', $applications->total()) }}
            </p>
        </div>
        <i class="bi bi-file-earmark-text" style="font-size:1.3rem; color:var(--border);"></i>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="padding-left:1.25rem;">App #</th>
                    <th>Pet</th>
                    <th>Applicant</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th style="text-align:right; padding-right:1.25rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $i => $app)
                <tr class="app-row" data-index="{{ $i }}">

                    {{-- App number ── --}}
                    <td style="padding-left:1.25rem;">
                        <a href="{{ route('admin.applications.show', $app) }}" class="app-number">
                            {{ $app->application_number }}
                        </a>
                    </td>

                    {{-- Pet ── --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $app->pet?->primary_image_url ?? asset('images/no-pet.png') }}"
                                 class="pet-thumb" alt="">
                            <span style="font-weight:600; font-size:.85rem; color:var(--navy);">
                                {{ $app->pet?->name ?? 'Deleted Pet' }}
                            </span>
                        </div>
                    </td>

                    {{-- Applicant ── --}}
                    <td>
                        <div style="font-size:.855rem; font-weight:500; color:var(--text);">
                            {{ $app->applicant_full_name }}
                        </div>
                        <div style="font-size:.75rem; color:var(--muted);">
                            {{ $app->applicant_email ?? $app->adopter?->email }}
                        </div>
                    </td>

                    {{-- Status ── --}}
                    <td>
                        <span class="badge bg-{{ $app->status_badge }}">{{ $app->status_label }}</span>
                    </td>

                    {{-- Submitted ── --}}
                    <td style="font-size:.8rem; color:var(--muted);">
                        {{ $app->submitted_at?->format('M d, Y') ?? $app->created_at->format('M d, Y') }}
                    </td>

                    {{-- Action ── --}}
                    <td style="text-align:right; padding-right:1.25rem;">
                        <a href="{{ route('admin.applications.show', $app) }}" class="action-btn" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state py-5">
                            <span class="empty-icon">📋</span>
                            <h5>No Applications Found</h5>
                            <p>Applications will appear here once adopters submit them.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($applications->hasPages())
    <div class="card-footer d-flex justify-content-end" style="background:var(--white);">
        {{ $applications->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Stagger application rows sliding in from left
    document.querySelectorAll('.app-row').forEach(row => {
        const delay = 400 + (parseInt(row.dataset.index) * 70);
        setTimeout(() => row.classList.add('visible'), delay);
    });
});
</script>
@endpush