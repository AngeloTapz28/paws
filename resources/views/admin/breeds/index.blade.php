@extends('layouts.app')
@section('title', 'Breeds')
@section('page-title', 'Breed Management')

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.categories.index') }}">Categories</a>
    </li>
    <li class="breadcrumb-item active">Breeds</li>
@endsection

@section('content')

<div class="row g-4">

    {{-- Add Breed Form --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold">
                <i class="bi bi-plus-circle me-2 text-success"></i>Add New Breed
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.breeds.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="pet_category_id"
                                class="form-select @error('pet_category_id') is-invalid @enderror" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('pet_category_id') == $cat->id)>
                                    {{ $cat->icon }} {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('pet_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Breed Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g. Labrador Retriever" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active"
                                   id="isActive" value="1" @checked(old('is_active', true))>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle me-1"></i>Add Breed
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Breeds List --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between fw-semibold">
                <span><i class="bi bi-list-ul me-2 text-primary"></i>All Breeds</span>
                <span class="badge bg-secondary">{{ $breeds->total() }} breeds</span>
            </div>
            <div class="card-body p-0">
                @if($breeds->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-list-ul d-block fs-1 mb-2 opacity-25"></i>
                        <p class="mb-0">No breeds added yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Breed</th>
                                    <th>Category</th>
                                    <th class="text-center">Pets</th>
                                    <th class="text-center">Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($breeds as $breed)
                                <tr>
                                    <td class="ps-3 fw-medium">{{ $breed->name }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border" style="font-size:.75rem;">
                                            {{ $breed->petCategory->icon ?? '' }}
                                            {{ $breed->petCategory->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $breed->pets_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($breed->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editBreed{{ $breed->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if($breed->pets_count === 0)
                                            <form method="POST"
                                                  action="{{ route('admin.breeds.destroy', $breed) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete breed: {{ $breed->name }}?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-danger" disabled
                                                    title="Cannot delete — has {{ $breed->pets_count }} pet(s)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editBreed{{ $breed->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('admin.breeds.update', $breed) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h6 class="modal-title">Edit Breed</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Category</label>
                                                        <select name="pet_category_id" class="form-select form-select-sm" required>
                                                            @foreach($categories as $cat)
                                                                <option value="{{ $cat->id }}"
                                                                        @selected($breed->pet_category_id == $cat->id)>
                                                                    {{ $cat->icon }} {{ $cat->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Name</label>
                                                        <input type="text" name="name" class="form-control form-control-sm"
                                                               value="{{ $breed->name }}" required>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="is_active" value="1"
                                                               @checked($breed->is_active)>
                                                        <label class="form-check-label small">Active</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($breeds->hasPages())
                        <div class="px-3 py-2 border-top">{{ $breeds->links() }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>

</div>

@endsection