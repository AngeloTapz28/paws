@extends('layouts.app')
@section('title', 'Categories & Breeds')
@section('page-title', 'Categories & Breeds')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Categories & Breeds</li>
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

    /* ── Category chip (for breeds table) ── */
    .cat-chip {
        font-size: .7rem; font-weight: 600; padding: .22em .65em;
        border-radius: 20px; background: rgba(45,49,71,.07); color: var(--navy);
        display: inline-block;
    }

    /* ── Row hover ── */
    .cat-row, .breed-row { transition: background .12s; }
    .cat-row:hover td, .breed-row:hover td { background: var(--coral-subtle); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-22px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(22px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes rowSlideIn {
        from { opacity: 0; transform: translateX(-14px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes rowSlideInRight {
        from { opacity: 0; transform: translateX(14px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* Panel cards */
    .panel-categories { opacity: 0; animation: slideInLeft  .45s ease .1s both; }
    .panel-breeds     { opacity: 0; animation: slideInRight .45s ease .2s both; }

    /* Table headers */
    .panel-categories thead tr { opacity: 0; animation: fadeDown .35s ease .35s both; }
    .panel-breeds     thead tr { opacity: 0; animation: fadeDown .35s ease .45s both; }

    /* Rows start hidden — JS staggers them */
    .cat-row   { opacity: 0; }
    .breed-row { opacity: 0; }
    .cat-row.visible   { animation: rowSlideIn      .38s ease both; }
    .breed-row.visible { animation: rowSlideInRight .38s ease both; }
</style>
@endpush

@section('content')

<div class="row g-4">

    {{-- ═══════════════════════ CATEGORIES ═══════════════════════ --}}
    <div class="col-lg-5">
        <div class="card panel-categories">
            <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
                <div>
                    <h6 class="mb-0 fw-bold" style="color:var(--navy);">
                        <i class="bi bi-grid me-2" style="color:var(--coral);"></i>Pet Categories
                    </h6>
                </div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Category
                </button>
            </div>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="padding-left:1.25rem;">Icon</th>
                            <th>Name</th>
                            <th>Breeds</th>
                            <th>Pets</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $i => $cat)
                        <tr class="cat-row" data-index="{{ $i }}">
                            <td style="padding-left:1.25rem;">
                                <i class="bi {{ $cat->icon ?? 'bi-tag' }}" style="font-size:1.1rem; color:var(--coral);"></i>
                            </td>
                            <td style="font-weight:600; font-size:.875rem; color:var(--navy);">{{ $cat->name }}</td>
                            <td style="font-size:.83rem; color:var(--muted);">{{ $cat->breeds_count }}</td>
                            <td style="font-size:.83rem; color:var(--muted);">{{ $cat->pets_count }}</td>
                            <td>
                                <span class="status-chip {{ $cat->is_active ? 'chip-active' : 'chip-inactive' }}">
                                    {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="text-align:right; padding-right:1rem;">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="action-btn" title="Edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal{{ $cat->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $cat) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete {{ addslashes($cat->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button class="action-btn danger" title="Delete" type="submit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state py-4">
                                    <span class="empty-icon">🏷️</span>
                                    <h5>No Categories Yet</h5>
                                    <p>Add your first pet category above.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($categories, 'hasPages') && $categories->hasPages())
            <div class="card-footer d-flex justify-content-end" style="background:var(--white);">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════ BREEDS ═══════════════════════ --}}
    <div class="col-lg-7">
        <div class="card panel-breeds">
            <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
                <div>
                    <h6 class="mb-0 fw-bold" style="color:var(--navy);">
                        <i class="bi bi-list-ul me-2" style="color:var(--coral);"></i>Breeds
                    </h6>
                </div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBreedModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Breed
                </button>
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
                                    {{ $breed->petCategory?->icon ?? '' }} {{ $breed->petCategory?->name ?? '—' }}
                                </span>
                            </td>
                            <td style="font-size:.83rem; color:var(--muted);">{{ $breed->pets_count ?? 0 }}</td>
                            <td>
                                <span class="status-chip {{ $breed->is_active ? 'chip-active' : 'chip-inactive' }}">
                                    {{ $breed->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="text-align:right; padding-right:1rem;">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="action-btn" title="Edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBreedModal{{ $breed->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.breeds.destroy', $breed) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete {{ addslashes($breed->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button class="action-btn danger" title="Delete" type="submit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state py-4">
                                    <span class="empty-icon">🐾</span>
                                    <h5>No Breeds Yet</h5>
                                    <p>Add your first breed above.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($breeds, 'hasPages') && $breeds->hasPages())
            <div class="card-footer d-flex justify-content-end" style="background:var(--white);">
                {{ $breeds->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ═══════════ ADD CATEGORY MODAL ═══════════ --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--border); padding:1.25rem;">
                    <h5 class="modal-title fw-bold" style="color:var(--navy);">
                        <i class="bi bi-plus-circle me-2" style="color:var(--coral);"></i>Add Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.25rem;">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Dog" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bootstrap Icon Class</label>
                        <input type="text" name="icon" class="form-control" placeholder="bi-emoji-smile" value="bi-tag">
                        <div class="form-text">Use any <a href="https://icons.getbootstrap.com" target="_blank">Bootstrap Icon</a> class.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="catActive" value="1" checked>
                        <label class="form-check-label" for="catActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border); padding:1rem 1.25rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════ EDIT CATEGORY MODALS ═══════════ --}}
@foreach($categories as $cat)
<div class="modal fade" id="editCategoryModal{{ $cat->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
            <form action="{{ route('admin.categories.update', $cat) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header" style="border-bottom:1px solid var(--border); padding:1.25rem;">
                    <h5 class="modal-title fw-bold" style="color:var(--navy);">
                        <i class="bi bi-pencil me-2" style="color:var(--coral);"></i>Edit Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.25rem;">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bootstrap Icon Class</label>
                        <input type="text" name="icon" class="form-control" value="{{ $cat->icon }}">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="catActive{{ $cat->id }}" value="1" @checked($cat->is_active)>
                        <label class="form-check-label" for="catActive{{ $cat->id }}">Active</label>
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
@endforeach

{{-- ═══════════ ADD BREED MODAL ═══════════ --}}
<div class="modal fade" id="addBreedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
            <form action="{{ route('admin.breeds.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--border); padding:1.25rem;">
                    <h5 class="modal-title fw-bold" style="color:var(--navy);">
                        <i class="bi bi-plus-circle me-2" style="color:var(--coral);"></i>Add Breed
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.25rem;">
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="pet_category_id" class="form-select" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Breed Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Labrador Retriever" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="breedActive" value="1" checked>
                        <label class="form-check-label" for="breedActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border); padding:1rem 1.25rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add Breed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════ EDIT BREED MODALS ═══════════ --}}
@foreach($breeds as $breed)
<div class="modal fade" id="editBreedModal{{ $breed->id }}" tabindex="-1">
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
                                <option value="{{ $cat->id }}" @selected($breed->pet_category_id == $cat->id)>
                                    {{ $cat->icon }} {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Breed Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $breed->name }}" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="breedActive{{ $breed->id }}" value="1" @checked($breed->is_active)>
                        <label class="form-check-label" for="breedActive{{ $breed->id }}">Active</label>
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
@endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Category rows slide in from left ──
    document.querySelectorAll('.cat-row').forEach(row => {
        const delay = 400 + (parseInt(row.dataset.index) * 60);
        setTimeout(() => row.classList.add('visible'), delay);
    });

    // ── Breed rows slide in from right, slightly later ──
    document.querySelectorAll('.breed-row').forEach(row => {
        const delay = 500 + (parseInt(row.dataset.index) * 45);
        setTimeout(() => row.classList.add('visible'), delay);
    });

});
</script>
@endpush