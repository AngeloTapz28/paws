@extends('layouts.app')
@section('title', $pet->name . ' — Health Records')
@section('page-title', 'Pet Health Records')

@section('content')
<div class="row g-4">
    {{-- Pet Summary --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center p-4">
                @if($pet->primary_image)
                <img src="{{ Storage::url($pet->primary_image) }}"
                     class="rounded-3 mb-3 shadow-sm" style="width:100%;max-height:220px;object-fit:cover">
                @else
                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center mb-3"
                     style="height:180px">
                    <i class="bi bi-image text-muted fs-1"></i>
                </div>
                @endif
                <h5 class="fw-bold">{{ $pet->name }}</h5>
                <div class="text-muted mb-2">{{ $pet->category->name ?? '—' }} · {{ $pet->breed->name ?? 'Mixed' }}</div>
                <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                    <span class="badge bg-primary-subtle text-primary">{{ ucfirst($pet->gender) }}</span>
                    <span class="badge bg-info-subtle text-info">{{ $pet->age_label }}</span>
                    @if($pet->is_vaccinated)
                    <span class="badge bg-success-subtle text-success"><i class="bi bi-shield-check me-1"></i>Vaccinated</span>
                    @endif
                    @if($pet->is_neutered)
                    <span class="badge bg-purple-subtle text-purple" style="background-color:#6f42c120;color:#6f42c1">Neutered</span>
                    @endif
                </div>
                <div class="text-muted small text-start">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span>Status</span>
                        <span class="badge bg-{{ $pet->status === 'available' ? 'success' : 'secondary' }}-subtle
                            text-{{ $pet->status === 'available' ? 'success' : 'secondary' }}">
                            {{ ucfirst($pet->status) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span>Weight</span>
                        <span class="text-dark">{{ $pet->weight ? $pet->weight . ' kg' : '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span>Microchip #</span>
                        <span class="text-dark">{{ $pet->microchip_number ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Medical Record --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-2 text-success"></i>Add Record</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('vet.pets.medical-records.create', $pet) }}"
                       class="btn btn-success">
                        <i class="bi bi-clipboard-pulse me-1"></i> Add Medical Record
                    </a>
                    <a href="{{ route('vet.pets.vaccinations.create', $pet) }}"
                       class="btn btn-outline-primary">
                        <i class="bi bi-shield-plus me-1"></i> Add Vaccination
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Health Records Timeline --}}
    <div class="col-lg-8">

        {{-- Vaccination Records --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-shield-check me-2 text-success"></i>Vaccination Records
                </h6>
                <span class="badge bg-success-subtle text-success">{{ $pet->vaccinationRecords->count() }} records</span>
            </div>
            @if($pet->vaccinationRecords->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Vaccine</th>
                            <th>Administered</th>
                            <th>By</th>
                            <th>Next Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pet->vaccinationRecords->sortByDesc('administered_at') as $vacc)
                        <tr>
                            <td class="fw-medium">{{ $vacc->vaccine_name }}</td>
                            <td>{{ $vacc->date_administered ? \Carbon\Carbon::parse($vacc->date_administered)->format('M d, Y') : '—' }}</td>
                            <td>{{ $vacc->administeredBy->name ?? '—' }}</td>
                            <td>
                                @if($vacc->next_due_date)
                                    @if($vacc->next_due_date->isPast())
                                    <span class="text-danger fw-medium">{{ $vacc->next_due_date->format('M d, Y') }}</span>
                                    @else
                                    {{ $vacc->next_due_date->format('M d, Y') }}
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if(!$vacc->next_due_date || $vacc->next_due_date->isFuture())
                                <span class="badge bg-success-subtle text-success">Current</span>
                                @else
                                <span class="badge bg-danger-subtle text-danger">Overdue</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-shield-x fs-2 d-block mb-2 opacity-25"></i>
                No vaccination records yet.
            </div>
            @endif
        </div>

        {{-- Medical Records Timeline --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-clipboard-pulse me-2 text-primary"></i>Medical History
                </h6>
                <span class="badge bg-primary-subtle text-primary">{{ $pet->medicalRecords->count() }} records</span>
            </div>
            <div class="card-body">
                @forelse($pet->medicalRecords->sortByDesc('created_at') as $record)
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-semibold">{{ $record->created_at->format('F d, Y') }}</div>
                            <div class="text-muted small">
                                by {{ $record->vet->name ?? 'Unknown Vet' }}
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-{{ $record->health_status === 'healthy' ? 'success' : ($record->health_status === 'sick' ? 'danger' : 'warning') }}-subtle
                                text-{{ $record->health_status === 'healthy' ? 'success' : ($record->health_status === 'sick' ? 'danger' : 'warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $record->health_status)) }}
                            </span>
                            @if($record->fit_for_adoption)
                            <span class="badge bg-success-subtle text-success">
                                <i class="bi bi-check-circle me-1"></i>Fit for Adoption
                            </span>
                            @else
                            <span class="badge bg-danger-subtle text-danger">
                                <i class="bi bi-x-circle me-1"></i>Not Fit Yet
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($record->diagnosis)
                    <div class="mb-2">
                        <span class="text-muted small fw-semibold">Diagnosis: </span>
                        <span class="small">{{ $record->diagnosis }}</span>
                    </div>
                    @endif
                    @if($record->treatment)
                    <div class="mb-2">
                        <span class="text-muted small fw-semibold">Treatment: </span>
                        <span class="small">{{ $record->treatment }}</span>
                    </div>
                    @endif
                    @if($record->notes)
                    <div class="text-muted small fst-italic">{{ $record->notes }}</div>
                    @endif
                    @if($record->next_checkup_date)
                    <div class="mt-2 pt-2 border-top small">
                        <i class="bi bi-calendar2-check text-info me-1"></i>
                        Next checkup: <strong>{{ $record->next_checkup_date->format('F d, Y') }}</strong>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-clipboard2-x fs-2 d-block mb-2 opacity-25"></i>
                    No medical records yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection