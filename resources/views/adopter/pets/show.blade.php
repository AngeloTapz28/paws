@extends('layouts.app')
@section('title', $pet->name)
@section('page-title', $pet->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('adopter.pets.index') }}">Browse Pets</a></li>
    <li class="breadcrumb-item active">{{ $pet->name }}</li>
@endsection

@section('content')
<div class="row g-4">

    {{-- ── Image Gallery ── --}}
    <div class="col-lg-5">
        <div class="card">
            <img src="{{ $pet->primary_image_url }}" alt="{{ $pet->name }}"
                 style="width:100%;height:360px;object-fit:cover;border-radius:12px 12px 0 0;">
            @if($pet->images && count($pet->images) > 0)
            <div class="card-body pt-2 pb-3">
                <div class="d-flex gap-2 mt-2 flex-wrap">
                    @foreach($pet->images as $img)
                        <img src="{{ asset('storage/'.$img) }}" alt=""
                             style="width:70px;height:70px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid var(--paws-border);">
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Pet Details ── --}}
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="fw-bold mb-0">{{ $pet->name }}</h2>
                        <span class="text-muted">
                            {{ $pet->category->icon ?? '' }} {{ $pet->category->name }}
                            @if($pet->breed) · {{ $pet->breed->name }} @endif
                        </span>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $pet->status_badge }} fs-6 px-3 py-2">
                            {{ ucfirst($pet->status) }}
                        </span>
                    </div>
                </div>

                {{-- Key Details Grid --}}
                <div class="row g-2 mb-3">
                    @foreach([
                        ['icon' => 'bi-gender-ambiguous', 'label' => 'Gender',  'value' => ucfirst($pet->gender)],
                        ['icon' => 'bi-calendar',         'label' => 'Age',     'value' => $pet->age],
                        ['icon' => 'bi-rulers',           'label' => 'Size',    'value' => ucfirst($pet->size ?? 'N/A')],
                        ['icon' => 'bi-palette',          'label' => 'Color',   'value' => $pet->color ?? 'N/A'],
                        ['icon' => 'bi-speedometer2',     'label' => 'Weight',  'value' => $pet->weight ? $pet->weight.'kg' : 'N/A'],
                    ] as $detail)
                    <div class="col-6 col-md-4">
                        <div class="p-2 rounded" style="background:#F8FAFC;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi {{ $detail['icon'] }} text-primary"></i>
                                <div>
                                    <div style="font-size:.7rem;color:var(--paws-muted);">{{ $detail['label'] }}</div>
                                    <div style="font-size:.85rem;font-weight:600;">{{ $detail['value'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Health Badges --}}
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <span class="badge {{ $pet->is_vaccinated ? 'bg-success' : 'bg-secondary' }}">
                        <i class="bi bi-shield-check me-1"></i>
                        {{ $pet->is_vaccinated ? 'Vaccinated' : 'Not Vaccinated' }}
                    </span>
                    <span class="badge {{ $pet->is_neutered ? 'bg-info' : 'bg-secondary' }}">
                        <i class="bi bi-scissors me-1"></i>
                        {{ $pet->is_neutered ? 'Neutered/Spayed' : 'Not Neutered' }}
                    </span>
                    <span class="badge {{ $pet->is_microchipped ? 'bg-primary' : 'bg-secondary' }}">
                        <i class="bi bi-cpu me-1"></i>
                        {{ $pet->is_microchipped ? 'Microchipped' : 'No Microchip' }}
                    </span>
                </div>

                {{-- Description --}}
                @if($pet->description)
                <div class="mb-3">
                    <h6 class="fw-bold">About {{ $pet->name }}</h6>
                    <p class="text-muted mb-0">{{ $pet->description }}</p>
                </div>
                @endif

                @if($pet->special_needs)
                <div class="alert alert-warning mb-3" style="font-size:.875rem;">
                    <i class="bi bi-heart-pulse me-2"></i>
                    <strong>Special Needs:</strong> {{ $pet->special_needs }}
                </div>
                @endif

                {{-- Adoption Fee & CTA --}}
                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                     style="background:#EFF6FF;">
                    <div>
                        <div class="text-muted" style="font-size:.75rem;">Adoption Fee</div>
                        <div class="fw-bold text-primary fs-5">{{ $pet->adoption_fee_display }}</div>
                    </div>
                    @if($pet->status === 'available')
                        @if($existingApplication)
                            <a href="{{ route('adopter.applications.show', $existingApplication) }}"
                               class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark-check me-1"></i>View My Application
                            </a>
                        @else
                            <a href="{{ route('adopter.applications.create', $pet) }}"
                               class="btn btn-primary px-4">
                                <i class="bi bi-heart me-1"></i>Adopt Me!
                            </a>
                        @endif
                    @else
                        <span class="btn btn-secondary disabled">Not Available</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection