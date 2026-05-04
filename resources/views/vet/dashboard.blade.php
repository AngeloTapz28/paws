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
    /* ── Stat cards ── */
    .stat-card-v2 {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.4rem 1.5rem;
        position: relative; overflow: hidden;
        transition: transform .2s, box-shadow .2s; box-shadow: var(--shadow-sm);
    }
    .stat-card-v2:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .stat-card-v2 .sc-accent {
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px; border-radius: var(--radius) var(--radius) 0 0;
    }
    .stat-card-v2 .sc-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: .9rem;
    }
    .stat-card-v2 .sc-value { font-size: 2rem; font-weight: 800; line-height: 1; color: var(--navy); margin-bottom: .2rem; }
    .stat-card-v2 .sc-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }

    /* ── Section header ── */
    .sec-hdr {
        display: flex; align-items: center; justify-content: space-between;
        padding: .85rem 1.25rem; border-bottom: 1px solid var(--border);
    }
    .sec-hdr h6 { font-size: .9rem; font-weight: 700; color: var(--navy); margin: 0; }
    .sec-hdr a  { font-size: .78rem; color: var(--coral); text-decoration: none; display: flex; align-items: center; gap: .3rem; }
    .sec-hdr a:hover { text-decoration: underline; }

    /* ── Section pill ── */
    .section-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .25rem .75rem; border-radius: 20px;
        margin-bottom: .5rem;
    }

    /* ── Exam item (medical records) ── */
    .exam-item {
        display: flex; align-items: center; gap: .85rem;
        padding: .85rem 1.25rem; border-bottom: 1px solid var(--border);
        transition: background .15s;
    }
    .exam-item:last-child { border-bottom: none; }
    .exam-item:hover { background: var(--coral-subtle); }
    .exam-thumb {
        width: 44px; height: 44px; border-radius: 10px;
        object-fit: cover; border: 2px solid var(--border); flex-shrink: 0;
        transition: transform .2s, border-color .2s;
    }
    .exam-item:hover .exam-thumb { transform: scale(1.08); border-color: var(--coral); }
    .exam-thumb-ph {
        width: 44px; height: 44px; border-radius: 10px;
        background: var(--coral-light); display: flex; align-items: center;
        justify-content: center; font-size: 1.4rem; flex-shrink: 0;
    }
    .action-btn {
        width: 28px; height: 28px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); background: var(--white);
        color: var(--muted); text-decoration: none; font-size: .82rem;
        transition: all .15s; flex-shrink: 0;
    }
    .action-btn:hover { background: var(--coral-subtle); color: var(--coral); border-color: var(--coral-light); }

    /* ── Health badges ── */
    .hb { font-size:.68rem; font-weight:700; padding:.25em .65em; border-radius:20px; display:inline-block; }
    .hb-excellent { background:var(--sage-light);  color:#2D5A3D; }
    .hb-good      { background:rgba(45,49,71,.07); color:var(--navy); }
    .hb-fair      { background:var(--gold-light);  color:#7A5A1A; }
    .hb-poor      { background:#FEF0EE;             color:#8B2516; }
    .hb-fit       { background:var(--sage-light);  color:#2D5A3D; }
    .hb-unfit     { background:#FEF0EE;             color:#8B2516; }

    /* ── Quick action items ── */
    .qa-item {
        display: flex; align-items: center; gap: .85rem;
        padding: .85rem 1rem; border-radius: var(--radius-sm);
        border: 1px solid var(--border); text-decoration: none; color: inherit;
        transition: background .15s, transform .15s;
        margin-bottom: .5rem;
    }
    .qa-item:last-child { margin-bottom: 0; }
    .qa-item:hover { transform: translateX(4px); color: inherit; }
    .qa-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .qa-title { font-size: .855rem; font-weight: 700; color: var(--navy); margin-bottom: .1rem; }
    .qa-sub   { font-size: .73rem; color: var(--muted); }
    .qa-arrow { margin-left: auto; font-size: .8rem; transition: transform .15s, color .15s; }
    .qa-item:hover .qa-arrow { transform: translateX(3px); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideLeft  { from { opacity:0; transform:translateX(-18px); } to { opacity:1; transform:translateX(0); } }
    @keyframes slideRight { from { opacity:0; transform:translateX(18px);  } to { opacity:1; transform:translateX(0); } }
    @keyframes fadeDown   { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

    /* Welcome */
    .welcome-strip { animation: fadeDown .45s ease both; }

    /* Stat cards */
    .stat-card-v2 { opacity: 0; }
    .stat-card-v2.animated { animation: fadeUp .45s ease both; }

    /* Main grid */
    .col-records { opacity: 0; animation: slideLeft  .45s ease .5s both; }
    .col-quick   { opacity: 0; animation: slideRight .45s ease .6s both; }

    /* Record rows */
    .exam-item { opacity: 0; }
    .exam-item.visible { animation: slideLeft .38s ease both; }

    /* Quick action items */
    .qa-item { opacity: 0; }
    .qa-item.visible { animation: slideRight .38s ease both; }
</style>
@endpush

@section('content')

{{-- ── Welcome ── --}}
@php
    $nameParts  = explode(' ', auth()->user()->name);
    $honorifics = ['dr.','dr','mr.','mr','ms.','ms','mrs.','mrs','prof.','prof'];
    $firstName  = collect($nameParts)->first(fn($p) => !in_array(strtolower($p), $honorifics)) ?? 'Dr.';
    $greeting   = 'Dr. ' . $firstName;
@endphp
<div class="welcome-strip d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 style="font-size:1.5rem; font-weight:800; color:var(--navy); margin:0;">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ $greeting }}
        </h2>
        <p class="mb-0 mt-1" style="color:var(--muted); font-size:.85rem;">
            {{ now()->format('l, F j, Y') }} — Here's your health monitoring overview
        </p>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">

    <div class="col-sm-4">
        <div class="stat-card-v2" data-delay="0">
            <div class="sc-accent" style="background:linear-gradient(90deg,var(--coral),#E8956A);"></div>
            <div class="sc-icon" style="background:var(--coral-light); color:var(--coral);"><i class="bi bi-heart-pulse-fill"></i></div>
            <div class="sc-value" data-count="{{ $totalPets }}">0</div>
            <div class="sc-label">Total Pets</div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="stat-card-v2" data-delay="120">
            <div class="sc-accent" style="background:linear-gradient(90deg,var(--sage),#A8C8B3);"></div>
            <div class="sc-icon" style="background:var(--sage-light); color:var(--sage);"><i class="bi bi-shield-check"></i></div>
            <div class="sc-value" data-count="{{ $vaccinatedPets }}">0</div>
            <div class="sc-label">Vaccinated Pets</div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="stat-card-v2" data-delay="240">
            <div class="sc-accent" style="background:linear-gradient(90deg,var(--gold),#EDD090);"></div>
            <div class="sc-icon" style="background:var(--gold-light); color:#B8892A;"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="sc-value" data-count="{{ $overdueVaccines }}" style="{{ $overdueVaccines > 0 ? 'color:#C0392B;' : '' }}">0</div>
            <div class="sc-label">Overdue Vaccines</div>
        </div>
    </div>

</div>

{{-- ── Main Grid ── --}}
<div class="row g-3">

    {{-- Recent Medical Records ── --}}
    <div class="col-lg-8 col-records">
        <div class="card h-100">
            <div class="sec-hdr">
                <h6><i class="bi bi-clipboard-pulse me-2" style="color:var(--coral);"></i>Recent Medical Records</h6>
                <a href="{{ route('vet.pets.index') }}">View All Pets <i class="bi bi-arrow-right"></i></a>
            </div>
            @if($recentRecords->isEmpty())
                <div class="empty-state py-5">
                    <span class="empty-icon">🩺</span>
                    <h5>No Records Yet</h5>
                    <p>Medical records you create will appear here.</p>
                </div>
            @else
                @foreach($recentRecords as $i => $record)
                <div class="exam-item" data-index="{{ $i }}">
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
                            by {{ $record->vet->name ?? 'Unknown Vet' }} ·
                            {{ $record->chief_complaint ?? $record->diagnosis ?? 'none so far' }}
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        @if($record->health_status)
                        @php
                            $hs = $record->health_status;
                            $hbClass = match($hs) {
                                'excellent' => 'hb-excellent',
                                'good'      => 'hb-good',
                                'fair'      => 'hb-fair',
                                default     => 'hb-poor',
                            };
                        @endphp
                        <span class="hb {{ $hbClass }}">{{ ucfirst($hs) }}</span>
                        @endif
                        @if(isset($record->is_fit_for_adoption))
                        <span class="hb {{ $record->is_fit_for_adoption ? 'hb-fit' : 'hb-unfit' }}">
                            {{ $record->is_fit_for_adoption ? '✓ Fit' : '✗ Unfit' }}
                        </span>
                        @endif
                        <span style="font-size:.72rem; color:var(--muted);">
                            {{ $record->created_at?->format('M d, Y') }}
                        </span>
                    </div>
                    <a href="{{ route('vet.pets.show', $record->pet) }}" class="action-btn ms-1" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Quick Actions ── --}}
    <div class="col-lg-4 col-quick">
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:var(--sage-light); color:#2D5A3D;">
                    <i class="bi bi-lightning-fill"></i> Quick Actions
                </div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">What would you like to do?</h6>
            </div>
            <div class="card-body d-flex flex-column gap-2" style="padding:.9rem 1rem;" id="qaList">

                <a href="{{ route('vet.pets.index') }}" class="qa-item" data-qa="0"
                   style="background:var(--coral-subtle); border-color:var(--coral-light);">
                    <div class="qa-icon" style="background:var(--coral); color:#fff;">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <div>
                        <div class="qa-title">View All Pet Records</div>
                        <div class="qa-sub">{{ $totalPets }} pets in the system</div>
                    </div>
                    <i class="bi bi-chevron-right qa-arrow" style="color:var(--coral);"></i>
                </a>

                <div class="qa-item" data-qa="1" style="background:var(--sage-light); border-color:#C0D9C8;">
                    <div class="qa-icon" style="background:var(--sage); color:#fff;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div class="qa-title">Vaccinated</div>
                        <div class="qa-sub">{{ $vaccinatedPets }} of {{ $totalPets }} pets</div>
                    </div>
                    <i class="bi bi-chevron-right qa-arrow" style="color:var(--sage);"></i>
                </div>

                <div class="qa-item" data-qa="2"
                     style="background:var(--gold-light); border-color:#DBBF72; {{ $overdueVaccines > 0 ? 'border-color:#C0392B; background:#FEF0EE;' : '' }}">
                    <div class="qa-icon" style="background:{{ $overdueVaccines > 0 ? '#C0392B' : '#B8892A' }}; color:#fff;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <div class="qa-title">Overdue Vaccines</div>
                        <div class="qa-sub">{{ $overdueVaccines }} need{{ $overdueVaccines === 1 ? 's' : '' }} attention</div>
                    </div>
                    <i class="bi bi-chevron-right qa-arrow" style="color:{{ $overdueVaccines > 0 ? '#C0392B' : '#B8892A' }};"></i>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── 1. Stat cards stagger ──
    document.querySelectorAll('.stat-card-v2').forEach(card => {
        setTimeout(() => card.classList.add('animated'), parseInt(card.dataset.delay ?? 0));
    });

    // ── 2. Count-up ──
    function countUp(el) {
        const target = parseInt(el.dataset.count);
        if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
        const dur = 900, step = 16, inc = target / (dur / step);
        let cur = 0;
        const t = setInterval(() => {
            cur += inc;
            if (cur >= target) { clearInterval(t); el.textContent = target; }
            else el.textContent = Math.floor(cur);
        }, step);
    }
    setTimeout(() => {
        document.querySelectorAll('.stat-card-v2 .sc-value[data-count]').forEach(countUp);
    }, 320);

    // ── 3. Record rows stagger ──
    document.querySelectorAll('.exam-item').forEach(item => {
        const delay = 600 + (parseInt(item.dataset.index) * 80);
        setTimeout(() => item.classList.add('visible'), delay);
    });

    // ── 4. Quick action items stagger ──
    document.querySelectorAll('.qa-item').forEach(item => {
        const delay = 700 + (parseInt(item.dataset.qa) * 100);
        setTimeout(() => item.classList.add('visible'), delay);
    });

});
</script>
@endpush