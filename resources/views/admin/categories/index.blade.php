@extends('layouts.app')
@section('title', 'Categories & Breeds')
@section('page-title', 'Categories & Breeds')

@section('content')
<div class="row g-4">
    {{-- ===== CATEGORIES ===== --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-grid me-2 text-primary"></i>Pet Categories</h6>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Category
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Icon</th><th>Name</th><th>Breeds</th><th>Pets</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                        <tr>
                            <td><i class="bi {{ $cat->icon }} fs-5 text-primary"></i></td>
                            <td class="fw-medium">{{ $cat->name }}</td>
                            <td>{{ $cat->breeds_count }}</td>
                            <td>{{ $cat->pets_count }}</td>
                            <td>
                                @if($cat->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal{{ $cat->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.categories.destroy', $cat) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Category Modal --}}
                        <div class="modal fade" id="editCategoryModal{{ $cat->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('admin.categories.update', $cat) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title fw-semibold">Edit Category</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Name</label>
                                                <input type="text" name="name" class="form-control"
                                                       value="{{ $cat->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Bootstrap Icon Class</label>
                                                <input type="text" name="icon" class="form-control"
                                                       value="{{ $cat->icon }}" placeholder="bi-tag">
                                                <div class="form-text">
                                                    Use Bootstrap Icons class e.g. <code>bi-heart</code>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active"
                                                       value="1" {{ $cat->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label">Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No categories yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ===== BREEDS ===== --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-list-ul me-2 text-success"></i>Breeds</h6>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addBreedModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Breed
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Breed</th><th>Category</th><th>Pets</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($breeds as $breed)
                        <tr>
                            <td class="fw-medium">{{ $breed->name }}</td>
                            <td><span class="badge bg-primary-subtle text-primary">{{ $breed->petCategory->name }}</span></td>
                            <td>{{ $breed->pets_count }}</td>
                            <td>
                                @if($breed->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editBreedModal{{ $breed->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.breeds.destroy', $breed) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this breed?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Breed Modal --}}
                        <div class="modal fade" id="editBreedModal{{ $breed->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('admin.breeds.update', $breed) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title fw-semibold">Edit Breed</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Category</label>
                                                <select name="pet_category_id" class="form-select" required>
                                                    @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ $breed->pet_category_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Breed Name</label>
                                                <input type="text" name="name" class="form-control"
                                                       value="{{ $breed->name }}" required>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active"
                                                       value="1" {{ $breed->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label">Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No breeds yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($breeds->hasPages())
            <div class="card-footer bg-white border-0">{{ $breeds->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Dogs" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bootstrap Icon Class</label>
                        <input type="text" name="icon" class="form-control" placeholder="bi-heart-fill">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Breed Modal --}}
<div class="modal fade" id="addBreedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.breeds.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Add Breed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="pet_category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Breed Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Labrador Retriever" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Breed</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection