@php $user = auth()->user(); @endphp

{{-- ─── SHARED (all roles) ─── --}}
<div class="nav-section-label">Main</div>
<a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
    Dashboard
    <i class="bi bi-chevron-right nav-chevron"></i>
</a>

{{-- ─── ADMIN MENU ─── --}}
@if($user->isAdmin())
    <div class="nav-section-label">Management</div>

    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-people"></i></span>
        Users
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-tags"></i></span>
        Categories
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <a href="{{ route('admin.breeds.index') }}" class="nav-link {{ request()->routeIs('admin.breeds.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-diagram-3"></i></span>
        Breeds
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-clock-history"></i></span>
        Activity Logs
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <div class="nav-section-label">Operations</div>

    <a href="{{ route('admin.pets.index') }}" class="nav-link {{ request()->routeIs('admin.pets.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-heart"></i></span>
        All Pets
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span>
        Applications
        @php $pendingCount = \App\Models\AdoptionApplication::whereIn('status', ['submitted','under_review'])->count(); @endphp
        @if($pendingCount > 0)
            <span class="nav-badge">{{ $pendingCount }}</span>
        @else
            <i class="bi bi-chevron-right nav-chevron"></i>
        @endif
    </a>

    <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-cash-stack"></i></span>
        Payments
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-bar-chart"></i></span>
        Reports
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>
@endif

{{-- ─── STAFF MENU ─── --}}
@if($user->isStaff() && !$user->isAdmin())
    <div class="nav-section-label">Pet Management</div>

    <a href="{{ route('staff.pets.index') }}" class="nav-link {{ request()->routeIs('staff.pets.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-heart"></i></span>
        Pets
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <div class="nav-section-label">Applications</div>

    <a href="{{ route('staff.applications.index') }}" class="nav-link {{ request()->routeIs('staff.applications.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span>
        Applications
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <a href="{{ route('staff.payments.index') }}" class="nav-link {{ request()->routeIs('staff.payments.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-cash"></i></span>
        Payments
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>
@endif

{{-- ─── VET MENU ─── --}}
@if($user->isVet() && !$user->isAdmin())
    <div class="nav-section-label">Medical</div>

    <a href="{{ route('vet.pets.index') }}" class="nav-link {{ request()->routeIs('vet.pets.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-heart-pulse"></i></span>
        Pet Health Records
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>
@endif

{{-- ─── ADOPTER MENU ─── --}}
@if($user->isAdopter())
    <div class="nav-section-label">Adoption</div>

    <a href="{{ route('adopter.pets.index') }}" class="nav-link {{ request()->routeIs('adopter.pets.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-search-heart"></i></span>
        Browse Pets
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <a href="{{ route('adopter.applications.index') }}" class="nav-link {{ request()->routeIs('adopter.applications.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-file-earmark-check"></i></span>
        My Applications
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>

    <div class="nav-section-label">Account</div>

    <a href="{{ route('adopter.profile.edit') }}" class="nav-link {{ request()->routeIs('adopter.profile.*') ? 'active' : '' }}">
        <span class="nav-icon"><i class="bi bi-person"></i></span>
        My Profile
        <i class="bi bi-chevron-right nav-chevron"></i>
    </a>
@endif

{{-- ─── SHARED BOTTOM ─── --}}
<div class="nav-section-label">System</div>
<a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
    <span class="nav-icon"><i class="bi bi-bell"></i></span>
    Notifications
    @php $unread = auth()->user()->unread_notifications_count; @endphp
    @if($unread > 0)
        <span class="nav-badge">{{ $unread }}</span>
    @else
        <i class="bi bi-chevron-right nav-chevron"></i>
    @endif
</a>