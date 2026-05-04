@extends('layouts.app')

@section('title', 'Payment Records')
@section('page-title', 'Payment Records')

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
    /* ── Filter bar ── */
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

    /* ── Type chip ── */
    .type-chip {
        font-size: .67rem; font-weight: 700; padding: .28em .75em;
        border-radius: 20px; background: var(--coral-subtle); color: var(--coral);
        text-transform: capitalize; display: inline-block;
    }

    /* ── Amount ── */
    .amount-val { font-weight: 800; color: var(--coral); font-size: .9rem; }

    /* ── Status chip ── */
    .status-chip {
        font-size: .68rem; font-weight: 700; padding: .3em .75em;
        border-radius: 20px; display: inline-block;
    }
    .chip-completed { background: var(--sage-light); color: #2D5A3D; }
    .chip-pending   { background: var(--gold-light); color: #7A5A1A; }
    .chip-failed    { background: #FEF0EE;           color: #8B2516; }
    .chip-refunded  { background: rgba(45,49,71,.07);color: var(--navy); }

    /* ── Reference # ── */
    .ref-num { font-weight: 700; font-size: .855rem; color: var(--navy); font-family: monospace; }

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
    .pay-row { transition: background .12s; }
    .pay-row:hover td { background: var(--coral-subtle); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeUp   { from { opacity:0; transform:translateY(16px);  } to { opacity:1; transform:translateY(0); } }
    @keyframes slideInRow { from { opacity:0; transform:translateX(-16px); } to { opacity:1; transform:translateX(0); } }

    .filter-bar  { animation: fadeDown .4s ease both; }
    .table-card  { opacity: 0; animation: fadeUp .45s ease .2s both; }
    .table-card thead tr { opacity: 0; animation: fadeDown .35s ease .35s both; }

    .pay-row { opacity: 0; }
    .pay-row.visible { animation: slideInRow .38s ease both; }
</style>
@endpush

@section('content')

{{-- ── Filter Bar ── --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-sm-5 col-md-4">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Reference number...">
            </div>
        </div>
        <div class="col-sm-3 col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                @foreach(['completed','pending','failed','refunded'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3 col-md-2">
            <select name="type" class="form-select form-select-sm">
                <option value="">All Types</option>
                @foreach(['adoption_fee','donation','other'] as $t)
                    <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('staff.payments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- ── Table Card ── --}}
<div class="card table-card">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
        <div>
            <h6 class="mb-0 fw-bold" style="color:var(--navy);">All Payments</h6>
            <p class="mb-0 mt-1" style="font-size:.75rem; color:var(--muted);">
                {{ $payments->total() }} total {{ Str::plural('record', $payments->total()) }}
            </p>
        </div>
        <i class="bi bi-cash-stack" style="font-size:1.3rem; color:var(--border);"></i>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="padding-left:1.25rem;">Reference #</th>
                    <th>Payer</th>
                    <th>Pet</th>
                    <th>Type</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align:right; padding-right:1.25rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $i => $payment)
                <tr class="pay-row" data-index="{{ $i }}">

                    <td style="padding-left:1.25rem;">
                        <span class="ref-num">{{ $payment->reference_number ?? '—' }}</span>
                    </td>

                    <td>
                        <div style="font-size:.855rem; font-weight:500; color:var(--text);">
                            {{ $payment->payer->name ?? '—' }}
                        </div>
                        <div style="font-size:.73rem; color:var(--muted);">
                            {{ $payment->payer->email ?? '' }}
                        </div>
                    </td>

                    <td style="font-size:.83rem; color:var(--muted);">
                        {{ $payment->adoptionApplication?->pet?->name ?? '—' }}
                    </td>

                    <td>
                        <span class="type-chip">
                            {{ str_replace('_', ' ', $payment->type ?? $payment->payment_type ?? 'adoption fee') }}
                        </span>
                    </td>

                    <td style="font-size:.83rem; color:var(--muted);">
                        {{ ucfirst(str_replace('_',' ', $payment->payment_method ?? $payment->method ?? '—')) }}
                    </td>

                    <td>
                        <span class="amount-val">₱{{ number_format($payment->amount, 2) }}</span>
                    </td>

                    <td>
                        @php $chipMap = ['completed'=>'chip-completed','pending'=>'chip-pending','failed'=>'chip-failed','refunded'=>'chip-refunded','cancelled'=>'chip-refunded']; @endphp
                        <span class="status-chip {{ $chipMap[$payment->status] ?? 'chip-pending' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>

                    <td style="font-size:.8rem; color:var(--muted); white-space:nowrap;">
                        {{ $payment->created_at->format('M d, Y') }}
                    </td>

                    <td style="text-align:right; padding-right:1.25rem;">
                        <a href="{{ route('staff.payments.show', $payment) }}" class="action-btn" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state py-5">
                            <span class="empty-icon">💳</span>
                            <h5>No Payment Records</h5>
                            <p>Record your first payment using the button above.</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pay-row').forEach(row => {
        const delay = 400 + (parseInt(row.dataset.index) * 65);
        setTimeout(() => row.classList.add('visible'), delay);
    });
});
</script>
@endpush