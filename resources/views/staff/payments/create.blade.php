@extends('layouts.app')
@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('staff.payments.index') }}">Payments</a></li>
    <li class="breadcrumb-item active">Record New</li>
@endsection

@push('styles')
<style>
    /* ── Form controls ── */
    .form-label { font-size: .8rem; font-weight: 600; color: var(--navy-mid); margin-bottom: .35rem; }
    .form-control, .form-select {
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        font-size: .875rem; transition: border-color .2s, box-shadow .2s, transform .15s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--coral); box-shadow: 0 0 0 3px rgba(217,119,87,.15);
        outline: none; transform: translateY(-1px);
    }
    .form-control[readonly] { background: var(--bg); color: var(--muted); cursor: not-allowed; }
    .input-group-text { background: var(--coral-subtle); border-color: var(--border); color: var(--coral); font-weight: 700; border-radius: var(--radius-sm) 0 0 var(--radius-sm); }

    /* ── Fee hint badge ── */
    .fee-hint-badge {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--sage-light); color: #2D5A3D;
        font-size: .78rem; font-weight: 600;
        padding: .35rem 1rem; border-radius: 20px;
        border: 1px solid rgba(143,175,154,.3);
        animation: badgeAppear .35s cubic-bezier(.34,1.56,.64,1) both;
    }
    @keyframes badgeAppear {
        from { opacity: 0; transform: scale(.85) translateY(-4px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* ── Submit button ── */
    .btn-record {
        background: var(--sage); color: #fff; border: none;
        border-radius: 20px; padding: .6rem 1.75rem;
        font-size: .9rem; font-weight: 700;
        transition: background .2s, transform .15s, box-shadow .2s;
        display: inline-flex; align-items: center; gap: .4rem;
    }
    .btn-record:hover {
        background: #7A9D86; color: #fff;
        transform: translateY(-1px); box-shadow: 0 5px 16px rgba(143,175,154,.4);
    }
    .btn-cancel {
        background: var(--bg); color: var(--muted); border: 1px solid var(--border);
        border-radius: 20px; padding: .6rem 1.25rem;
        font-size: .875rem; font-weight: 500;
        text-decoration: none; transition: background .15s, color .15s;
    }
    .btn-cancel:hover { background: var(--border); color: var(--text); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fieldIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(143,175,154,0); }
        50%       { box-shadow: 0 0 0 8px rgba(143,175,154,.25); }
    }

    /* Card fades up */
    .form-card { opacity: 0; animation: fadeUp .45s ease .1s both; }

    /* Header */
    .card-hdr { opacity: 0; animation: fadeUp .4s ease .25s both; }

    /* Field groups — JS staggers */
    .field-group { opacity: 0; }
    .field-group.visible { animation: fieldIn .38s ease both; }

    /* Submit row */
    .submit-row { opacity: 0; animation: fadeUp .4s ease .85s both; }

    /* Button pulse */
    .btn-record { animation: pulseGlow 2.5s ease 1.3s 2; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

    <div class="card form-card">

        {{-- Header ── --}}
        <div class="card-header card-hdr" style="background:var(--white); padding:1rem 1.25rem; border-bottom:1px solid var(--border);">
            <h6 class="mb-0 fw-bold" style="color:var(--navy); font-size:.95rem;">
                <i class="bi bi-cash-coin me-2" style="color:var(--sage);"></i>Record New Payment
            </h6>
        </div>

        <div class="card-body" style="padding:1.5rem;">
            <form action="{{ route('staff.payments.store') }}" method="POST" id="paymentForm" novalidate>
                @csrf

                <div class="row g-4">

                    {{-- Application ── --}}
                    <div class="col-12 field-group" data-idx="0">
                        <label class="form-label">Adoption Application <span class="text-danger">*</span></label>
                        <select name="adoption_application_id" id="applicationSelect"
                                class="form-select @error('adoption_application_id') is-invalid @enderror" required>
                            <option value="" data-fee="" data-pet="">Select Application</option>
                            @foreach($applications as $app)
                            <option value="{{ $app->id }}"
                                    data-fee="{{ $app->pet?->adoption_fee ?? '' }}"
                                    data-pet="{{ $app->pet?->name ?? 'Deleted Pet' }}"
                                    @selected(old('adoption_application_id') == $app->id)>
                                {{ $app->application_number }} — {{ $app->adopter->name ?? '' }}
                                ({{ $app->pet?->name ?? 'Deleted Pet' }})
                            </option>
                            @endforeach
                        </select>
                        @error('adoption_application_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Auto-fill hint badge ── --}}
                        <div id="feeHint" class="mt-2 d-none">
                            <span class="fee-hint-badge">
                                <i class="bi bi-check-circle-fill"></i>
                                Adoption fee for <strong id="hintPetName"></strong>:
                                <strong>₱<span id="hintFee"></span></strong> — auto-filled
                            </span>
                        </div>
                    </div>

                    {{-- Amount ── --}}
                    <div class="col-md-6 field-group" data-idx="1">
                        <label class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="amount" id="amountInput"
                                   step="0.01" min="0"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}"
                                   placeholder="Auto-filled from application"
                                   readonly required>
                        </div>
                        @error('amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Payment Method ── --}}
                    <div class="col-md-6 field-group" data-idx="2">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="method"
                                class="form-select @error('method') is-invalid @enderror" required>
                            <option value="">Select Method</option>
                            <option value="cash"          @selected(old('method') === 'cash')>Cash</option>
                        </select>
                        @error('method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Payment Type ── --}}
                    <div class="col-md-6 field-group" data-idx="3">
                        <label class="form-label">Payment Type</label>
                        <select name="type" class="form-select">
                            <option value="adoption_fee" @selected(old('type','adoption_fee') === 'adoption_fee')>Adoption Fee</option>
                            <option value="donation"     @selected(old('type') === 'donation')>Donation</option>
                            <option value="medical_fee"  @selected(old('type') === 'medical_fee')>Medical Fee</option>
                            <option value="other"        @selected(old('type') === 'other')>Other</option>
                        </select>
                    </div>

                    {{-- Status ── --}}
                    <div class="col-md-6 field-group" data-idx="4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="completed" @selected(old('status','completed') === 'completed')>Completed</option>
                            <option value="pending"   @selected(old('status') === 'pending')>Pending</option>
                        </select>
                    </div>

                    {{-- External Reference # ── --}}
                    <div class="col-md-6 field-group" data-idx="5">
                        <label class="form-label">
                            External Reference # <span style="color:var(--muted); font-weight:400;">(optional)</span>
                        </label>
                        <input type="text" name="external_reference" class="form-control"
                               value="{{ old('external_reference') }}"
                               placeholder="GCash ref, bank trace #, etc.">
                    </div>

                    {{-- Payment Date ── --}}
                    <div class="col-md-6 field-group" data-idx="6">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="paid_at" class="form-control"
                               value="{{ old('paid_at', now()->format('Y-m-d')) }}">
                    </div>

                    {{-- Notes ── --}}
                    <div class="col-12 field-group" data-idx="7">
                        <label class="form-label">
                            Notes <span style="color:var(--muted); font-weight:400;">(optional)</span>
                        </label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Additional notes about this payment...">{{ old('notes') }}</textarea>
                    </div>

                </div>

                {{-- Submit row ── --}}
                <div class="submit-row d-flex justify-content-end align-items-center gap-2 mt-4 pt-3"
                     style="border-top:1px solid var(--border);">
                    <a href="{{ route('staff.payments.index') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-record">
                        <i class="bi bi-check-lg"></i> Record Payment
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Stagger field groups ──
    document.querySelectorAll('.field-group').forEach(el => {
        const delay = 350 + (parseInt(el.dataset.idx) * 70);
        setTimeout(() => el.classList.add('visible'), delay);
    });

    // ── Auto-fill logic (exact same as original) ──
    const appSelect   = document.getElementById('applicationSelect');
    const amountInput = document.getElementById('amountInput');
    const feeHint     = document.getElementById('feeHint');
    const hintFee     = document.getElementById('hintFee');
    const hintPet     = document.getElementById('hintPetName');

    appSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const fee      = selected.dataset.fee;
        const petName  = selected.dataset.pet;

        if (fee && fee !== '') {
            amountInput.value   = parseFloat(fee).toFixed(2);
            hintFee.textContent = parseFloat(fee).toLocaleString('en-PH', { minimumFractionDigits: 2 });
            hintPet.textContent = petName;
            // Re-trigger badge animation
            feeHint.classList.remove('d-none');
            const badge = feeHint.querySelector('.fee-hint-badge');
            badge.style.animation = 'none';
            void badge.offsetWidth; // reflow
            badge.style.animation = '';
        } else {
            amountInput.value = '';
            feeHint.classList.add('d-none');
        }
    });

    // Re-trigger on page load if there's an old value (after validation error)
    if (appSelect.value) {
        appSelect.dispatchEvent(new Event('change'));
    }

});
</script>
@endpush