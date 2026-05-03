@extends('layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Activity Logs</li>
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

    .log-row td { padding: .8rem 1rem; vertical-align: middle; }
    .log-row:hover td { background: var(--coral-subtle); }

    .log-user-avatar {
        width: 30px; height: 30px; border-radius: 50%;
        background: var(--coral-light); color: var(--coral);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 700; flex-shrink: 0;
    }

    .action-chip {
        font-size: .68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .04em; padding: .28em .75em; border-radius: 20px;
        display: inline-block;
    }
    .chip-created  { background: var(--sage-light);  color: #2D5A3D; }
    .chip-updated  { background: var(--gold-light);  color: #7A5A1A; }
    .chip-deleted  { background: #FEF0EE;            color: #8B2516; }
    .chip-login    { background: var(--coral-subtle); color: var(--coral-dark); }
    .chip-default  { background: rgba(45,49,71,.07); color: var(--navy); }
</style>
@endpush

@section('content')

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4 col-lg-4">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Search action or model…">
            </div>
        </div>
        <div class="col-md-3 col-lg-2">
            <label class="form-label" style="font-size:.73rem;">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm"
                   value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3 col-lg-2">
            <label class="form-label" style="font-size:.73rem;">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm"
                   value="{{ request('date_to') }}">
        </div>
        <div class="col-auto d-flex gap-2" style="padding-top:1.4rem;">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Logs Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
        <div>
            <h6 class="mb-0 fw-bold" style="color:var(--navy);">System Activity</h6>
            <p class="mb-0 mt-1" style="font-size:.75rem; color:var(--muted);">
                A record of all actions performed in the system
            </p>
        </div>
        <i class="bi bi-clock-history" style="font-size:1.3rem; color:var(--border);"></i>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="padding-left:1.25rem;">User</th>
                    <th>Action</th>
                    <th>Model / Target</th>
                    <th>Description</th>
                    <th style="text-align:right; padding-right:1.25rem;">Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="log-row">
                    <td style="padding-left:1.25rem;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="log-user-avatar">
                                {{ strtoupper(substr($log->user->full_name ?? 'S', 0, 1)) }}
                            </div>
                            <span style="font-size:.83rem; font-weight:500; color:var(--navy);">
                                {{ $log->user->full_name ?? 'System' }}
                            </span>
                        </div>
                    </td>
                    <td>
                        @php
                            $action = $log->action ?? '';
                            $chipClass = 'chip-default';
                            if (str_contains($action, 'created')) $chipClass = 'chip-created';
                            elseif (str_contains($action, 'updated')) $chipClass = 'chip-updated';
                            elseif (str_contains($action, 'deleted')) $chipClass = 'chip-deleted';
                            elseif (str_contains($action, 'login'))   $chipClass = 'chip-login';
                        @endphp
                        <span class="action-chip {{ $chipClass }}">{{ $action }}</span>
                    </td>
                    <td style="font-size:.8rem; color:var(--muted);">{{ $log->model_type ?? '—' }}</td>
                    <td style="font-size:.82rem; color:var(--text); max-width:260px;">
                        <span title="{{ $log->description ?? '' }}">
                            {{ Str::limit($log->description ?? '—', 60) }}
                        </span>
                    </td>
                    <td style="text-align:right; padding-right:1.25rem; font-size:.78rem; color:var(--muted); white-space:nowrap;">
                        <div>{{ $log->created_at->format('M d, Y') }}</div>
                        <div style="font-size:.72rem;">{{ $log->created_at->format('h:i A') }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state py-5">
                            <span class="empty-icon">📋</span>
                            <h5>No Activity Logs Yet</h5>
                            <p>System actions will be recorded here.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer d-flex justify-content-end" style="background:var(--white);">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection