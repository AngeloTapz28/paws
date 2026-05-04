@extends('layouts.app')
@section('title', 'Adoption Application')
@section('page-title', 'Adoption Application')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('adopter.pets.index') }}">Browse Pets</a></li>
    <li class="breadcrumb-item"><a href="{{ route('adopter.pets.show', $pet) }}">{{ $pet->name }}</a></li>
    <li class="breadcrumb-item active">Apply</li>
@endsection

@push('styles')
<style>
    /* ── Section card ── */
    .form-section {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); margin-bottom: 1rem;
        box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .section-hdr {
        padding: .9rem 1.25rem; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: .6rem;
        font-size: .88rem; font-weight: 700; color: var(--navy);
    }
    .section-hdr .sh-icon {
        width: 30px; height: 30px; border-radius: 8px;
        background: var(--coral-subtle); color: var(--coral);
        display: flex; align-items: center; justify-content: center; font-size: .85rem;
        flex-shrink: 0;
    }
    .section-body { padding: 1.25rem; }

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
    .form-check-input:checked { background-color: var(--coral); border-color: var(--coral); }

    /* ── Pet sidebar ── */
    .pet-sidebar { position: sticky; top: calc(var(--topbar-h) + 1rem); }
    .pet-sidebar-img {
        width: 100%; height: 200px; object-fit: cover;
        border-radius: var(--radius) var(--radius) 0 0;
        transition: transform .35s ease;
    }
    .pet-sidebar:hover .pet-sidebar-img { transform: scale(1.03); }
    .pet-sidebar-img-wrap { overflow: hidden; border-radius: var(--radius) var(--radius) 0 0; }

    .pet-sb-name { font-size: 1.2rem; font-weight: 800; color: var(--navy); margin-bottom: .2rem; }
    .pet-sb-meta { font-size: .8rem; color: var(--muted); margin-bottom: .85rem; }

    .pet-sb-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; margin-bottom: .85rem; }
    .pet-sb-cell { background: var(--bg); border-radius: var(--radius-sm); padding: .5rem .75rem; }
    .pet-sb-cell .sbc-label { font-size: .65rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }
    .pet-sb-cell .sbc-value { font-size: .83rem; font-weight: 700; color: var(--navy); margin-top: .1rem; }

    .fee-box {
        background: rgba(45,49,71,.05); border-radius: var(--radius-sm);
        padding: .75rem 1rem; text-align: center; margin-bottom: .85rem;
    }
    .fee-box .fb-label { font-size: .72rem; color: var(--muted); font-weight: 500; }
    .fee-box .fb-amount { font-size: 1.5rem; font-weight: 800; color: var(--coral); }

    .info-notice {
        background: var(--coral-subtle); border-radius: var(--radius-sm);
        padding: .65rem .85rem; font-size: .78rem; color: #7A4030;
        display: flex; gap: .5rem; align-items: flex-start;
    }

    /* ── Submit button ── */
    .btn-submit-app {
        background: var(--coral); color: #fff; border: none;
        border-radius: 25px; padding: .75rem 2rem;
        font-size: .95rem; font-weight: 700; width: 100%;
        transition: background .2s, transform .15s, box-shadow .2s;
        display: flex; align-items: center; justify-content: center; gap: .5rem;
    }
    .btn-submit-app:hover {
        background: var(--coral-dark); color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(217,119,87,.4);
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(20px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes imageReveal {
        from { opacity: 0; transform: scale(.95); }
        to   { opacity: 1; transform: scale(1); }
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(217,119,87,0); }
        50%       { box-shadow: 0 0 0 10px rgba(217,119,87,.2); }
    }

    /* Form sections stagger */
    .form-section { opacity: 0; }
    .form-section.visible { animation: slideInLeft .45s ease both; }

    /* Submit section */
    .submit-section { opacity: 0; }
    .submit-section.visible { animation: fadeUp .4s ease both; }

    /* Sidebar */
    .pet-sidebar { opacity: 0; animation: slideInRight .45s ease .15s both; }
    .pet-sidebar-img-wrap { animation: imageReveal .5s ease .2s both; opacity: 0; }

    /* Pet name + info stagger */
    .pet-sb-name    { opacity: 0; animation: fadeUp .4s ease .4s both; }
    .pet-sb-meta    { opacity: 0; animation: fadeUp .4s ease .46s both; }
    .pet-sb-grid    { opacity: 0; animation: fadeUp .4s ease .52s both; }
    .fee-box        { opacity: 0; animation: fadeUp .4s ease .58s both; }
    .info-notice    { opacity: 0; animation: fadeUp .4s ease .64s both; }

    /* Submit button pulse */
    .btn-submit-app { animation: pulseGlow 2.5s ease 2s 2; }
</style>
@endpush

@section('content')

<form action="{{ route('adopter.applications.store') }}" method="POST">
    @csrf
    <input type="hidden" name="pet_id" value="{{ $pet->id }}">

    <div class="row g-3">

        {{-- ══ LEFT: Form ══ --}}
        <div class="col-lg-8">

            {{-- Personal Info ── --}}
            <div class="form-section" data-section="0">
                <div class="section-hdr">
                    <div class="sh-icon"><i class="bi bi-person"></i></div>
                    Personal Information
                </div>
                <div class="section-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="applicant_email"
                                   class="form-control @error('applicant_email') is-invalid @enderror"
                                   value="{{ old('applicant_email', $user->email) }}" required>
                            @error('applicant_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="applicant_phone"
                                   class="form-control @error('applicant_phone') is-invalid @enderror"
                                   value="{{ old('applicant_phone', $user->phone) }}" required>
                            @error('applicant_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control"
                                   value="{{ old('occupation') }}" placeholder="e.g. Teacher, Engineer">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Home Address <span class="text-danger">*</span></label>
                            <textarea name="applicant_address" rows="2"
                                      class="form-control @error('applicant_address') is-invalid @enderror"
                                      required>{{ old('applicant_address', $user->address) }}</textarea>
                            @error('applicant_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Home & Lifestyle ── --}}
            <div class="form-section" data-section="1">
                <div class="section-hdr">
                    <div class="sh-icon"><i class="bi bi-house"></i></div>
                    Home & Lifestyle
                </div>
                <div class="section-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Type of Housing <span class="text-danger">*</span></label>
                            <select name="housing_type" class="form-select @error('housing_type') is-invalid @enderror" required>
                                <option value="">Select type</option>
                                <option value="house"        @selected(old('housing_type') === 'house')>House (owned)</option>
                                <option value="house_rented" @selected(old('housing_type') === 'house_rented')>House (rented)</option>
                                <option value="apartment"    @selected(old('housing_type') === 'apartment')>Apartment</option>
                                <option value="condo"        @selected(old('housing_type') === 'condo')>Condo</option>
                                <option value="other"        @selected(old('housing_type') === 'other')>Other</option>
                            </select>
                            @error('housing_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">When you're away, who cares for the pet?</label>
                            <input type="text" name="working_hours" class="form-control"
                                   value="{{ old('working_hours') }}" placeholder="e.g. Family member, pet sitter">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="has_yard" value="1" id="hasYard"
                                       @checked(old('has_yard'))>
                                <label class="form-check-label" for="hasYard" style="font-size:.855rem;">I have a yard/garden</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="has_other_pets" value="1" id="hasOtherPets"
                                       @checked(old('has_other_pets'))>
                                <label class="form-check-label" for="hasOtherPets" style="font-size:.855rem;">I have other pets</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="has_children" value="1" id="hasChildren"
                                       @checked(old('has_children'))>
                                <label class="form-check-label" for="hasChildren" style="font-size:.855rem;">I have children</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- About Your Adoption ── --}}
            <div class="form-section" data-section="2">
                <div class="section-hdr">
                    <div class="sh-icon"><i class="bi bi-heart"></i></div>
                    About Your Adoption
                </div>
                <div class="section-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Why do you want to adopt {{ $pet->name }}? <span class="text-danger">*</span></label>
                            <textarea name="reason_for_adopting" rows="3"
                                      class="form-control @error('reason_for_adopting') is-invalid @enderror"
                                      placeholder="Share your reasons for adopting..." required>{{ old('reason_for_adopting') }}</textarea>
                            @error('reason_for_adopting')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Your experience with pets</label>
                            <textarea name="experience_with_pets" rows="2"
                                      class="form-control"
                                      placeholder="Tell us about your previous experience with pets...">{{ old('experience_with_pets') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Additional notes <span style="color:var(--muted); font-weight:400;">(optional)</span></label>
                            <textarea name="additional_notes" rows="2"
                                      class="form-control"
                                      placeholder="Anything else you'd like us to know...">{{ old('additional_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit ── --}}
            <div class="submit-section" data-section="3">
                <button type="submit" class="btn-submit-app">
                    <i class="bi bi-send-fill"></i> Submit Application
                </button>
                <p class="text-center mt-2" style="font-size:.75rem; color:var(--muted);">
                    By submitting, you agree to our adoption policies. We'll review your application within 3–5 business days.
                </p>
            </div>

        </div>

        {{-- ══ RIGHT: Pet Sidebar ══ --}}
        <div class="col-lg-4">
            <div class="card pet-sidebar">

                {{-- Image ── --}}
                <div class="pet-sidebar-img-wrap">
                    @if($pet->primary_image)
                        <img src="{{ Storage::url($pet->primary_image) }}" class="pet-sidebar-img" alt="{{ $pet->name }}">
                    @else
                        <div style="height:200px; background:var(--coral-light); display:flex; align-items:center; justify-content:center; font-size:5rem; border-radius:var(--radius) var(--radius) 0 0;">
                            🐾
                        </div>
                    @endif
                </div>

                <div class="card-body" style="padding:1.1rem 1.25rem;">
                    <div class="pet-sb-name">{{ $pet->name }}</div>
                    <div class="pet-sb-meta">
                        {{ $pet->category->icon ?? '🐾' }}
                        {{ $pet->category->name ?? '—' }}
                        @if($pet->breed) · {{ $pet->breed->name }} @endif
                    </div>

                    <div class="pet-sb-grid">
                        <div class="pet-sb-cell">
                            <div class="sbc-label">Age</div>
                            <div class="sbc-value">{{ $pet->age_label ?? ($pet->age ? $pet->age . ' yrs' : '—') }}</div>
                        </div>
                        <div class="pet-sb-cell">
                            <div class="sbc-label">Gender</div>
                            <div class="sbc-value">{{ ucfirst($pet->gender ?? '—') }}</div>
                        </div>
                    </div>

                    <div class="fee-box">
                        <div class="fb-label">Adoption Fee</div>
                        <div class="fb-amount">₱{{ number_format($pet->adoption_fee, 2) }}</div>
                    </div>

                    <div class="info-notice">
                        <i class="bi bi-info-circle-fill mt-1" style="flex-shrink:0;"></i>
                        Processing typically takes 3–5 business days. We'll contact you for an interview.
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Stagger form sections sliding in from left
    document.querySelectorAll('.form-section').forEach(section => {
        const i     = parseInt(section.dataset.section);
        const delay = 100 + (i * 140);
        setTimeout(() => section.classList.add('visible'), delay);
    });

    // Submit section fades up after all sections
    setTimeout(() => {
        document.querySelector('.submit-section')?.classList.add('visible');
    }, 550);
});
</script>
@endpush