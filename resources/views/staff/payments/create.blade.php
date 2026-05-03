@extends('layouts.app')
@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-cash-coin me-2 text-success"></i>Record New Payment
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('staff.payments.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="row g-4">
                        {{-- Application --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adoption Application <span class="text-danger">*</span></label>
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

                            {{-- Auto-fill hint --}}
                            <div id="feeHint" class="mt-2 d-none">
                                <span class="badge rounded-pill px-3 py-2"
                                      style="background:var(--sage-light);color:#2D5A3D;font-size:.8rem;">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Adoption fee for <strong id="hintPetName"></strong>:
                                    <strong>₱<span id="hintFee"></span></strong> — auto-filled
                                </span>
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount (₱) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" name="amount" id="amountInput"
                                    step="0.01" min="0"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    value="{{ old('amount') }}"
                                    placeholder="Auto-filled from application"
                                    readonly required style="background:var(--bg);cursor:not-allowed;">
                            </div>
                            @error('amount')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                            <select name="method" class="form-select @error('method') is-invalid @enderror" required>
                                <option value="">Select Method</option>
                                <option value="cash" @selected(old('method') === 'cash')>Cash</option>
                            </select>
                            @error('method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Payment Type --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Type</label>
                            <select name="type" class="form-select">
                                <option value="adoption_fee" @selected(old('type') === 'adoption_fee')>Adoption Fee</option>
                                <option value="donation" @selected(old('type') === 'donation')>Donation</option>
                                <option value="medical_fee" @selected(old('type') === 'medical_fee')>Medical Fee</option>
                                <option value="other" @selected(old('type') === 'other')>Other</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="completed" @selected(old('status','completed') === 'completed')>Completed</option>
                                <option value="pending" @selected(old('status') === 'pending')>Pending</option>
                            </select>
                        </div>

                        {{-- Reference # --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">External Reference # (optional)</label>
                            <input type="text" name="external_reference" class="form-control"
                                value="{{ old('external_reference') }}"
                                placeholder="GCash ref, bank trace #, etc.">
                        </div>

                        {{-- Payment Date --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Date</label>
                            <input type="date" name="paid_at" class="form-control"
                                value="{{ old('paid_at', now()->format('Y-m-d')) }}">
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes (optional)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="Additional notes about this payment...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('staff.payments.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-lg me-1"></i> Record Payment
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
            feeHint.classList.remove('d-none');
        } else {
            amountInput.value = '';
            feeHint.classList.add('d-none');
        }
    });

    // Re-trigger on page load if there's an old value (after validation error)
    if (appSelect.value) {
        appSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush