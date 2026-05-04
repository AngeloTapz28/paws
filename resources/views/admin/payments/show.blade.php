@extends('layouts.app')

@section('title', 'Payment — ' . $payment->reference_number)
@section('page-title', 'Payment Detail')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Payments</a></li>
    <li class="breadcrumb-item active">{{ $payment->reference_number }}</li>
@endsection

@push('styles')
<style>
    /* ── Info label/value pairs ── */
    .info-label {
        font-size: .75rem; color: var(--muted); font-weight: 500;
        margin-bottom: .2rem; text-transform: capitalize;
    }
    .info-value { font-size: .9rem; font-weight: 700; color: var(--text); }

    /* ── Divider ── */
    .receipt-divider {
        border: none; border-top: 1px solid var(--border); margin: 1.25rem 0;
    }

    /* ── Amount display ── */
    .amount-display {
        font-size: 2.6rem; font-weight: 800;
        color: var(--sage); letter-spacing: -.02em; line-height: 1;
    }

    /* ── Status badge ── */
    .status-badge {
        font-size: .8rem; font-weight: 700; padding: .45em 1.2em;
        border-radius: 25px; letter-spacing: .04em;
    }
    .s-completed { background: var(--sage);  color: #fff; }
    .s-pending   { background: var(--gold);  color: var(--navy); }
    .s-failed    { background: #C0392B;      color: #fff; }
    .s-refunded  { background: var(--muted); color: #fff; }

    /* ── Transaction log ── */
    .txn-item {
        display: flex; gap: .9rem; align-items: flex-start;
        padding: .75rem; border-radius: 10px;
        background: var(--bg); margin-bottom: .5rem;
        transition: background .15s;
    }
    .txn-item:hover { background: var(--coral-subtle); }
    .txn-item:last-child { margin-bottom: 0; }
    .txn-icon {
        width: 30px; height: 30px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; flex-shrink: 0;
        background: var(--sage-light); color: #2D5A3D;
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-24px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(24px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes badgePop {
        0%   { transform: scale(.5); opacity: 0; }
        70%  { transform: scale(1.12); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes amountReveal {
        from { opacity: 0; transform: translateY(10px) scale(.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes txnSlide {
        from { opacity: 0; transform: translateX(16px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* Breadcrumb/back bar */
    .back-bar { animation: fadeDown .4s ease both; }

    /* Receipt card */
    .card-receipt { opacity: 0; animation: slideInLeft .45s ease .1s both; }

    /* Receipt header row */
    .receipt-header { opacity: 0; animation: fadeDown .4s ease .3s both; }

    /* Status badge pops */
    .status-badge { animation: badgePop .5s cubic-bezier(.34,1.56,.64,1) .35s both; opacity: 0; }

    /* Info grid rows stagger */
    .info-pair { opacity: 0; }
    .info-pair.visible { animation: fadeUp .38s ease both; }

    /* Amount section */
    .amount-section { opacity: 0; animation: amountReveal .5s ease .7s both; }

    /* Transaction log card */
    .card-txn { opacity: 0; animation: slideInRight .45s ease .2s both; }

    /* Transaction items stagger */
    .txn-item { opacity: 0; }
    .txn-item.visible { animation: txnSlide .4s ease both; }
</style>
@endpush

@section('content')

<div class="row g-4">

    {{-- ══ LEFT: Payment Receipt ══ --}}
    <div class="col-lg-8">
        <div class="card card-receipt">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">
                    <i class="bi bi-receipt me-2" style="color:var(--sage);"></i>Payment Receipt
                </h6>
            </div>
            <div class="card-body" style="padding:1.5rem;">

                {{-- Reference # + Status ── --}}
                <div class="receipt-header d-flex align-items-start justify-content-between mb-1">
                    <div>
                        <div class="info-label">Reference #</div>
                        <div style="font-size:1.2rem; font-weight:800; color:var(--navy); font-family:monospace; letter-spacing:.03em;">
                            {{ $payment->reference_number }}
                        </div>
                    </div>
                    @php
                        $sClass = match($payment->status) {
                            'completed' => 's-completed',
                            'pending'   => 's-pending',
                            'failed'    => 's-failed',
                            default     => 's-refunded',
                        };
                    @endphp
                    <span class="status-badge {{ $sClass }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>

                <hr class="receipt-divider">

                {{-- Info grid ── --}}
                <div class="row g-4" id="infoGrid">
                    <div class="col-md-6 info-pair" data-idx="0">
                        <div class="info-label">Payer</div>
                        <div class="info-value">{{ $payment->payer->name ?? '—' }}</div>
                        <div style="font-size:.78rem; color:var(--muted);">{{ $payment->payer->email ?? '' }}</div>
                    </div>
                    <div class="col-md-6 info-pair" data-idx="1">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value">{{ ucwords(str_replace('_', ' ', $payment->payment_method ?? $payment->method ?? '—')) }}</div>
                    </div>
                    <div class="col-md-6 info-pair" data-idx="2">
                        <div class="info-label">Type</div>
                        <div class="info-value">{{ ucwords(str_replace('_', ' ', $payment->type ?? $payment->payment_type ?? '—')) }}</div>
                    </div>
                    <div class="col-md-6 info-pair" data-idx="3">
                        <div class="info-label">Date</div>
                        <div class="info-value">{{ $payment->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                    <div class="col-md-6 info-pair" data-idx="4">
                        <div class="info-label">Recorded By</div>
                        <div class="info-value">
                            {{ $payment->recordedBy->name ?? $payment->recorder->name ?? '—' }}
                        </div>
                    </div>
                    @if($payment->adoptionApplication)
                    <div class="col-md-6 info-pair" data-idx="5">
                        <div class="info-label">Application</div>
                        <div class="info-value">
                            <a href="{{ route('admin.applications.show', $payment->adoptionApplication) }}"
                               style="color:var(--coral); text-decoration:none; font-size:.875rem;">
                                {{ $payment->adoptionApplication->application_number }}
                                <i class="bi bi-arrow-up-right ms-1" style="font-size:.75rem;"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                    @if($payment->notes)
                    <div class="col-12 info-pair" data-idx="6">
                        <div class="info-label">Notes</div>
                        <div class="info-value" style="font-weight:500;">{{ $payment->notes }}</div>
                    </div>
                    @endif
                </div>

                <hr class="receipt-divider">

                {{-- Total Amount ── --}}
                <div class="amount-section text-end">
                    <div class="info-label mb-1" style="font-size:.78rem;">Total Amount</div>
                    <div class="amount-display" id="amountDisplay" data-amount="{{ $payment->amount }}">
                        ₱0.00
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══ RIGHT: Transaction Log ══ --}}
    <div class="col-lg-4">
        <div class="card card-txn">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">
                    <i class="bi bi-list-check me-2" style="color:var(--coral);"></i>Transaction Log
                </h6>
            </div>
            <div class="card-body" style="padding:1rem 1.25rem;" id="txnList">

                {{-- Always show the creation entry ── --}}
                <div class="txn-item">
                    <div class="txn-icon">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div>
                        <div style="font-size:.83rem; font-weight:700; color:var(--navy);">Credit</div>
                        <div style="font-size:.75rem; color:var(--muted);">
                            {{ $payment->created_at->format('M d, Y h:i A') }}
                        </div>
                        <div style="font-size:.75rem; color:var(--muted); margin-top:.15rem;">
                            Payment recorded by staff:
                            {{ $payment->recordedBy->name ?? $payment->recorder->name ?? 'System' }}
                        </div>
                    </div>
                </div>

                {{-- Transactions if available ── --}}
                @if($payment->transactions && $payment->transactions->count())
                    @foreach($payment->transactions as $txn)
                    <div class="txn-item">
                        <div class="txn-icon" style="background:var(--gold-light); color:#7A5A1A;">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div>
                            <div style="font-size:.83rem; font-weight:700; color:var(--navy);">
                                {{ ucfirst($txn->type ?? 'Transaction') }}
                            </div>
                            <div style="font-size:.75rem; color:var(--muted);">
                                {{ $txn->created_at->format('M d, Y h:i A') }}
                            </div>
                            @if($txn->description)
                            <div style="font-size:.75rem; color:var(--muted); margin-top:.15rem;">
                                {{ $txn->description }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif

                {{-- Status update entry ── --}}
                @if($payment->status === 'completed')
                <div class="txn-item">
                    <div class="txn-icon">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div>
                        <div style="font-size:.83rem; font-weight:700; color:var(--navy);">Completed</div>
                        <div style="font-size:.75rem; color:var(--muted);">
                            Payment marked as completed
                        </div>
                    </div>
                </div>
                @elseif($payment->status === 'failed')
                <div class="txn-item" style="background:#FEF0EE;">
                    <div class="txn-icon" style="background:#FEF0EE; color:#C0392B;">
                        <i class="bi bi-x-lg"></i>
                    </div>
                    <div>
                        <div style="font-size:.83rem; font-weight:700; color:#C0392B;">Failed</div>
                        <div style="font-size:.75rem; color:var(--muted);">Payment could not be processed</div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── 1. Info pair stagger ──
    document.querySelectorAll('.info-pair').forEach(pair => {
        const idx   = parseInt(pair.dataset.idx ?? 0);
        const delay = 400 + (idx * 80);
        setTimeout(() => pair.classList.add('visible'), delay);
    });

    // ── 2. Amount count-up ──
    setTimeout(() => {
        const el     = document.getElementById('amountDisplay');
        const target = parseFloat(el.dataset.amount);
        if (!el || isNaN(target)) return;

        const duration = 900;
        const steps    = 45;
        const inc      = target / steps;
        let cur        = 0;

        const timer = setInterval(() => {
            cur += inc;
            if (cur >= target) {
                clearInterval(timer);
                el.textContent = '₱' + target.toLocaleString('en-PH', {
                    minimumFractionDigits: 2, maximumFractionDigits: 2
                });
            } else {
                el.textContent = '₱' + cur.toLocaleString('en-PH', {
                    minimumFractionDigits: 2, maximumFractionDigits: 2
                });
            }
        }, duration / steps);
    }, 750); // starts after amount section animates in

    // ── 3. Transaction log items stagger in ──
    document.querySelectorAll('.txn-item').forEach((item, i) => {
        setTimeout(() => item.classList.add('visible'), 450 + (i * 150));
    });

});
</script>
@endpush