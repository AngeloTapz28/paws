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
    /* ── Summary stat cards ── */
    .pay-stat {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-sm);
        transition: transform .2s, box-shadow .2s;
        text-align: center;
    }
    .pay-stat:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .pay-stat .ps-amount {
        font-size: 1.6rem; font-weight: 800; line-height: 1.1;
        margin-bottom: .25rem;
    }
    .pay-stat .ps-label {
        font-size: .73rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: .07em; color: var(--muted);
    }

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

    /* ── Payment row ── */
    .pay-row { transition: background .12s; }
    .pay-row:hover td { background: var(--coral-subtle); }

    /* ── Amount ── */
    .amount-val {
        font-weight: 800; color: var(--coral); font-size: .9rem;
    }

    /* ── Type chip ── */
    .type-chip {
        font-size: .67rem; font-weight: 700; padding: .28em .75em;
        border-radius: 20px; display: inline-block; text-transform: capitalize;
        background: var(--coral-subtle); color: var(--coral);
    }

    /* ── Status chip ── */
    .status-chip {
        font-size: .68rem; font-weight: 700; padding: .3em .75em;
        border-radius: 20px; display: inline-block;
    }
    .chip-completed { background: var(--sage-light); color: #2D5A3D; }
    .chip-pending   { background: var(--gold-light); color: #7A5A1A; }
    .chip-failed    { background: #FEF0EE;            color: #8B2516; }
    .chip-refunded  { background: rgba(45,49,71,.07); color: var(--navy); }

    /* ── Action button ── */
    .action-btn {
        width: 30px; height: 30px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); background: var(--white);
        color: var(--muted); text-decoration: none; font-size: .85rem;
        transition: all .15s;
    }
    .action-btn:hover { background: var(--coral-subtle); color: var(--coral); border-color: var(--coral-light); }

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

    /* Stat cards stagger */
    .pay-stat { opacity: 0; }
    .pay-stat.animated { animation: fadeUp .45s ease both; }

    /* Filter bar */
    .filter-bar { animation: fadeDown .4s ease .1s both; opacity: 0; }

    /* Table card */
    .table-card { opacity: 0; animation: fadeUp .45s ease .3s both; }

    /* Table header */
    .table-card thead tr { opacity: 0; animation: fadeDown .35s ease .45s both; }

    /* Rows — JS staggers */
    .pay-row { opacity: 0; }
    .pay-row.visible { animation: slideInRow .38s ease both; }
</style>
@endpush

@section('content')

{{-- ── Summary Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="pay-stat" data-delay="0">
            <div class="ps-amount" style="color:var(--sage);"
                 data-amount="{{ $summary['total_collected'] }}">₱0.00</div>
            <div class="ps-label">Total Collected</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pay-stat" data-delay="100">
            <div class="ps-amount" style="color:var(--navy);"
                 data-amount="{{ $summary['this_month'] }}">₱0.00</div>
            <div class="ps-label">This Month</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pay-stat" data-delay="200">
            <div class="ps-amount" style="color:var(--coral);"
                 data-amount="{{ $summary['today'] ?? 0 }}">₱0.00</div>
            <div class="ps-label">Collected Today</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="pay-stat" data-delay="300">
            <div class="ps-amount" style="color:var(--gold);"
                 data-count="{{ $summary['pending_count'] ?? 0 }}">0</div>
            <div class="ps-label">Pending Payments</div>
        </div>
    </div>
</div>

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
                @foreach(['completed','pending','failed','refunded','cancelled'] as $s)
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
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                    <th style="padding-left:1.25rem;">Reference</th>
                    <th>Payer</th>
                    <th>Pet</th>
                    <th>Type</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Recorded By</th>
                    <th style="text-align:right; padding-right:1.25rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $i => $payment)
                <tr class="pay-row" data-index="{{ $i }}">

                    <td style="padding-left:1.25rem;">
                        <span style="font-weight:700; font-size:.85rem; color:var(--navy); font-family:monospace;">
                            {{ $payment->reference_number ?? '—' }}
                        </span>
                    </td>

                    <td>
                        <div style="font-size:.855rem; font-weight:500; color:var(--text);">
                            {{ $payment->payer->name ?? '—' }}
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
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? $payment->method ?? '—')) }}
                    </td>

                    <td>
                        <span class="amount-val">₱{{ number_format($payment->amount, 2) }}</span>
                    </td>

                    <td>
                        @php
                            $chipMap = [
                                'completed' => 'chip-completed',
                                'pending'   => 'chip-pending',
                                'failed'    => 'chip-failed',
                                'refunded'  => 'chip-refunded',
                                'cancelled' => 'chip-refunded',
                            ];
                        @endphp
                        <span class="status-chip {{ $chipMap[$payment->status] ?? 'chip-pending' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>

                    <td style="font-size:.8rem; color:var(--muted); white-space:nowrap;">
                        {{ $payment->created_at->format('M d, Y') }}
                    </td>

                    <td style="font-size:.8rem; color:var(--muted);">
                        {{ $payment->recordedBy->name ?? $payment->recorder->name ?? '—' }}
                    </td>

                    <td style="text-align:right; padding-right:1.25rem;">
                        <a href="{{ route('admin.payments.show', $payment) }}" class="action-btn" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state py-5">
                            <span class="empty-icon">💳</span>
                            <h5>No Payment Records</h5>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── 1. Stat cards stagger in ──
    document.querySelectorAll('.pay-stat').forEach(card => {
        const delay = parseInt(card.dataset.delay ?? 0);
        setTimeout(() => card.classList.add('animated'), delay);
    });

    // ── 2. Amount count-up ──
    function countUpAmount(el) {
        const target   = parseFloat(el.dataset.amount);
        if (isNaN(target) || target === 0) { el.textContent = '₱0.00'; return; }
        const duration = 900;
        const steps    = 40;
        const inc      = target / steps;
        let cur        = 0;
        const timer    = setInterval(() => {
            cur += inc;
            if (cur >= target) {
                clearInterval(timer);
                el.textContent = '₱' + target.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                el.textContent = '₱' + cur.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }, duration / steps);
    }

    // ── 3. Count-up for pending count ──
    function countUpInt(el) {
        const target = parseInt(el.dataset.count);
        if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
        const duration = 700; const step = 16;
        const inc = target / (duration / step);
        let cur = 0;
        const timer = setInterval(() => {
            cur += inc;
            if (cur >= target) { clearInterval(timer); el.textContent = target; }
            else el.textContent = Math.floor(cur);
        }, step);
    }

    setTimeout(() => {
        document.querySelectorAll('[data-amount]').forEach(countUpAmount);
        document.querySelectorAll('[data-count]').forEach(countUpInt);
    }, 400);

    // ── 4. Stagger payment rows ──
    document.querySelectorAll('.pay-row').forEach(row => {
        const delay = 500 + (parseInt(row.dataset.index) * 65);
        setTimeout(() => row.classList.add('visible'), delay);
    });

});
</script>
@endpush