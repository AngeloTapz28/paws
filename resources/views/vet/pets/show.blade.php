@extends('layouts.app')
@section('title', $pet->name . ' — Health Records')
@section('page-title', 'Pet Health Records')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('vet.pets.index') }}">Pet Health Records</a></li>
    <li class="breadcrumb-item active">{{ $pet->name }}</li>
@endsection

@push('styles')
<style>
    /* ── Pet photo ── */
    .pet-photo {
        width: 100%; max-height: 220px; object-fit: cover;
        border-radius: var(--radius-sm); display: block;
        transition: transform .35s ease; cursor: zoom-in;
    }
    .pet-photo:hover { transform: scale(1.03); }
    .pet-photo-wrap { overflow: hidden; border-radius: var(--radius-sm); }

    /* ── Info rows in summary ── */
    .info-row-simple {
        display: flex; justify-content: space-between; align-items: center;
        padding: .5rem 0; border-bottom: 1px solid var(--border);
        font-size: .855rem;
    }
    .info-row-simple:last-child { border-bottom: none; }
    .info-row-simple .ir-label { color: var(--muted); font-weight: 500; }
    .info-row-simple .ir-value { font-weight: 600; color: var(--text); }

    /* ── Health badges ── */
    .health-badge {
        font-size: .68rem; font-weight: 600; padding: .25em .65em;
        border-radius: 20px; display: inline-flex; align-items: center; gap: .25rem;
    }
    .hb-male      { background: rgba(13,110,253,.1); color: #0d6efd; }
    .hb-female    { background: var(--coral-subtle); color: var(--coral); }
    .hb-vaccinated{ background: var(--sage-light); color: #2D5A3D; }

    /* ── Action buttons (add record) ── */
    .btn-add-medical {
        display: flex; align-items: center; justify-content: center; gap: .45rem;
        width: 100%; padding: .65rem; border-radius: var(--radius-sm);
        background: var(--sage); color: #fff; border: none;
        font-size: .875rem; font-weight: 600; text-decoration: none;
        transition: background .15s, transform .12s; margin-bottom: .5rem;
    }
    .btn-add-medical:hover { background: #7A9D86; color: #fff; transform: translateY(-1px); }

    .btn-add-vacc {
        display: flex; align-items: center; justify-content: center; gap: .45rem;
        width: 100%; padding: .6rem; border-radius: var(--radius-sm);
        background: var(--white); color: var(--coral); border: 1.5px solid var(--coral);
        font-size: .875rem; font-weight: 600; text-decoration: none;
        transition: all .15s;
    }
    .btn-add-vacc:hover { background: var(--coral); color: #fff; }

    /* ── Section header ── */
    .section-hdr {
        display: flex; align-items: center; justify-content: space-between;
        padding: .9rem 1.25rem; border-bottom: 1px solid var(--border);
    }
    .section-hdr h6 { font-size: .9rem; font-weight: 700; color: var(--navy); margin: 0; display: flex; align-items: center; gap: .5rem; }

    /* ── Vaccination table rows ── */
    .vacc-row { transition: background .12s; }
    .vacc-row:hover td { background: var(--coral-subtle); }
    .vacc-row { opacity: 0; }
    .vacc-row.visible { animation: rowIn .38s ease both; }

    /* ── Medical record card ── */
    .med-card {
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        padding: 1rem 1.1rem; margin-bottom: .75rem;
        transition: box-shadow .2s, transform .15s;
        opacity: 0;
    }
    .med-card:last-child { margin-bottom: 0; }
    .med-card:hover { box-shadow: var(--shadow-sm); transform: translateY(-1px); }
    .med-card.visible { animation: cardIn .42s ease both; }

    /* ── Status/fitness badges ── */
    .hb { font-size:.7rem; font-weight:700; padding:.28em .75em; border-radius:20px; display:inline-flex; align-items:center; gap:.3rem; }
    .hb-excellent  { background:var(--sage-light);  color:#2D5A3D; }
    .hb-good       { background:rgba(45,49,71,.08); color:var(--navy); }
    .hb-fair       { background:var(--gold-light);  color:#7A5A1A; }
    .hb-poor,.hb-sick { background:#FEF0EE;          color:#8B2516; }
    .hb-fit        { background:var(--sage-light);  color:#2D5A3D; }
    .hb-unfit      { background:#FEF0EE;             color:#8B2516; }
    .hb-current    { background:var(--sage-light);  color:#2D5A3D; }
    .hb-overdue    { background:#FEF0EE;             color:#8B2516; }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes slideLeft  { from { opacity:0; transform:translateX(-22px); } to { opacity:1; transform:translateX(0); } }
    @keyframes slideRight { from { opacity:0; transform:translateX(22px);  } to { opacity:1; transform:translateX(0); } }
    @keyframes fadeUp     { from { opacity:0; transform:translateY(14px);  } to { opacity:1; transform:translateY(0); } }
    @keyframes rowIn      { from { opacity:0; transform:translateX(-12px); } to { opacity:1; transform:translateX(0); } }
    @keyframes cardIn     { from { opacity:0; transform:translateY(12px) scale(.98); } to { opacity:1; transform:translateY(0) scale(1); } }
    @keyframes imageReveal{ from { opacity:0; transform:scale(.95); } to { opacity:1; transform:scale(1); } }

    /* Left column */
    .col-left  { opacity:0; animation: slideLeft  .45s ease .1s  both; }
    .col-add   { opacity:0; animation: slideLeft  .45s ease .25s both; }

    /* Right column */
    .card-vacc { opacity:0; animation: slideRight .45s ease .2s  both; }
    .card-med  { opacity:0; animation: slideRight .45s ease .35s both; }

    /* Photo */
    .pet-photo-wrap { animation: imageReveal .5s ease .2s both; opacity:0; animation-fill-mode: forwards; }

    /* Pet info rows stagger */
    .info-row-simple { opacity:0; }
    .info-row-simple.visible { animation: fadeUp .35s ease both; }
</style>
@endpush

@section('content')

<div class="row g-4">

    {{-- ══ LEFT COLUMN ══ --}}
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Pet summary ── --}}
        <div class="card col-left">
            <div class="card-body p-4 text-center">
                <div class="pet-photo-wrap mb-3">
                    @if($pet->primary_image)
                        <img src="{{ Storage::url($pet->primary_image) }}" class="pet-photo" alt="{{ $pet->name }}">
                    @else
                        <div style="height:180px; background:var(--coral-light); border-radius:var(--radius-sm);
                                    display:flex; align-items:center; justify-content:center; font-size:4rem;">🐾</div>
                    @endif
                </div>

                <h5 class="fw-bold mb-1" style="color:var(--navy);">{{ $pet->name }}</h5>
                <div style="font-size:.83rem; color:var(--muted); margin-bottom:.75rem;">
                    {{ $pet->petCategory->name ?? '—' }}
                    @if($pet->breed) · {{ $pet->breed->name }} @endif
                </div>

                <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                    @if($pet->gender)
                    <span class="health-badge {{ $pet->gender === 'male' ? 'hb-male' : 'hb-female' }}">
                        {{ ucfirst($pet->gender) }}
                    </span>
                    @endif
                    @if($pet->is_vaccinated)
                    <span class="health-badge hb-vaccinated">
                        <i class="bi bi-patch-check-fill" style="font-size:.6rem;"></i> Vaccinated
                    </span>
                    @endif
                </div>

                <div id="infoRows">
                    <div class="info-row-simple" data-row="0">
                        <span class="ir-label">Status</span>
                        @php $statusColors = ['available'=>'hb-vaccinated','adopted'=>'hb-fair','pending'=>'hb-fair','under_treatment'=>'hb-unfit']; @endphp
                        <span class="hb {{ $statusColors[$pet->status] ?? 'hb-good' }}">{{ ucfirst($pet->status) }}</span>
                    </div>
                    <div class="info-row-simple" data-row="1">
                        <span class="ir-label">Weight</span>
                        <span class="ir-value">{{ $pet->weight ? $pet->weight . ' kg' : '—' }}</span>
                    </div>
                    <div class="info-row-simple" data-row="2">
                        <span class="ir-label">Microchip #</span>
                        <span class="ir-value" style="font-family:monospace; font-size:.83rem;">{{ $pet->microchip_number ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Record ── --}}
        <div class="card col-add">
            <div class="card-header" style="padding:.85rem 1.25rem;">
                <h6 class="mb-0 fw-bold" style="color:var(--navy); font-size:.87rem;">
                    <i class="bi bi-plus-circle me-2" style="color:var(--sage);"></i>Add Record
                </h6>
            </div>
            <div class="card-body" style="padding:1rem 1.25rem;">
                <a href="{{ route('vet.pets.medical-records.create', $pet) }}" class="btn-add-medical">
                    <i class="bi bi-clipboard-pulse"></i> Add Medical Record
                </a>
                <a href="{{ route('vet.pets.vaccinations.create', $pet) }}" class="btn-add-vacc">
                    <i class="bi bi-shield-plus"></i> Add Vaccination
                </a>
            </div>
        </div>

    </div>

    {{-- ══ RIGHT COLUMN ══ --}}
    <div class="col-lg-8 d-flex flex-column gap-3">

        {{-- Vaccination Records ── --}}
        <div class="card card-vacc">
            <div class="section-hdr">
                <h6>
                    <i class="bi bi-shield-check" style="color:var(--sage);"></i>
                    Vaccination Records
                </h6>
                <span style="font-size:.72rem; font-weight:700; padding:.28em .75em; border-radius:20px;
                             background:var(--sage-light); color:#2D5A3D;">
                    {{ $pet->vaccinationRecords->count() }} records
                </span>
            </div>
            @if($pet->vaccinationRecords->count())
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="padding-left:1.25rem;">Vaccine</th>
                            <th>Administered</th>
                            <th>By</th>
                            <th>Next Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pet->vaccinationRecords->sortByDesc('administered_at') as $i => $vacc)
                        <tr class="vacc-row" data-index="{{ $i }}">
                            <td style="padding-left:1.25rem; font-weight:600; color:var(--navy);">
                                {{ $vacc->vaccine_name }}
                            </td>
                            <td style="font-size:.83rem;">
                                {{ $vacc->date_administered
                                    ? \Carbon\Carbon::parse($vacc->date_administered)->format('M d, Y')
                                    : ($vacc->administered_at?->format('M d, Y') ?? '—') }}
                            </td>
                            <td style="font-size:.83rem; color:var(--muted);">
                                {{ $vacc->administeredBy->name ?? '—' }}
                            </td>
                            <td style="font-size:.83rem;">
                                @if($vacc->next_due_date)
                                    @if(\Carbon\Carbon::parse($vacc->next_due_date)->isPast())
                                        <span style="color:#C0392B; font-weight:600;">
                                            {{ \Carbon\Carbon::parse($vacc->next_due_date)->format('M d, Y') }}
                                        </span>
                                    @else
                                        {{ \Carbon\Carbon::parse($vacc->next_due_date)->format('M d, Y') }}
                                    @endif
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                            <td>
                                @php $isOverdue = $vacc->next_due_date && \Carbon\Carbon::parse($vacc->next_due_date)->isPast(); @endphp
                                <span class="hb {{ $isOverdue ? 'hb-overdue' : 'hb-current' }}">
                                    {{ $isOverdue ? 'Overdue' : 'Current' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4" style="color:var(--muted); font-size:.83rem;">
                <i class="bi bi-shield-x d-block mb-2" style="font-size:1.8rem; opacity:.25;"></i>
                No vaccination records yet.
            </div>
            @endif
        </div>

        {{-- Medical History ── --}}
        <div class="card card-med">
            <div class="section-hdr">
                <h6>
                    <i class="bi bi-clipboard-pulse" style="color:var(--coral);"></i>
                    Medical History
                </h6>
                <span style="font-size:.72rem; font-weight:700; padding:.28em .75em; border-radius:20px;
                             background:var(--coral-subtle); color:var(--coral);">
                    {{ $pet->medicalRecords->count() }} records
                </span>
            </div>
            <div class="card-body" style="padding:1.1rem 1.25rem;">
                @forelse($pet->medicalRecords->sortByDesc('created_at') as $i => $record)
                <div class="med-card" data-med="{{ $i }}">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div>
                            <div class="fw-bold" style="font-size:.9rem; color:var(--navy);">
                                {{ $record->created_at->format('F d, Y') }}
                            </div>
                            <div style="font-size:.75rem; color:var(--muted);">
                                by {{ $record->vet->name ?? $record->vet->full_name ?? 'Unknown Vet' }}
                            </div>
                        </div>
                        <div class="d-flex gap-1 flex-wrap justify-content-end">
                            @if($record->health_status)
                            @php
                                $hbMap = ['excellent'=>'hb-excellent','good'=>'hb-good','fair'=>'hb-fair',
                                          'poor'=>'hb-poor','sick'=>'hb-sick','healthy'=>'hb-excellent'];
                            @endphp
                            <span class="hb {{ $hbMap[$record->health_status] ?? 'hb-good' }}">
                                {{ ucfirst($record->health_status) }}
                            </span>
                            @endif
                            @if(isset($record->fit_for_adoption))
                            <span class="hb {{ $record->fit_for_adoption ? 'hb-fit' : 'hb-unfit' }}">
                                <i class="bi bi-{{ $record->fit_for_adoption ? 'check-circle' : 'x-circle' }}" style="font-size:.65rem;"></i>
                                {{ $record->fit_for_adoption ? 'Fit for Adoption' : 'Not Fit Yet' }}
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($record->diagnosis)
                    <div style="font-size:.82rem; margin-bottom:.3rem;">
                        <span style="color:var(--muted); font-weight:600;">Diagnosis:</span>
                        {{ $record->diagnosis }}
                    </div>
                    @endif
                    @if($record->treatment)
                    <div style="font-size:.82rem; margin-bottom:.3rem;">
                        <span style="color:var(--muted); font-weight:600;">Treatment:</span>
                        {{ $record->treatment }}
                    </div>
                    @endif
                    @if($record->notes)
                    <div style="font-size:.8rem; color:var(--muted); font-style:italic; margin-bottom:.3rem;">
                        {{ $record->notes }}
                    </div>
                    @endif
                    @if($record->next_checkup_date)
                    <div style="font-size:.8rem; padding-top:.5rem; border-top:1px solid var(--border); margin-top:.5rem; color:var(--muted);">
                        <i class="bi bi-calendar2-check me-1" style="color:var(--coral);"></i>
                        Next checkup: <strong style="color:var(--navy);">{{ $record->next_checkup_date->format('F d, Y') }}</strong>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-4" style="color:var(--muted); font-size:.83rem;">
                    <i class="bi bi-clipboard2-x d-block mb-2" style="font-size:1.8rem; opacity:.25;"></i>
                    No medical records yet.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Pet info rows stagger ──
    document.querySelectorAll('.info-row-simple').forEach(row => {
        const delay = 400 + (parseInt(row.dataset.row) * 80);
        setTimeout(() => row.classList.add('visible'), delay);
    });

    // ── Vaccination rows stagger ──
    document.querySelectorAll('.vacc-row').forEach(row => {
        const delay = 500 + (parseInt(row.dataset.index) * 80);
        setTimeout(() => row.classList.add('visible'), delay);
    });

    // ── Medical record cards stagger ──
    document.querySelectorAll('.med-card').forEach(card => {
        const delay = 600 + (parseInt(card.dataset.med) * 100);
        setTimeout(() => card.classList.add('visible'), delay);
    });

});
</script>
@endpush