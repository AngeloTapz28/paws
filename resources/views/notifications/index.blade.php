@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-bell me-2"></i>All Notifications</span>
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-outline-primary">Mark All Read</button>
        </form>
    </div>
    <div class="list-group list-group-flush">
        @forelse($notifications as $notif)
        <div class="list-group-item list-group-item-action {{ !$notif->isRead() ? 'bg-light' : '' }} py-3">
            <div class="d-flex gap-3 align-items-start">
                <div class="flex-shrink-0">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                         style="width:42px;height:42px;background:var(--bs-{{ $notif->type }}-bg-subtle, #EFF6FF);">
                        <i class="bi {{ $notif->icon ?? 'bi-bell' }} text-{{ $notif->type }}" style="font-size:1.2rem;"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0 fw-{{ !$notif->isRead() ? 'bold' : 'semibold' }}">{{ $notif->title }}</h6>
                        <small class="text-muted ms-3 flex-shrink-0">{{ $notif->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="text-muted mb-1 mt-1" style="font-size:.875rem;">{{ $notif->message }}</p>
                    @if($notif->link)
                    <a href="{{ $notif->link }}" class="text-primary" style="font-size:.8rem;">
                        View Details <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    @endif
                </div>
                @if(!$notif->isRead())
                <div class="flex-shrink-0 mt-1">
                    <div class="rounded-circle bg-primary" style="width:8px;height:8px;"></div>
                </div>
                @endif
            </div>
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
    <div class="card-footer">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection