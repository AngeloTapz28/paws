@extends('layouts.app')

@section('title', 'Vet Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-actions')
    <a href="{{ route('vet.pets.index') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-heart-pulse me-1"></i> View All Pets
    </a>
@endsection

@push('styles')
<style>
    .stat-card-v2 {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.4rem 1.5rem;
        position: relative; overflow: hidden;
        transition: transform .2s, box-shadow .2s; box-shadow: var(--shadow-sm);
    }
    .stat-card-v2:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .stat-card-v2 .sc-accent {
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px; border-radius: var(--radius) var(--radius) 0 0;
    }
    .stat-card-v2 .sc-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: .9rem;
    }
    .stat-card-v2 .sc-value { font-size: 2rem; font-weight: 800; color: var(--navy); line-height: 1; margin-bottom: .2rem; }
    .stat-card-v2 .sc-label { font-size: .73rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }

    .section-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--coral-subtle); color: var(--coral);
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .22rem .7rem; border-radius: 20px; margin-bottom: .5rem;
    }

    .exam-item {
        display: flex; align-items: center; gap: .85rem;
        padding: .82rem 1.25rem; border-bottom: 1px solid var(--border);
        transition: background .15s;
    }
    .exam-item:last-child { border-bottom: none; }
    .exam-item:hover { background: var(--coral-subtle); }
    .exam-thumb {
        width: 42px; height: 42px; border-radius: 10px;
        object-fit: cover; border: 2px solid var(--border); flex-shrink: 0;
    }
    .exam-thumb-ph {
        width: 42px; height: 42px; border-radius: 10px;
        background: var(--sage-light); display: flex; align-items: center;
        justify-content: center; font-size: 1.3rem; flex-shrink: 0;
    }

    .sec-hdr {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
    }
    .sec-hdr h6 { font-size: .92rem; font-weight: 700; color: var(--navy); margin: 0; }
    .sec-hdr a  { font-size: .78rem; color: var(--coral); text-decoration: none; }
    .sec-hdr a:hover { text-decoration: underline; }

    .action-btn {
        width: 28px; height: 28px; border-radius: 7px; border: 1px solid var(--border);
        background: var(--white); display: inline-flex; align-items: center;
        justify-content: center; font-size: .8rem; color: var(--muted);
        text-decoration: none; transition: all .15s;
    }
    .action-btn:hover { background: var(--coral-light); color: var(--coral); border-color: transparent; }
</style>
@endpush

@section('content')

@php
    $nameParts   = explode(' ', auth()->user()->name);
    $honorifics  = ['system'];
    $displayName = collect($nameParts)->first(fn($p) => !in_array(strtolower($p), $honorifics)) ?? last($nameParts);
@endphp

{{-- Greeting --}}
<div class="mb-4">
    <h2 style="font-size:1.45rem; font-weight:800; color:var(--navy); margin:0;">
        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ $displayName }} 
    </h2>
    <p class="mb-0 mt-1" style="color:var(--muted); font-size:.85rem;">
        {{ now()->format('l, F j, Y') }} — Here's your health monitoring overview
    </p>
</div>

