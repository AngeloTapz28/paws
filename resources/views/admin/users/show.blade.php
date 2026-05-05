@extends('layouts.app')
@section('title', $user->name)
@section('page-title', 'User Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-pencil me-1"></i>Edit
    </a>
    @if($user->id !== auth()->id())
        <form method="POST" action="{{ route('admin.users.toggleStatus', $user) }}" class="d-inline">
            @csrf
            <button class="btn btn-{{ $user->status === 'active' ? 'outline-warning' : 'outline-success' }} btn-sm">
                <i class="bi bi-{{ $user->status === 'active' ? 'pause-circle' : 'play-circle' }} me-1"></i>
                {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
            </button>
        </form>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
              onsubmit="return confirm('Permanently delete {{ $user->name }}? This cannot be undone.')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i>Delete
            </button>
        </form>
    @endif
@endsection

@push('styles')
<style>
    /* ── Avatar ── */
    .user-avatar-lg {
        width: 80px; height: 80px; border-radius: 50%;
        background: var(--coral); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: 800; margin: 0 auto 1rem;
        box-shadow: 0 4px 18px rgba(217,119,87,.3);
        transition: transform .2s;
    }
    .user-avatar-lg:hover { transform: scale(1.05); }
    .user-avatar-img {
        width: 80px; height: 80px; border-radius: 50%; object-fit: cover;
        margin: 0 auto 1rem; display: block;
        border: 3px solid var(--coral-light);
        box-shadow: 0 4px 18px rgba(217,119,87,.2);
        transition: transform .2s;
    }
    .user-avatar-img:hover { transform: scale(1.05); }

    /* ── Status badge ── */
    .status-pill {
        font-size: .82rem; font-weight: 700; padding: .4em 1.1em;
        border-radius: 25px; display: inline-block; margin-top: .35rem;
    }
    .sp-active    { background: var(--sage-light); color: #2D5A3D; }
    .sp-inactive  { background: rgba(45,49,71,.07); color: var(--muted); }
    .sp-suspended { background: #FEF0EE; color: #8B2516; }

    /* ── Role badge ── */
    .role-pill {
        font-size: .73rem; font-weight: 700; padding: .28em .8em;
        border-radius: 20px; background: var(--coral-subtle); color: var(--coral);
        display: inline-block;
    }

    /* ── Info rows ── */
    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: .55rem 0; border-bottom: 1px solid var(--border);
        font-size: .855rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .ir-label { color: var(--muted); font-weight: 500; }
    .info-row .ir-value { font-weight: 600; color: var(--text); }

    /* ── Section header ── */
    .sec-hdr {
        display: flex; align-items: center; justify-content: space-between;
        padding: .85rem 1.25rem; border-bottom: 1px solid var(--border);
    }
    .sec-hdr h6 { margin: 0; font-size: .88rem; font-weight: 700; color: var(--navy); display: flex; align-items: center; gap: .5rem; }

    /* ── App / pet rows ── */
    .list-row {
        display: flex; align-items: center; gap: .85rem;
        padding: .75rem 1.25rem; border-bottom: 1px solid var(--border);
        transition: background .12s;
    }
    .list-row:last-child { border-bottom: none; }
    .list-row:hover { background: var(--coral-subtle); }
    .list-thumb {
        width: 38px; height: 38px; border-radius: 9px; object-fit: cover;
        border: 2px solid var(--border); flex-shrink: 0;
        transition: transform .18s, border-color .18s;
    }
    .list-row:hover .list-thumb { transform: scale(1.08); border-color: var(--coral); }
    .list-thumb-ph {
        width: 38px; height: 38px; border-radius: 9px;
        background: var(--coral-light); display: flex; align-items: center;
        justify-content: center; font-size: 1.2rem; flex-shrink: 0;
    }

    /* ── Add role ── */
    .btn-add-role {
        background: var(--coral); color: #fff; border: none;
        border-radius: var(--radius-sm); padding: .35rem .9rem;
        font-size: .8rem; font-weight: 600; flex-shrink: 0;
        transition: background .15s, transform .12s;
    }
    .btn-add-role:hover { background: var(--coral-dark); color: #fff; transform: translateY(-1px); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes slideInLeft  { from { opacity:0; transform:translateX(-22px); } to { opacity:1; transform:translateX(0); } }
    @keyframes slideInRight { from { opacity:0; transform:translateX(22px);  } to { opacity:1; transform:translateX(0); } }
    @keyframes fadeUp       { from { opacity:0; transform:translateY(14px);  } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeDown     { from { opacity:0; transform:translateY(-8px);  } to { opacity:1; transform:translateY(0); } }
    @keyframes avatarPop {
        0%   { opacity:0; transform:scale(0.6); }
        70%  { transform:scale(1.1); }
        100% { opacity:1; transform:scale(1); }
    }
    @keyframes badgePop {
        0%   { opacity:0; transform:scale(.5); }
        70%  { transform:scale(1.12); }
        100% { opacity:1; transform:scale(1); }
    }
    @keyframes rowSlide {
        from { opacity:0; transform:translateX(-12px); }
        to   { opacity:1; transform:translateX(0); }
    }

    /* Profile card */
    .card-profile { opacity:0; animation: slideInLeft  .45s ease .1s  both; }
    .card-contact { opacity:0; animation: slideInLeft  .45s ease .25s both; }
    .card-role    { opacity:0; animation: slideInLeft  .45s ease .4s  both; }

    /* Right column */
    .card-apps    { opacity:0; animation: slideInRight .45s ease .2s  both; }
    .card-pets    { opacity:0; animation: slideInRight .45s ease .35s both; }

    /* Avatar */
    .user-avatar-lg,
    .user-avatar-img { animation: avatarPop .55s cubic-bezier(.34,1.56,.64,1) .3s forwards; opacity:0; }

    /* Name, email, badges */
    .user-name   { opacity:0; animation: fadeDown .38s ease .45s both; }
    .user-email  { opacity:0; animation: fadeDown .38s ease .52s both; }
    .user-roles  { opacity:0; animation: fadeDown .38s ease .58s both; }
    .user-status { opacity:0; animation: badgePop .45s cubic-bezier(.34,1.56,.64,1) .65s both; }

    /* Info rows — JS stagger */
    .info-row { opacity:0; }
    .info-row.visible { animation: rowSlide .35s ease both; }

    /* App / pet rows — JS stagger */
    .list-row { opacity:0; }
    .list-row.visible { animation: rowSlide .38s ease both; }
</style>
@endpush

@section('content')

<div class="row g-4">

    {{-- ══ LEFT COLUMN ══ --}}
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Profile card ── --}}
        <div class="card card-profile text-center" style="padding:1.75rem 1.25rem 1.25rem;">
            @if($user->avatar)
                <img src="{{ $user->avatar_url }}" class="user-avatar-img" alt="{{ $user->name }}">
            @else
                <div class="user-avatar-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif

            <h5 class="fw-bold mb-1 user-name" style="color:var(--navy);">{{ $user->name }}</h5>
            <p class="user-email mb-2" style="font-size:.82rem; color:var(--muted);">{{ $user->email }}</p>

            <div class="user-roles d-flex justify-content-center gap-1 flex-wrap mb-2">
                @foreach($user->roles as $role)
                    <span class="role-pill">{{ $role->display_name }}</span>
                @endforeach
            </div>

            @php
                $spClass = match($user->status) {
                    'active'    => 'sp-active',
                    'suspended' => 'sp-suspended',
                    default     => 'sp-inactive',
                };
            @endphp
            <span class="status-pill {{ $spClass }} user-status">{{ ucfirst($user->status) }}</span>
        </div>

        {{-- Contact Info ── --}}
        <div class="card card-contact">
            <div class="sec-hdr">
                <h6><i class="bi bi-person-lines-fill" style="color:var(--coral);"></i> Contact Info</h6>
            </div>
            <div class="card-body" style="padding:.85rem 1.25rem;" id="infoRows">
                @php
                    $info = [
                        'Phone'         => $user->phone ?? '—',
                        'Gender'        => $user->gender ? ucfirst($user->gender) : '—',
                        'Date of Birth' => $user->date_of_birth?->format('M d, Y') ?? '—',
                        'Joined'        => $user->created_at->format('M d, Y'),
                        'Last Login'    => $user->last_login_at?->diffForHumans() ?? 'Never',
                    ];
                @endphp
                @foreach($info as $label => $value)
                <div class="info-row" data-row="{{ $loop->index }}">
                    <span class="ir-label">{{ $label }}</span>
                    <span class="ir-value">{{ $value }}</span>
                </div>
                @endforeach
                @if($user->address)
                <div class="info-row" data-row="5">
                    <span class="ir-label">Address</span>
                    <span class="ir-value" style="text-align:right; max-width:60%;">{{ $user->address }}</span>
                </div>
                @endif
                @if($user->notes)
                <div class="mt-2 p-2 rounded" style="background:var(--bg); font-size:.78rem; color:var(--muted);">
                    <strong style="color:var(--navy);">Notes:</strong> {{ $user->notes }}
                </div>
                @endif
            </div>
        </div>

        {{-- Add Role ── --}}
        <div class="card card-role">
            <div class="sec-hdr">
                <h6><i class="bi bi-shield-plus" style="color:var(--coral);"></i> Add Role</h6>
            </div>
            <div class="card-body" style="padding:.85rem 1.25rem;">
                <form method="POST" action="{{ route('admin.users.assignRole', $user) }}"
                      class="d-flex gap-2">
                    @csrf
                    <select name="role_id" class="form-select form-select-sm">
                        <option value="">— Select Role —</option>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-add-role">Add</button>
                </form>
            </div>
        </div>

    </div>

    {{-- ══ RIGHT COLUMN ══ --}}
    <div class="col-lg-8 d-flex flex-column gap-3">

        {{-- Adoption Applications ── --}}
        <div class="card card-apps">
            <div class="sec-hdr">
                <h6>
                    <i class="bi bi-file-earmark-text" style="color:var(--gold);"></i>
                    Adoption Applications
                </h6>
                <span style="font-size:.72rem; font-weight:700; padding:.28em .75em; border-radius:20px;
                             background:var(--bg); color:var(--muted);">
                    {{ $user->adoptionApplications->count() }}
                </span>
            </div>
            <div id="appRows">
                @forelse($user->adoptionApplications as $i => $app)
                <div class="list-row" data-row="{{ $i }}">
                    @if($app->pet?->primary_image)
                        <img src="{{ $app->pet->primary_image_url }}" class="list-thumb" alt="">
                    @else
                        <div class="list-thumb-ph">🐾</div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <div style="font-size:.855rem; font-weight:700; color:var(--navy);">
                            {{ $app->application_number }}
                        </div>
                        <div style="font-size:.75rem; color:var(--muted);">
                            Pet: {{ $app->pet?->name ?? '—' }} ·
                            {{ $app->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    @php
                        $statusStyle = match($app->status) {
                            'pending','submitted'         => 'background:var(--gold-light);color:#7A5A1A;',
                            'reviewing','under_review'    => 'background:var(--coral-subtle);color:var(--coral-dark);',
                            'approved'                    => 'background:var(--sage-light);color:#2D5A3D;',
                            'completed'                   => 'background:var(--sage-light);color:#2D5A3D;',
                            'rejected'                    => 'background:#FEF0EE;color:#8B2516;',
                            default                       => 'background:#F3F4F6;color:#6B7280;',
                        };
                    @endphp
                    <span style="font-size:.68rem; font-weight:700; padding:.3em .8em; border-radius:20px; white-space:nowrap; {{ $statusStyle }}">
                        {{ $app->status_label }}
                    </span>
                    <a href="{{ route('admin.applications.show', $app) }}"
                       class="btn btn-sm btn-outline-primary" style="font-size:.75rem; padding:.25rem .65rem; flex-shrink:0;">
                        View
                    </a>
                </div>
                @empty
                <div class="text-center py-4" style="color:var(--muted); font-size:.83rem;">
                    No adoption applications.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Pets Added (Staff/Admin) ── --}}
        @if($user->pets->isNotEmpty())
        <div class="card card-pets">
            <div class="sec-hdr">
                <h6>
                    <i class="bi bi-heart-fill" style="color:var(--coral);"></i>
                    Pets Added by This User
                </h6>
                <span style="font-size:.72rem; font-weight:700; padding:.28em .75em; border-radius:20px;
                             background:var(--bg); color:var(--muted);">
                    {{ $user->pets->count() }}
                </span>
            </div>
            <div id="petRows">
                @foreach($user->pets as $i => $pet)
                <div class="list-row" data-pet="{{ $i }}">
                    @if($pet->primary_image)
                        <img src="{{ $pet->primary_image_url }}" class="list-thumb" alt="">
                    @else
                        <div class="list-thumb-ph">
                            {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : '🐱' }}
                        </div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <div style="font-size:.855rem; font-weight:700; color:var(--navy);">{{ $pet->name }}</div>
                        <div style="font-size:.75rem; color:var(--muted);">
                            {{ $pet->category->name ?? '—' }} · Added {{ $pet->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    @php
                        $petStatusStyle = match($pet->status) {
                            'available' => 'background:var(--sage-light);color:#2D5A3D;',
                            'adopted'   => 'background:var(--coral-subtle);color:var(--coral);',
                            'pending'   => 'background:var(--gold-light);color:#7A5A1A;',
                            default     => 'background:#F3F4F6;color:#6B7280;',
                        };
                    @endphp
                    <span style="font-size:.68rem; font-weight:700; padding:.3em .8em; border-radius:20px; {{ $petStatusStyle }}">
                        {{ ucfirst($pet->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Info rows stagger ──
    document.querySelectorAll('#infoRows .info-row').forEach(row => {
        const delay = 650 + (parseInt(row.dataset.row) * 80);
        setTimeout(() => row.classList.add('visible'), delay);
    });

    // ── App rows stagger ──
    document.querySelectorAll('#appRows .list-row').forEach(row => {
        const delay = 450 + (parseInt(row.dataset.row) * 80);
        setTimeout(() => row.classList.add('visible'), delay);
    });

    // ── Pet rows stagger ──
    document.querySelectorAll('#petRows .list-row').forEach(row => {
        const delay = 600 + (parseInt(row.dataset.pet) * 80);
        setTimeout(() => row.classList.add('visible'), delay);
    });

});
</script>
@endpush