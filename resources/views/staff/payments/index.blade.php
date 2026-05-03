@extends('layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Payments</li>
@endsection

@section('page-actions')
    <a href="{{ route('staff.payments.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Record Payment
    </a>
@endsection

@push('styles')
<style>
    .filter-bar {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1rem 1.25rem;
        margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);
    }
    .search-wrap { position: relative; }
    .search-wrap .bi-search {
        position: absolute; left: .75rem; top: 50%;
        transform: translateY(-50%); color: var(--muted); font-size: .85rem; pointer-events: none;
    }
    .search-wrap input { padding-left: 2.1rem; }
    .pay-row td { padding: .82rem 1rem !important; vertical-align: middle; }
    .action-btn {
        width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--border);
        background: var(--white); display: inline-flex; align-items: center;
        justify-content: center; font-size: .85rem; color: var(--muted);
        text-decoration: none; transition: all .15s;
    }
    .action-btn:hover { background: var(--coral-light); color: var(--coral); border-color: transparent; }
    .amount-val { font-weight: 700; color: var(--coral); font-size: .9rem; }
</style>
@endpush

@section('content')

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Search by reference #…">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                @foreach(['pending','completed','failed','refunded'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('staff.payments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
        <div>
            <h6 class="mb-0 fw-bold" style="color:var(--navy);">All Payments</h6>
            <p class="mb-0 mt-1" style="font-size:.75rem; color:var(--muted);">
                {{ $payments->total() }} total {{ Str::plural('record', $payments->total()) }}
            </p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="padding-left:1.25rem;">Reference #</th>
                    <th>Payer</th>
                    <th>Application</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align:right; padding-right:1.25rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr class="pay-row">
                    <td style="padding-left:1.25rem;">
                        <span style="font-weight:700; font-size:.85rem; color:var(--navy);">
                            {{ $payment->reference_number ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <div style="font-size:.855rem; font-weight:500; color:var(--text);">
                            {{ $payment->payer->name ?? '—' }}
                        </div>
                        <div style="font-size:.75rem; color:var(--muted);">
                            {{ $payment->payer->email ?? '' }}
                        </div>
                    </td>
                    <td style="font-size:.83rem; color:var(--muted);">
                        {{ $payment->adoptionApplication->application_number ?? '—' }}
                    </td>
                    <td>
                        <span class="amount-val">₱{{ number_format($payment->amount, 2) }}</span>
                    </td>
                    <td style="font-size:.83rem; color:var(--muted);">
                        {{ ucfirst($payment->payment_method ?? '—') }}
                    </td>
                    <td>
                        @php
                            $pStyles = [
                                'completed' => 'background:var(--sage-light); color:#2D5A3D;',
                                'pending'   => 'background:var(--gold-light); color:#7A5A1A;',
                                'failed'    => 'background:#FEF0EE; color:#8B2516;',
                                'refunded'  => 'background:rgba(45,49,71,.07); color:var(--navy);',
                            ];
                            $ps = $pStyles[$payment->status] ?? 'background:var(--bg);color:var(--muted);';
                        @endphp
                        <span style="font-size:.68rem; font-weight:700; padding:.3em .75em; border-radius:20px; {{ $ps }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td style="font-size:.8rem; color:var(--muted);">
                        {{ $payment->paid_at?->format('M d, Y') ?? '—' }}
                    </td>
                    <td style="text-align:right; padding-right:1.25rem;">
                        <a href="{{ route('staff.payments.show', $payment) }}" class="action-btn" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state py-5">
                            <span class="empty-icon">💳</span>
                            <h5>No Payments Yet</h5>
                            <p>Payment records will appear here once recorded.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="card-footer d-flex justify-content-end" style="background:var(--white);">
        {{ $payments->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection