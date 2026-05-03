@extends('layouts.app')
@section('title', 'Payments')
@section('page-title', 'Payment Records')

@section('page-actions')
    <a href="{{ route('staff.payments.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Record Payment
    </a>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item active">Payments</li>
@endsection

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#F0FFF4;color:#16A34A;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <div class="stat-value">₱{{ number_format($summary['total_collected'], 2) }}</div>
                <div class="stat-label">Total Collected</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#EFF6FF;color:#2563EB;">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div>
                <div class="stat-value">₱{{ number_format($summary['this_month'], 2) }}</div>
                <div class="stat-label">This Month</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#FFFBEB;color:#D97706;">
                <i class="bi bi-sun"></i>
            </div>
            <div>
                <div class="stat-value">₱{{ number_format($summary['today'], 2) }}</div>
                <div class="stat-label">Collected Today</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#FFF1F2;color:#E11D48;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="stat-value">{{ $summary['pending'] }}</div>
                <div class="stat-label">Pending Payments</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-sm-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Reference number…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-sm-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    @foreach(['pending','completed','failed','refunded'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach(['adoption_fee','donation','microchip_fee','medical_fee','other'] as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>
                            {{ ucwords(str_replace('_',' ',$t)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-auto d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($payments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-cash-coin d-block fs-1 mb-2 opacity-25"></i>
                <p class="mb-0">No payment records found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Reference</th>
                            <th>Payer</th>
                            <th>Pet</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Recorded By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $pay)
                        @php
                            $statusColor = match($pay->status) {
                                'completed' => 'success',
                                'pending'   => 'warning',
                                'failed'    => 'danger',
                                'refunded'  => 'info',
                                default     => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <span class="fw-semibold text-primary font-monospace" style="font-size:.83rem;">
                                    {{ $pay->reference_number }}
                                </span>
                            </td>
                            <td style="font-size:.85rem;">{{ $pay->payer->name ?? '—' }}</td>
                            <td style="font-size:.83rem;">
                                @if($pay->adoptionApplication?->pet)
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $pay->adoptionApplication->pet->primary_image_url }}"
                                             style="width:28px;height:28px;border-radius:6px;object-fit:cover;">
                                        {{ $pay->adoptionApplication->pet->name }}
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border" style="font-size:.72rem;">
                                    {{ ucwords(str_replace('_',' ',$pay->type)) }}
                                </span>
                            </td>
                            <td style="font-size:.82rem;">
                                {{ ucwords(str_replace('_',' ',$pay->method)) }}
                            </td>
                            <td class="fw-bold text-success" style="font-size:.9rem;">
                                ₱{{ number_format($pay->amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst($pay->status) }}</span>
                            </td>
                            <td class="text-muted" style="font-size:.78rem;">
                                {{ $pay->created_at->format('M d, Y') }}
                            </td>
                            <td style="font-size:.78rem;">
                                {{ $pay->recordedBy->name ?? '—' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.payments.show', $pay) }}"
                                   class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="px-3 py-2 border-top">{{ $payments->links() }}</div>
            @endif
        @endif
    </div>
</div>

@endsection