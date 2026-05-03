@extends('layouts.app')

@section('title', 'Applications')
@section('page-title', 'Adoption Applications')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Applications</li>
@endsection

@push('styles')
<style>
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
        transform: translateY(-50%); color: var(--muted); font-size: .85rem; pointer-events: none;
    }
    .search-wrap input { padding-left: 2.1rem; }

    /* Status tab chips */
    .status-tabs { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .status-tab {
        padding: .35rem .9rem; border-radius: 20px; font-size: .78rem; font-weight: 600;
        text-decoration: none; color: var(--muted); background: var(--white);
        border: 1.5px solid var(--border); transition: all .15s;
    }
    .status-tab:hover { border-color: var(--coral); color: var(--coral); }
    .status-tab.active { background: var(--coral); color: #fff; border-color: var(--coral); }

    .app-row td { vertical-align: middle; padding: .85rem 1rem; }
    .app-number { font-weight: 700; color: var(--coral); font-size: .85rem; }
    .pet-thumb  { width: 36px; height: 36px; border-radius: 9px; object-fit: cover; border: 2px solid var(--border); }
    .action-btn {
        width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--border);
        background: var(--white); display: inline-flex; align-items: center; justify-content: center;
        font-size: .85rem; color: var(--muted); text-decoration: none; transition: all .15s;
    }
    .action-btn:hover { background: var(--coral-light); color: var(--coral); border-color: transparent; }

    /* status badge overrides */
    .badge-status {
        font-size: .68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .04em; padding: .3em .8em; border-radius: 20px; display: inline-block;
    }
</style>
@endpush

@section('content')

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5 col-lg-5">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Search by application # or applicant…">
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                @foreach(['pending','submitted','under_review','interview','approved','rejected','completed','withdrawn'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
        <div>
            <h6 class="mb-0 fw-bold" style="color:var(--navy);">All Applications</h6>
            <p class="mb-0 mt-1" style="font-size:.75rem; color:var(--muted);">
                {{ $applications->total() }} total {{ Str::plural('application', $applications->total()) }}
            </p>
        </div>
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
                @forelse($applications as $app)
                <tr class="app-row">
                    <td style="padding-left:1.25rem;">
                        <span class="app-number">{{ $app->application_number }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $app->pet?->primary_image_url ?? asset('images/no-pet.png') }}" class="pet-thumb" alt="">
                                <span style="font-weight:600; font-size:.85rem; color:var(--navy);">{{ $app->pet?->name ?? 'Deleted Pet' }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.855rem; font-weight:500; color:var(--text);">{{ $app->applicant_full_name }}</div>
                        <div style="font-size:.75rem; color:var(--muted);">{{ $app->applicant_email ?? $app->adopter?->email }}</div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $app->status_badge }}">{{ $app->status_label }}</span>
                    </td>
                    <td style="font-size:.8rem; color:var(--muted);">
                        {{ $app->submitted_at?->format('M d, Y') ?? $app->created_at->format('M d, Y') }}
                    </td>
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