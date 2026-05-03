@extends('layouts.app')

@section('title', $pet->name)
@section('page-title', 'Pet Details')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('staff.pets.index') }}" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <h5 class="mb-0 fw-bold">{{ $pet->name }}</h5>
    <span class="badge bg-{{ $pet->status === 'available' ? 'success' : 'warning' }}-subtle
        text-{{ $pet->status === 'available' ? 'success' : 'warning' }} ms-auto">
        {{ ucfirst($pet->status) }}
    </span>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-4">
                @if($pet->primary_image)
    <img src="{{ Storage::url($pet->primary_image) }}"
     class="rounded-3 w-100 mb-3" style="height:200px;object-fit:cover">
@else
    <div class="rounded-3 bg-light d-flex align-items-center justify-content-center mb-3"
     style="height:200px">
    <i class="bi bi-image text-muted fs-1"></i>
    </div>
@endif
                <h5 class="fw-bold">{{ $pet->name }}</h5>
                <div class="text-muted">{{ $pet->petCategory->name ?? '' }}
                    @if($pet->breed) · {{ $pet->breed->name }} @endif
                </div>
                <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                    @if($pet->is_vaccinated)
                    <span class="badge bg-success-subtle text-success">Vaccinated</span>
                    @endif
                    @if($pet->is_neutered)
                    <span class="badge bg-info-subtle text-info">Neutered</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">Pet Information</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-md-4 text-muted">Name</dt>
                    <dd class="col-md-8">{{ $pet->name }}</dd>
                    <dt class="col-md-4 text-muted">Category</dt>
                    <dd class="col-md-8">{{ $pet->petCategory->name ?? '—' }}</dd>
                    <dt class="col-md-4 text-muted">Breed</dt>
                    <dd class="col-md-8">{{ $pet->breed->name ?? '—' }}</dd>
                    <dt class="col-md-4 text-muted">Gender</dt>
                    <dd class="col-md-8">{{ ucfirst($pet->gender) }}</dd>
                    <dt class="col-md-4 text-muted">Age</dt>
                    <dd class="col-md-8">{{ $pet->age }} {{ $pet->age_unit }}</dd>
                    <dt class="col-md-4 text-muted">Adoption Fee</dt>
                    <dd class="col-md-8">₱{{ number_format($pet->adoption_fee, 2) }}</dd>
                    <dt class="col-md-4 text-muted">Status</dt>
                    <dd class="col-md-8">{{ ucfirst($pet->status) }}</dd>
                    <dt class="col-md-4 text-muted">Description</dt>
                    <dd class="col-md-8">{{ $pet->description ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection