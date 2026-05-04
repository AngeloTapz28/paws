@extends('layouts.app')
@section('title', 'Breeds')
@section('page-title', 'Breed Management')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">Breeds</li>
@endsection

@push('styles')
<style>
    /* ── Action buttons ── */
    .action-btn {
        width: 30px; height: 30px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); background: var(--white);
        color: var(--muted); font-size: .82rem; text-decoration: none;
        transition: all .15s; cursor: pointer; padding: 0;
    }
    .action-btn:hover        { background: var(--coral-subtle); color: var(--coral); border-color: var(--coral-light); }
    .action-btn.danger:hover { background: #FEF0EE; color: #8B2516; border-color: #FECDD3; }

    /* ── Status chip ── */
    .status-chip {
        font-size: .68rem; font-weight: 700; padding: .28em .75em;
        border-radius: 20px; display: inline-block;
    }
    .chip-active   { background: var(--sage-light); color: #2D5A3D; }
    .chip-inactive { background: #F3F4F6; color: #6B7280; }

    /* ── Count badge ── */
    .count-badge {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 22px; border-radius: 50%;
        background: rgba(45,49,71,.08); color: var(--navy);
        font-size: .72rem; font-weight: 700;
    }

    /* ── Category chip ── */
    .cat-chip {
        font-size: .7rem; font-weight: 600; padding: .22em .65em;
        border-radius: 20px; background: rgba(45,49,71,.07); color: var(--navy);
        display: inline-flex; align-items: center; gap: .25rem;
    }

    /* ── Breed row hover ── */
    .breed-row { transition: background .12s; }
    .breed-row:hover td { background: var(--coral-subtle); }

    /* ── Add form focus style ── */
    .add-form-card .form-control:focus,
    .add-form-card .form-select:focus {
        border-color: var(--coral);
        box-shadow: 0 0 0 3px rgba(217,119,87,.15);
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-24px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(24px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes rowIn {
        from { opacity: 0; transform: translateX(16px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes formFieldIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Panel entrances */
    .panel-form  { opacity: 0; animation: slideInLeft  .45s ease .1s both; }
    .panel-table { opacity: 0; animation: slideInRight .45s ease .2s both; }

    /* Form fields stagger */
    .add-form-card .mb-3:nth-child(1) { opacity: 0; animation: formFieldIn .4s ease .35s both; }
    .add-form-card .mb-3:nth-child(2) { opacity: 0; animation: formFieldIn .4s ease .45s both; }
    .add-form-card .mb-3:nth-child(3) { opacity: 0; animation: formFieldIn .4s ease .55s both; }
    .add-form-card .btn-submit        { opacity: 0; animation: formFieldIn .4s ease .63s both; }

    /* Table header */
    .panel-table thead tr { opacity: 0; animation: fadeDown .35s ease .4s both; }

    /* Breed rows — start hidden, JS staggers them */
    .breed-row { opacity: 0; }
    .breed-row.visible { animation: rowIn .38s ease both; }

    /* Breeds count badge in header */
    .breeds-total-badge {
        animation: fadeDown .4s ease .5s both;
        opacity: 0;
    }
</style>
@endpush

@section('content')

<div class="row g-4">

    {{-- ═══════ ADD BREED FORM ═══════ --}}
    <div class="col-lg-4">
        <div class="card add-form-card panel-form">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">
                    <i class="bi bi-plus-circle me-2" style="color:var(--coral);"></i>Add New Breed
                </h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <form method="POST" action="{{ route('admin.breeds.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="pet_category_id"
                                class="form-select @error('pet_category_id') is-invalid @enderror" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('pet_category_id') == $cat->id)>
                                    {{ $cat->icon }} {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('pet_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Breed Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g. Labrador Retriever"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active"
                                   id="isActive" value="1" @checked(old('is_active', true))>
                            <label class="form-check-label" for="isActive" style="font-size:.875rem;">Active</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-submit">
                        <i class="bi bi-plus-circle me-1"></i>Add Breed
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════ BREEDS TABLE ═══════ --}}
    <div class="col-lg-8">
        <div class="card panel-table">
            <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">
                    <i class="bi bi-list-ul me-2" style="color:var(--coral);"></i>All Breeds
                </h6>
                <span class="badge breeds-total-badge"
                      style="background:rgba(45,49,71,.08); color:var(--navy); font-size:.75rem; padding:.4em .75em;">
                    {{ $breeds->total() }} breeds
                </span>
            </div>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="padding-left:1.25rem;">Breed</th>
                            <th>Category</th>
                            <th>Pets</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($breeds as $i => $breed)
                        <tr class="breed-row" data-index="{{ $i }}">
                            <td style="padding-left:1.25rem; font-weight:600; font-size:.875rem; color:var(--navy);">
                                {{ $breed->name }}
                            </td>
                            <td>
                                <span class="cat-chip">
                                    {{ $breed->petCategory?->icon ?? '' }}
                                    {{ $breed->petCategory?->name ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="count-badge">{{ $breed->pets_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="status-chip {{ $breed->is_active ? 'chip-active' : 'chip-inactive' }}">
                                    {{ $breed->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="text-align:right; padding-right:1rem;">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="action-btn" title="Edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBreed{{ $breed->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if(($breed->pets_count ?? 0) === 0)
                                    <form method="POST"
                                          action="{{ route('admin.breeds.destroy', $breed) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete breed: {{ addslashes($breed->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button class="action-btn danger" title="Delete" type="submit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="action-btn" title="Cannot delete — breed has pets" disabled
                                            style="opacity:.4; cursor:not-allowed;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Modal for this breed --}}
                        <div class="modal fade" id="editBreed{{ $breed->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
                                    <form action="{{ route('admin.breeds.update', $breed) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header" style="border-bottom:1px solid var(--border); padding:1.25rem;">
                                            <h5 class="modal-title fw-bold" style="color:var(--navy);">
                                                <i class="bi bi-pencil me-2" style="color:var(--coral);"></i>Edit Breed
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body" style="padding:1.25rem;">
                                            <div class="mb-3">
                                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                                <select name="pet_category_id" class="form-select" required>
                                                    <option value="">— Select Category —</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}"
                                                                @selected($breed->pet_category_id == $cat->id)>
                                                            {{ $cat->icon }} {{ $cat->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Breed Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control"
                                                       value="{{ $breed->name }}" required>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active"
                                                       id="editActive{{ $breed->id }}" value="1"
                                                       @checked($breed->is_active)>
                                                <label class="form-check-label" for="editActive{{ $breed->id }}">Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="border-top:1px solid var(--border); padding:1rem 1.25rem;">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-check-circle me-1"></i> Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state py-5">
                                    <span class="empty-icon">🐾</span>
                                    <h5>No Breeds Yet</h5>
                                    <p>Add your first breed using the form on the left.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($breeds->hasPages())
            <div class="card-footer d-flex justify-content-end" style="background:var(--white);">
                {{ $breeds->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Stagger breed rows sliding in from the right
    document.querySelectorAll('.breed-row').forEach(row => {
        const delay = 450 + (parseInt(row.dataset.index) * 50);
        setTimeout(() => row.classList.add('visible'), delay);
    });
});
</script>
@endpush