{{-- Stat Cards — uses $totalPets, $vaccinatedPets, $overdueVaccines from controller --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--coral), #E8956A);"></div>
            <div class="sc-icon" style="background:var(--coral-light); color:var(--coral);"><i class="bi bi-heart-pulse-fill"></i></div>
            <div class="sc-value">{{ $totalPets }}</div>
            <div class="sc-label">Total Pets</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--sage), #A8C8B3);"></div>
            <div class="sc-icon" style="background:var(--sage-light); color:var(--sage);"><i class="bi bi-shield-check"></i></div>
            <div class="sc-value">{{ $vaccinatedPets }}</div>
            <div class="sc-label">Vaccinated Pets</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--gold), #EDD090);"></div>
            <div class="sc-icon" style="background:var(--gold-light); color:#B8892A;"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="sc-value">{{ $overdueVaccines }}</div>
            <div class="sc-label">Overdue Vaccines</div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Recent Medical Records --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="sec-hdr">
                <h6><i class="bi bi-clipboard-pulse me-2" style="color:var(--coral);"></i>Recent Medical Records</h6>
                <a href="{{ route('vet.pets.index') }}">View All Pets <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            @if($recentRecords->isEmpty())
                <div class="empty-state py-4">
                    <span class="empty-icon"></span>
                    <h5>No Records Yet</h5>
                    <p>Medical records you create will appear here.</p>
                </div>
            @else
                @foreach($recentRecords as $record)
                <div class="exam-item">
                    @if($record->pet?->primary_image)
                        <img src="{{ $record->pet->primary_image_url }}" class="exam-thumb" alt="">
                    @else
                        <div class="exam-thumb-ph">🐾</div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <div style="font-weight:700; font-size:.875rem; color:var(--navy);">
                            {{ $record->pet->name ?? '—' }}
                        </div>
                        <div style="font-size:.75rem; color:var(--muted);">
                            by {{ $record->vet->name ?? 'Unknown Vet' }}
                            @if($record->diagnosis) · {{ Str::limit($record->diagnosis, 40) }} @endif
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0">
                        @php
                            $hStyles = [
                                'healthy'    => 'background:var(--sage-light); color:#2D5A3D;',
                                'sick'       => 'background:#FEF0EE; color:#8B2516;',
                                'injured'    => 'background:var(--gold-light); color:#7A5A1A;',
                                'recovering' => 'background:var(--coral-subtle); color:var(--coral-dark);',
                            ];
                            $hs = $hStyles[$record->health_status] ?? 'background:var(--bg);color:var(--muted);';
                        @endphp
                        <span style="font-size:.67rem; font-weight:700; padding:.28em .75em; border-radius:20px; {{ $hs }}">
                            {{ ucfirst(str_replace('_', ' ', $record->health_status ?? '—')) }}
                        </span>
                        @if($record->fit_for_adoption)
                            <span style="font-size:.65rem; font-weight:700; padding:.22em .65em; border-radius:20px; background:var(--sage-light); color:#2D5A3D;">
                                ✓ Fit
                            </span>
                        @else
                            <span style="font-size:.65rem; font-weight:700; padding:.22em .65em; border-radius:20px; background:#FEF0EE; color:#8B2516;">
                                ✗ Not Fit
                            </span>
                        @endif
                        <span style="font-size:.7rem; color:var(--muted);">
                            {{ $record->examination_date?->format('M d, Y') ?? $record->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <a href="{{ route('vet.pets.show', $record->pet) }}" class="action-btn ms-2" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:var(--sage-light);color:#2D5A3D;">
                    <i class="bi bi-lightning-fill"></i> Quick Actions
                </div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">What would you like to do?</h6>
            </div>
            <div class="card-body d-flex flex-column gap-2" style="padding:.9rem 1rem;">
                <a href="{{ route('vet.pets.index') }}"
                   class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                   style="background:var(--coral-subtle); border:1px solid var(--coral-light);">
                    <div style="width:36px;height:36px;border-radius:9px;background:var(--coral);color:#fff;
                                display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <div>
                        <div style="font-size:.855rem;font-weight:700;color:var(--navy);">View All Pet Records</div>
                        <div style="font-size:.73rem;color:var(--muted);">{{ $totalPets }} pets in the system</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--coral);font-size:.8rem;"></i>
                </a>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3"
                     style="background:var(--sage-light); border:1px solid #C0D9C8;">
                    <div style="width:36px;height:36px;border-radius:9px;background:var(--sage);color:#fff;
                                display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div style="font-size:.855rem;font-weight:700;color:var(--navy);">Vaccinated</div>
                        <div style="font-size:.73rem;color:var(--muted);">{{ $vaccinatedPets }} of {{ $totalPets }} pets</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3"
                     style="background:var(--gold-light); border:1px solid #EDD8A0;">
                    <div style="width:36px;height:36px;border-radius:9px;background:var(--gold);color:var(--navy);
                                display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div style="font-size:.855rem;font-weight:700;color:var(--navy);">Overdue Vaccines</div>
                        <div style="font-size:.73rem;color:var(--muted);">{{ $overdueVaccines }} need attention</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection