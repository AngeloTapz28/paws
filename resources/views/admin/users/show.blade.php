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

@section('content')

<div class="row g-4">

    {{-- Left: Profile Card --}}
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center
                            mx-auto mb-3 fw-bold"
                     style="width:80px;height:80px;font-size:2rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <div class="d-flex justify-content-center gap-1 flex-wrap mb-3">
                    @foreach($user->roles as $role)
                        <span class="badge bg-{{ $role->color ?? 'secondary' }}">
                            {{ $role->display_name }}
                        </span>
                    @endforeach
                </div>
                <span class="badge fs-6 bg-{{ match($user->status) {
                    'active'    => 'success',
                    'inactive'  => 'secondary',
                    'suspended' => 'danger',
                    default     => 'secondary',
                } }}">{{ ucfirst($user->status) }}</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-person-lines-fill me-2 text-primary"></i>Contact Info
            </div>
            <div class="card-body">
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
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted small">{{ $label }}</span>
                    <span class="small fw-medium">{{ $value }}</span>
                </div>
                @endforeach
                @if($user->address)
                <div class="pt-2">
                    <span class="text-muted small d-block">Address</span>
                    <span class="small">{{ $user->address }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Assign Role --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold">
                <i class="bi bi-shield-plus me-2 text-info"></i>Add Role
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.assignRole', $user) }}"
                      class="d-flex gap-2">
                    @csrf
                    <select name="role_id" class="form-select form-select-sm">
                        <option value="">— Select Role —</option>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-info btn-sm text-white flex-shrink-0">
                        Add
                    </button>
                </form>
                @if($user->notes)
                <div class="mt-3 p-2 rounded" style="background:#F8FAFC;font-size:.8rem;">
                    <strong>Notes:</strong> {{ $user->notes }}
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Right: Applications + Pets --}}
    <div class="col-lg-8">

        {{-- Adoption Applications --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header d-flex align-items-center justify-content-between fw-semibold">
                <span><i class="bi bi-file-earmark-text me-2 text-warning"></i>Adoption Applications</span>
                <span class="badge bg-secondary">{{ $user->adoptionApplications->count() }}</span>
            </div>
            <div class="card-body p-0">
                @forelse($user->adoptionApplications as $app)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    @if($app->pet)
                    <img src="{{ $app->pet->primary_image_url }}"
                         style="width:36px;height:36px;border-radius:8px;object-fit:cover;flex-shrink:0;">
                    @endif
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:.83rem;">
                            {{ $app->application_number }}
                        </div>
                        <div class="text-muted" style="font-size:.75rem;">
                            Pet: {{ $app->pet->name ?? '—' }} &bull;
                            {{ $app->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    <span class="badge bg-{{ $app->status_badge }}">{{ $app->status_label }}</span>
                    <a href="{{ route('admin.applications.show', $app) }}"
                       class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.75rem;">View</a>
                </div>
                @empty
                <div class="text-center py-3 text-muted small">No adoption applications.</div>
                @endforelse
            </div>
        </div>

        {{-- Pets Added (Staff) --}}
        @if($user->pets->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between fw-semibold">
                <span><i class="bi bi-paw me-2 text-primary"></i>Pets Added by This User</span>
                <span class="badge bg-secondary">{{ $user->pets->count() }}</span>
            </div>
            <div class="card-body p-0">
                @foreach($user->pets as $pet)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <img src="{{ $pet->primary_image_url }}"
                         style="width:36px;height:36px;border-radius:8px;object-fit:cover;flex-shrink:0;">
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:.83rem;">{{ $pet->name }}</div>
                        <div class="text-muted" style="font-size:.75rem;">
                            {{ $pet->category->name ?? '—' }} &bull;
                            Added {{ $pet->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    <span class="badge bg-{{ match($pet->status) {
                        'available' => 'success', 'adopted' => 'primary',
                        'pending'   => 'warning',  default  => 'secondary',
                    } }}">{{ ucfirst($pet->status) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection