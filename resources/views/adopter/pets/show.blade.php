@extends('layouts.app')
@section('title', $pet->name)
@section('page-title', $pet->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('adopter.pets.index') }}">Browse Pets</a></li>
    <li class="breadcrumb-item active">{{ $pet->name }}</li>
@endsection

@push('styles')
<style>
    /* ── Image ── */
    .pet-photo-wrap {
        border-radius: var(--radius); overflow: hidden;
        box-shadow: var(--shadow-md); position: relative;
    }
    .pet-photo {
        width: 100%; height: 380px; object-fit: cover;
        display: block; transition: transform .4s ease;
        cursor: zoom-in;
    }
    .pet-photo:hover { transform: scale(1.03); }
    .pet-photo-ph {
        height: 380px; background: var(--coral-light);
        display: flex; align-items: center; justify-content: center; font-size: 6rem;
    }

    /* ── Status badge ── */
    .status-pill {
        font-size: .8rem; font-weight: 700; padding: .45em 1.2em;
        border-radius: 25px; letter-spacing: .04em;
    }
    .s-available { background: var(--sage); color: #fff; }
    .s-other     { background: var(--muted); color: #fff; }

    /* ── Detail grid ── */
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .75rem; margin: 1rem 0; }
    .detail-cell {
        background: var(--bg); border-radius: var(--radius-sm);
        padding: .6rem .85rem; border: 1px solid var(--border);
        transition: background .15s, transform .15s;
    }
    .detail-cell:hover { background: var(--coral-subtle); transform: translateY(-1px); }
    .detail-cell .dc-label { font-size: .67rem; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); }
    .detail-cell .dc-value { font-size: .875rem; font-weight: 700; color: var(--navy); margin-top: .1rem; }
    .detail-cell .dc-icon  { font-size: .85rem; color: var(--coral); margin-bottom: .2rem; }

    /* ── Health badges ── */
    .health-badge {
        font-size: .73rem; font-weight: 600; padding: .3em .8em;
        border-radius: 20px; display: inline-flex; align-items: center; gap: .3rem;
    }
    .hb-vaccinated   { background: var(--sage-light);      color: #2D5A3D; }
    .hb-not-vacc     { background: rgba(45,49,71,.07);     color: var(--muted); }
    .hb-neutered     { background: rgba(111,66,193,.1);    color: #6f42c1; }
    .hb-not-neutered { background: rgba(45,49,71,.07);     color: var(--muted); }
    .hb-micro        { background: rgba(45,49,71,.07);     color: var(--navy); }
    .hb-no-micro     { background: rgba(45,49,71,.07);     color: var(--muted); }

    /* ── About section ── */
    .about-text { font-size: .875rem; color: var(--text); line-height: 1.7; }

    /* ── Special needs ── */
    .special-needs-box {
        background: var(--gold-light); border-radius: var(--radius-sm);
        padding: .75rem 1rem; border-left: 3px solid var(--gold);
        font-size: .855rem; color: #5A4010;
    }

    /* ── Fee + CTA ── */
    .fee-cta-bar {
        background: var(--bg); border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        padding: 1rem 1.25rem;
        display: flex; align-items: center; justify-content: space-between;
    }
    .fee-label { font-size: .73rem; color: var(--muted); font-weight: 500; margin-bottom: .15rem; }
    .fee-amount { font-size: 1.4rem; font-weight: 800; color: var(--coral); line-height: 1; }

    .btn-adopt-me {
        display: inline-flex; align-items: center; gap: .5rem;
        background: var(--coral); color: #fff; border: none;
        border-radius: 25px; padding: .65rem 1.75rem;
        font-size: .92rem; font-weight: 700; text-decoration: none;
        transition: background .2s, transform .15s, box-shadow .2s;
    }
    .btn-adopt-me:hover {
        background: var(--coral-dark); color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(217,119,87,.4);
    }
    .btn-view-app {
        display: inline-flex; align-items: center; gap: .5rem;
        background: var(--white); color: var(--coral); border: 2px solid var(--coral);
        border-radius: 25px; padding: .55rem 1.5rem;
        font-size: .88rem; font-weight: 600; text-decoration: none;
        transition: all .2s;
    }
    .btn-view-app:hover { background: var(--coral); color: #fff; }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes imageReveal {
        from { opacity: 0; transform: scale(.96); }
        to   { opacity: 1; transform: scale(1); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-22px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(22px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes badgePop {
        0%   { transform: scale(.5); opacity: 0; }
        70%  { transform: scale(1.12); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(217,119,87,0); }
        50%       { box-shadow: 0 0 0 10px rgba(217,119,87,.2); }
    }
    @keyframes cellIn {
        from { opacity: 0; transform: translateY(10px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Photo */
    .pet-photo-wrap { animation: imageReveal .55s ease both; opacity: 0; }

    /* Right column */
    .detail-col { opacity: 0; animation: slideInRight .45s ease .15s both; }

    /* Name + badge */
    .pet-name-row { opacity: 0; animation: fadeDown .4s ease .3s both; }
    .status-pill  { animation: badgePop .5s cubic-bezier(.34,1.56,.64,1) .35s both; opacity: 0; }

    /* Sub breed line */
    .pet-sub { opacity: 0; animation: fadeUp .38s ease .38s both; }

    /* Detail cells stagger — JS */
    .detail-cell { opacity: 0; }
    .detail-cell.visible { animation: cellIn .35s ease both; }

    /* Health badges */
    .health-badges { opacity: 0; animation: fadeUp .4s ease .65s both; }

    /* About */
    .about-section { opacity: 0; animation: fadeUp .4s ease .72s both; }

    /* Special needs */
    .sn-box { opacity: 0; animation: fadeUp .4s ease .78s both; }

    /* Fee CTA */
    .fee-cta-bar { opacity: 0; animation: fadeUp .4s ease .85s both; }

    /* Adopt Me button pulse after it appears */
    .btn-adopt-me { animation: pulseGlow 2.5s ease 1.5s 2; }
</style>
@endpush

@section('content')

<div class="row g-4">

    {{-- ── LEFT: Photo ── --}}
    <div class="col-lg-5">
        <div class="pet-photo-wrap">
            @if($pet->primary_image)
                <img src="{{ Storage::url($pet->primary_image) }}"
                     class="pet-photo" alt="{{ $pet->name }}">
            @else
                <div class="pet-photo-ph">
                    {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                </div>
            @endif
        </div>

        {{-- Vaccination records if any ── --}}
        @if($pet->vaccinationRecords && $pet->vaccinationRecords->count())
        <div class="card mt-3" style="opacity:0; animation: fadeUp .4s ease .9s both;">
            <div class="card-header" style="padding:.85rem 1.25rem;">
                <h6 class="mb-0 fw-bold" style="color:var(--navy); font-size:.87rem;">
                    <i class="bi bi-shield-check me-2" style="color:var(--sage);"></i>Vaccination Records
                </h6>
            </div>
            <div class="card-body" style="padding:.85rem 1.25rem;">
                @foreach($pet->vaccinationRecords as $vac)
                <div style="display:flex; justify-content:space-between; padding:.4rem 0;
                            border-bottom:1px solid var(--border); font-size:.82rem;">
                    <span style="font-weight:600; color:var(--navy);">{{ $vac->vaccine_name }}</span>
                    <span style="color:var(--muted);">{{ $vac->administered_at?->format('M d, Y') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── RIGHT: Details ── --}}
    <div class="col-lg-7 detail-col">

        {{-- Name + Status ── --}}
        <div class="pet-name-row d-flex align-items-start justify-content-between gap-2 mb-1">
            <h2 style="font-size:2rem; font-weight:800; color:var(--navy); margin:0; line-height:1.1;">
                {{ $pet->name }}
            </h2>
            <span class="status-pill {{ $pet->status === 'available' ? 's-available' : 's-other' }}">
                {{ ucfirst($pet->status) }}
            </span>
        </div>

        {{-- Category + Breed ── --}}
        <p class="pet-sub" style="font-size:.9rem; color:var(--muted); margin-bottom:1rem;">
            {{ $pet->category->icon ?? '🐾' }}
            {{ $pet->category->name ?? '—' }}
            @if($pet->breed) · {{ $pet->breed->name }} @endif
        </p>

        {{-- Detail Grid ── --}}
        <div class="detail-grid" id="detailGrid">
            @foreach([
                ['icon' => 'bi-gender-ambiguous', 'label' => 'Gender',  'value' => ucfirst($pet->gender ?? '—')],
                ['icon' => 'bi-calendar3',        'label' => 'Age',     'value' => $pet->age_label ?? ($pet->age ? $pet->age . ' yrs' : '—')],
                ['icon' => 'bi-rulers',           'label' => 'Size',    'value' => ucfirst($pet->size ?? '—')],
                ['icon' => 'bi-palette',          'label' => 'Color',   'value' => $pet->color ?? '—'],
                ['icon' => 'bi-speedometer2',     'label' => 'Weight',  'value' => $pet->weight ? $pet->weight . 'kg' : '—'],
            ] as $idx => $d)
            <div class="detail-cell" data-idx="{{ $idx }}">
                <div class="dc-icon"><i class="bi {{ $d['icon'] }}"></i></div>
                <div class="dc-label">{{ $d['label'] }}</div>
                <div class="dc-value">{{ $d['value'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Health Badges ── --}}
        <div class="health-badges d-flex gap-2 flex-wrap mb-3">
            <span class="health-badge {{ $pet->is_vaccinated ? 'hb-vaccinated' : 'hb-not-vacc' }}">
                <i class="bi {{ $pet->is_vaccinated ? 'bi-patch-check-fill' : 'bi-x-circle' }}"></i>
                {{ $pet->is_vaccinated ? 'Vaccinated' : 'Not Vaccinated' }}
            </span>
            <span class="health-badge {{ $pet->is_neutered ? 'hb-neutered' : 'hb-not-neutered' }}">
                <i class="bi {{ $pet->is_neutered ? 'bi-scissors' : 'bi-x-circle' }}"></i>
                {{ $pet->is_neutered ? 'Neutered/Spayed' : 'Not Neutered' }}
            </span>
            <span class="health-badge {{ $pet->is_microchipped ? 'hb-micro' : 'hb-no-micro' }}">
                <i class="bi bi-cpu"></i>
                {{ $pet->is_microchipped ? 'Microchipped' : 'No Microchip' }}
            </span>
        </div>

        {{-- About ── --}}
        @if($pet->description)
        <div class="about-section mb-3">
            <h6 class="fw-bold mb-2" style="color:var(--navy);">About {{ $pet->name }}</h6>
            <p class="about-text mb-0">{{ $pet->description }}</p>
        </div>
        @endif

        {{-- Special Needs ── --}}
        <div class="sn-box mb-3">
            <div class="special-needs-box">
                <i class="bi bi-heart-pulse me-2" style="color:#B8892A;"></i>
                <strong>Special Needs:</strong> {{ $pet->special_needs ?? 'none' }}
            </div>
        </div>

        {{-- Fee + CTA ── --}}
        <div class="fee-cta-bar">
            <div>
                <div class="fee-label">Adoption Fee</div>
                <div class="fee-amount">₱{{ number_format($pet->adoption_fee, 2) }}</div>
            </div>

            @if($pet->status === 'available')
                @if($existingApplication)
                    <a href="{{ route('adopter.applications.show', $existingApplication) }}" class="btn-view-app">
                        <i class="bi bi-file-earmark-check"></i> View My Application
                    </a>
                @else
                    <a href="{{ route('adopter.applications.create', $pet) }}" class="btn-adopt-me">
                        <i class="bi bi-heart-fill"></i> Adopt Me!
                    </a>
                @endif
            @else
                <span style="font-size:.85rem; font-weight:600; color:var(--muted); padding:.6rem 1.2rem; background:var(--bg); border-radius:20px;">
                    Not Available
                </span>
            @endif
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Stagger detail cells
    document.querySelectorAll('.detail-cell').forEach(cell => {
        const delay = 450 + (parseInt(cell.dataset.idx) * 80);
        setTimeout(() => cell.classList.add('visible'), delay);
    });
});
</script>
@endpush