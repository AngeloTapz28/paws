@extends('layouts.app')
@section('title', 'Payment — ' . $payment->reference_number)
@section('page-title', 'Payment Detail')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-receipt me-2 text-success"></i>Payment Receipt
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Reference #</div>
                        <div class="fw-bold fs-5">{{ $payment->reference_number }}</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        @php $cls = match($payment->status) {
                            'completed' => 'success', 'pending' => 'warning',
                            'failed' => 'danger', default => 'secondary' }; @endphp
                        <span class="badge bg-{{ $cls }} fs-6 px-3 py-2">{{ ucfirst($payment->status) }}</span>
                    </div>
                    <div class="col-12"><hr class="my-2"></div>
                    <div class="col-md-6">
                        <div class="text-muted small">Payer</div>
                        <div class="fw-semibold">{{ $payment->payer->full_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Payment Method</div>
                        <div class="fw-semibold">{{ ucwords(str_replace('_', ' ', $payment->method)) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Type</div>
                        <div class="fw-semibold">{{ ucwords(str_replace('_', ' ', $payment->type)) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Date</div>
                        <div class="fw-semibold">{{ $payment->created_at->format('F d, Y h:i A') }}</div>
                    </div>
                    @if($payment->external_reference)
                    <div class="col-12">
                        <div class="text-muted small">External Reference</div>
                        <div class="fw-semibold font-monospace">{{ $payment->external_reference }}</div>
                    </div>
                    @endif
                    @if($payment->notes)
                    <div class="col-12">
                        <div class="text-muted small">Notes</div>
                        <div>{{ $payment->notes }}</div>
                    </div>
                    @endif
                    <div class="col-12"><hr></div>
                    <div class="col-12 text-end">
                        <div class="text-muted small">Total Amount</div>
                        <div class="fs-3 fw-bold text-success">₱{{ number_format($payment->amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        {{-- Linked Application --}}
        @if($payment->adoptionApplication)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-link-45deg me-2"></i>Linked Application</h6>
            </div>
            <div class="card-body">
                <div class="fw-semibold">{{ $payment->adoptionApplication->application_number }}</div>
                <div class="text-muted small">Pet: {{ $payment->adoptionApplication->pet->name ?? '—' }}</div>
                <div class="text-muted small">Adopter: {{ $payment->adoptionApplication->adopter->full_name ?? '—' }}</div>
                <a href="{{ route('admin.applications.show', $payment->adoptionApplication) }}"
                   class="btn btn-outline-primary btn-sm mt-2">View Application</a>
            </div>
        </div>
        @endif

        {{-- Transaction Log --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-journal-text me-2 text-muted"></i>Transaction Log</h6>
            </div>
            <div class="card-body p-0">
                @forelse($payment->transactions as $tx)
                <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                    <div class="rounded-circle bg-{{ $tx->status === 'success' ? 'success' : 'danger' }}-subtle
                                d-flex align-items-center justify-content-center"
                         style="width:32px;height:32px;min-width:32px">
                        <i class="bi bi-{{ $tx->status === 'success' ? 'check' : 'x' }}
                                    text-{{ $tx->status === 'success' ? 'success' : 'danger' }} small"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold">{{ ucfirst($tx->type) }}</div>
                        <div class="text-muted small">{{ $tx->created_at->format('M d, Y h:i A') }}</div>
                        @if($tx->gateway_response)
                        <div class="text-muted small font-monospace">{{ $tx->gateway_response }}</div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4 small">No transaction log entries.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection