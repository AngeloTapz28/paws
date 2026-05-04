@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@push('styles')
<style>
    /* ── Notification item ── */
    .notif-item {
        display: flex; gap: 1rem; align-items: flex-start;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        transition: background .15s, transform .15s;
        position: relative;
    }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: var(--coral-subtle); }
    .notif-item.unread { background: var(--coral-subtle); }
    .notif-item.unread:hover { background: #F5EBE5; }

    /* ── Unread dot ── */
    .unread-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--coral); flex-shrink: 0; margin-top: .45rem;
        animation: dotPulse 2s ease-in-out infinite;
    }
    @keyframes dotPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .6; transform: scale(.8); }
    }

    /* ── Icon circle ── */
    .notif-icon-wrap {
        width: 44px; height: 44px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem; flex-shrink: 0;
        transition: transform .2s;
    }
    .notif-item:hover .notif-icon-wrap { transform: scale(1.1); }

    /* ── Text ── */
    .notif-title {
        font-size: .875rem; font-weight: 700; color: var(--navy);
        margin-bottom: .2rem; line-height: 1.3;
    }
    .notif-item:not(.unread) .notif-title { font-weight: 600; }
    .notif-message { font-size: .8rem; color: var(--muted); margin-bottom: .3rem; line-height: 1.5; }
    .notif-time    { font-size: .73rem; color: var(--muted); }
    .notif-link    { font-size: .78rem; color: var(--coral); text-decoration: none; transition: color .15s; }
    .notif-link:hover { color: var(--coral-dark); text-decoration: underline; }

    /* ── Mark all read button ── */
    .btn-mark-all {
        font-size: .8rem; font-weight: 600; padding: .4rem 1rem;
        border-radius: 20px; border: 1.5px solid var(--coral);
        color: var(--coral); background: transparent;
        transition: all .15s; cursor: pointer;
    }
    .btn-mark-all:hover { background: var(--coral); color: #fff; }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInRow {
        from { opacity: 0; transform: translateX(-18px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* Card header */
    .notif-card { opacity: 0; animation: fadeDown .4s ease .1s both; }

    /* Notification items — JS staggers */
    .notif-item { opacity: 0; }
    .notif-item.visible { animation: slideInRow .4s ease both; }
</style>
@endpush

@section('content')

<div class="card notif-card">

    {{-- Header ── --}}
    <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
        <div>
            <h6 class="mb-0 fw-bold" style="color:var(--navy);">
                <i class="bi bi-bell me-2" style="color:var(--coral);"></i>All Notifications
            </h6>
            @php $unreadCount = $notifications->where('read_at', null)->count(); @endphp
            @if($unreadCount > 0)
            <p class="mb-0 mt-1" style="font-size:.73rem; color:var(--muted);">
                {{ $unreadCount }} unread
            </p>
            @endif
        </div>
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button class="btn-mark-all" type="submit">
                <i class="bi bi-check2-all me-1"></i>Mark All Read
            </button>
        </form>
    </div>

    {{-- List ── --}}
    <div id="notifList">
        @forelse($notifications as $i => $notif)

        @php
            // Icon background & color based on notification type
            $typeStyles = [
                'success' => ['bg' => 'var(--sage-light)',   'color' => '#2D5A3D'],
                'danger'  => ['bg' => '#FEF0EE',             'color' => '#8B2516'],
                'warning' => ['bg' => 'var(--gold-light)',   'color' => '#7A5A1A'],
                'info'    => ['bg' => 'rgba(45,49,71,.07)',  'color' => 'var(--navy)'],
            ];
            $ts    = $typeStyles[$notif->type ?? 'info'] ?? $typeStyles['info'];
            $isNew = !$notif->isRead();
        @endphp

        <div class="notif-item {{ $isNew ? 'unread' : '' }}" data-index="{{ $i }}">

            {{-- Icon ── --}}
            <div class="notif-icon-wrap" style="background:{{ $ts['bg'] }}; color:{{ $ts['color'] }};">
                <i class="bi {{ $notif->icon ?? 'bi-bell' }}"></i>
            </div>

            {{-- Content ── --}}
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="notif-title">{{ $notif->title }}</div>
                    <span class="notif-time flex-shrink-0">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
                <div class="notif-message">{{ $notif->message }}</div>
                @if($notif->link)
                <a href="{{ $notif->link }}" class="notif-link">
                    View Details <i class="bi bi-arrow-right ms-1" style="font-size:.7rem;"></i>
                </a>
                @endif
            </div>

            {{-- Unread indicator ── --}}
            @if($isNew)
            <div class="unread-dot"></div>
            @endif

        </div>

        @empty
        <div class="empty-state py-5">
            <span class="empty-icon">🔔</span>
            <h5>All Caught Up!</h5>
            <p>You have no notifications at the moment.</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="card-footer d-flex justify-content-end" style="background:var(--white);">
        {{ $notifications->links() }}
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Stagger notification items sliding in
    document.querySelectorAll('.notif-item').forEach(item => {
        const delay = 200 + (parseInt(item.dataset.index) * 65);
        setTimeout(() => item.classList.add('visible'), delay);
    });
});
</script>
@endpush