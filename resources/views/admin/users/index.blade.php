@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-person-plus me-1"></i> Add User
    </a>
@endsection

@push('styles')
<style>
    .filter-bar {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-sm);
    }
    .user-avatar-cell {
        width: 38px; height: 38px; border-radius: 50%;
        object-fit: cover; border: 2px solid var(--border);
        flex-shrink: 0;
    }
    .user-avatar-fallback {
        width: 38px; height: 38px; border-radius: 50%;
        background: var(--coral-light); color: var(--coral);
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 700; flex-shrink: 0;
    }
    .role-chip {
        font-size: .68rem; font-weight: 600; padding: .25em .7em;
        border-radius: 20px; display: inline-block;
    }
    .status-chip {
        display: inline-flex; align-items: center; gap: .3rem;
        font-size: .72rem; font-weight: 600; padding: .3em .75em;
        border-radius: 20px;
    }
    .status-chip .dot { width: 6px; height: 6px; border-radius: 50%; }
    .chip-active   { background: var(--sage-light);  color: #2D5A3D; }
    .chip-inactive { background: #F3F4F6; color: #6B7280; }
    .chip-suspended { background: #FEF0EE; color: #8B2516; }
    .action-btn {
        width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--border);
        background: var(--white); display: inline-flex; align-items: center; justify-content: center;
        font-size: .85rem; color: var(--muted); text-decoration: none;
        transition: all .15s;
    }
    .action-btn:hover { background: var(--coral-light); color: var(--coral); border-color: var(--coral-light); }
    .action-btn.danger:hover { background: #FEF0EE; color: #8B2516; border-color: #FEF0EE; }
    .search-wrap { position: relative; }
    .search-wrap .bi-search { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: .85rem; pointer-events: none; }
    .search-wrap input { padding-left: 2.1rem; }
</style>
@endpush

@section('content')

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4 col-lg-5">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Search by name or email…">
            </div>
        </div>
        <div class="col-md-3 col-lg-2">
            <select name="role" class="form-select form-select-sm">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected(request('role') == $role->id)>{{ $role->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="active"    @selected(request('status') === 'active')>Active</option>
                <option value="inactive"  @selected(request('status') === 'inactive')>Inactive</option>
                <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Table Card --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding: 1rem 1.25rem;">
        <div>
            <h6 class="mb-0 fw-bold" style="color:var(--navy);">All Users</h6>
            <p class="mb-0 mt-1" style="font-size:.75rem; color:var(--muted);">
                {{ $users->total() }} total {{ Str::plural('user', $users->total()) }}
            </p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0" style="border-collapse:separate; border-spacing:0;">
            <thead>
                <tr>
                    <th style="padding-left:1.25rem;">User</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th style="text-align:right; padding-right:1.25rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="padding-left:1.25rem;">
                        <div class="d-flex align-items-center gap-2">
                            @if($user->avatar)
                                <img src="{{ $user->avatar_url }}" class="user-avatar-cell" alt="">
                            @else
                                <div class="user-avatar-fallback">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                            @endif
                            <div>
                                <div class="fw-semibold" style="font-size:.875rem; color:var(--navy);">{{ $user->name }}</div>
                                <div style="font-size:.75rem; color:var(--muted);">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="role-chip" style="background:var(--coral-subtle); color:var(--coral);">
                                {{ $role->display_name }}
                            </span>
                        @endforeach
                    </td>
                    <td style="font-size:.83rem; color:var(--muted);">{{ $user->phone ?? '—' }}</td>
                    <td>
                        @php $st = $user->status ?? 'active'; @endphp
                        <span class="status-chip chip-{{ $st }}">
                            <span class="dot" style="background: {{ $st === 'active' ? 'var(--sage)' : ($st === 'suspended' ? '#C0392B' : '#9CA3AF') }};"></span>
                            {{ ucfirst($st) }}
                        </span>
                    </td>
                    <td style="font-size:.8rem; color:var(--muted);">{{ $user->created_at->format('M d, Y') }}</td>
                    <td style="text-align:right; padding-right:1.25rem;">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.users.show', $user) }}" class="action-btn" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="action-btn" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="action-btn danger" title="Delete" type="submit">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state py-5">
                            <span class="empty-icon">👤</span>
                            <h5>No Users Found</h5>
                            <p>Try adjusting your filters or add a new user.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex justify-content-end" style="background:var(--white);">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection