@extends('layouts.app')
@section('title', 'Adoption Application')
@section('page-title', 'Adoption Application')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('adopter.pets.index') }}">Browse Pets</a></li>
    <li class="breadcrumb-item"><a href="{{ route('adopter.pets.show', $pet) }}">{{ $pet->name }}</a></li>
    <li class="breadcrumb-item active">Apply</li>
@endsection

@section('content')
<form action="{{ route('adopter.applications.store') }}" method="POST">
    @csrf
    <input type="hidden" name="pet_id" value="{{ $pet->id }}">

    <div class="row g-3">

        {{-- ── Left: Application Form ── --}}
        <div class="col-lg-8">

            {{-- Personal Info --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>Personal Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="applicant_email"
                                   class="form-control @error('applicant_email') is-invalid @enderror"
                                   value="{{ old('applicant_email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="applicant_phone"
                                   class="form-control @error('applicant_phone') is-invalid @enderror"
                                   value="{{ old('applicant_phone', $user->phone) }}" required>
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
                        </div>
                    </div>
                </div>
            </div>

            {{-- Home & Lifestyle --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-house me-2"></i>Home & Lifestyle</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Type of Housing <span class="text-danger">*</span></label>
                            <select name="housing_type" class="form-select @error('housing_type') is-invalid @enderror" required>
                                <option value="">Select type</option>
                                <option value="house" {{ old('housing_type')=='house'?'selected':'' }}>House (owned)</option>
                                <option value="house_rented" {{ old('housing_type')=='house_rented'?'selected':'' }}>House (rented)</option>
                                <option value="apartment" {{ old('housing_type')=='apartment'?'selected':'' }}>Apartment</option>
                                <option value="condo" {{ old('housing_type')=='condo'?'selected':'' }}>Condo</option>
                                <option value="other" {{ old('housing_type')=='other'?'selected':'' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">When you're away, who cares for the pet?</label>
                            <input type="text" name="working_hours" class="form-control"
                                   value="{{ old('working_hours') }}" placeholder="e.g. Family member, pet sitter">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="has_yard" value="1" id="hasYard"
                                       {{ old('has_yard') ? 'checked' : '' }}>
                                <label class="form-check-label" for="hasYard">I have a yard/garden</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="has_other_pets" value="1"
                                       id="hasOtherPets" {{ old('has_other_pets') ? 'checked' : '' }}>
                                <label class="form-check-label" for="hasOtherPets">I have other pets</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="has_children" value="1"
                                       id="hasChildren" {{ old('has_children') ? 'checked' : '' }}>
                                <label class="form-check-label" for="hasChildren">I have children</label>
                            </div>
                        </div>
                        <div class="col-12" id="otherPetsDetails" style="display:none;">
                            <label class="form-label">Details about your other pets</label>
                            <textarea name="other_pets_details" class="form-control" rows="2"
                                      placeholder="Type, breed, age...">{{ old('other_pets_details') }}</textarea>
                        </div>
                        <div class="col-12" id="childrenAges" style="display:none;">
                            <label class="form-label">Children's ages</label>
                            <input type="text" name="children_ages" class="form-control"
                                   value="{{ old('children_ages') }}" placeholder="e.g. 5, 8, 12">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Motivation --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-heart me-2"></i>About Your Adoption</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">
                                Why do you want to adopt {{ $pet->name }}? <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason_for_adopting" rows="5"
                                      class="form-control @error('reason_for_adopting') is-invalid @enderror"
                                      placeholder="Please share your motivation and how you plan to care for this pet... (min. 50 characters)"
                                      required>{{ old('reason_for_adopting') }}</textarea>
                            @error('reason_for_adopting')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Previous experience with pets</label>
                            <textarea name="experience_with_pets" rows="3" class="form-control"
                                      placeholder="Describe your experience caring for pets in the past...">{{ old('experience_with_pets') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Emergency Contact</label>
                            <input type="text" name="emergency_contact" class="form-control"
                                   value="{{ old('emergency_contact') }}" placeholder="Name and phone number">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="additional_notes" rows="2" class="form-control"
                                      placeholder="Anything else you'd like us to know...">{{ old('additional_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-send me-2"></i>Submit Application
            </button>
            <p class="text-muted text-center mt-2" style="font-size:.8rem;">
                By submitting, you agree to our adoption terms and conditions.
            </p>
        </div>

        {{-- ── Right: Pet Summary ── --}}
        <div class="col-lg-4">
            <div class="card" style="position:sticky;top:calc(var(--paws-topbar-h) + 1rem);">
                <img src="{{ $pet->primary_image_url }}" style="width:100%;height:200px;object-fit:cover;border-radius:12px 12px 0 0;">
                <div class="card-body">
                    <h5 class="fw-bold">{{ $pet->name }}</h5>
                    <p class="text-muted mb-2" style="font-size:.85rem;">
                        {{ $pet->category->icon ?? '' }} {{ $pet->category->name }}
                        @if($pet->breed) · {{ $pet->breed->name }}@endif
                    </p>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-muted" style="font-size:.7rem;">Age</div>
                            <div class="fw-semibold" style="font-size:.85rem;">{{ $pet->age }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted" style="font-size:.7rem;">Gender</div>
                            <div class="fw-semibold" style="font-size:.85rem;">{{ ucfirst($pet->gender) }}</div>
                        </div>
                    </div>
                    <div class="p-2 rounded text-center" style="background:#EFF6FF;">
                        <div class="text-muted" style="font-size:.7rem;">Adoption Fee</div>
                        <div class="fw-bold text-primary fs-5">{{ $pet->adoption_fee_display }}</div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0" style="font-size:.78rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Processing typically takes 3-5 business days. We'll contact you for an interview.
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('hasOtherPets').addEventListener('change', function() {
    document.getElementById('otherPetsDetails').style.display = this.checked ? '' : 'none';
});
document.getElementById('hasChildren').addEventListener('change', function() {
    document.getElementById('childrenAges').style.display = this.checked ? '' : 'none';
});
// Initialize
if (document.getElementById('hasOtherPets').checked)
    document.getElementById('otherPetsDetails').style.display = '';
if (document.getElementById('hasChildren').checked)
    document.getElementById('childrenAges').style.display = '';
</script>
@endpush