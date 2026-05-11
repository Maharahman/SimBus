@extends('index.components.main_index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white">Service Management</h3>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#addServiceModal">
        <i class="bi bi-command me-1"></i> Add New Service
    </button>
</div>

<div class="card bg-dark border-secondary shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-cukur">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Price</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td class="ps-4 fw-bold text-warning">{{ $service->name }}</td>
                        <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-warning me-1 edit-service-btn" 
                                    data-id="{{ $service->id }}"
                                    data-name="{{ $service->name }}"
                                    data-price="{{ $service->price }}"
                                    data-duration="{{ $service->duration }}">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-outline-secondary delete-service-btn"
                                    data-id="{{ $service->id }}"
                                    data-name="{{ $service->name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary text-white">
                <h5 class="modal-title">Add New Service</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('services.store') }}" method="POST">
                @csrf
                <div class="modal-body text-white">
                    <div class="mb-3">
                        <label class="form-label">Service Name</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price (Rp)</label>
                            <input type="number" name="price" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold">Save Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary text-white">
                <h5 class="modal-title">Edit Service</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body text-white">
                    <div class="mb-3">
                        <label class="form-label">Service Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price (Rp)</label>
                            <input type="number" name="price" id="edit_price" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-dark border-danger border-opacity-50">
            <div class="modal-header border-secondary text-white">
                <h5 class="modal-title"><i class="bi bi-trash text-danger me-2"></i>Delete?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center text-white py-4">
                <p>Delete <strong id="delete_name" class="text-warning"></strong>?</p>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm px-4">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize Bootstrap Modals
    const editModal = new bootstrap.Modal(document.getElementById('editServiceModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteServiceModal'));

    // 2. Edit Logic
    document.querySelectorAll('.edit-service-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('editForm').action = '/admin/services/' + this.dataset.id;
            editModal.show();
        });
    });

    // 3. Delete Logic
    document.querySelectorAll('.delete-service-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('delete_name').innerText = this.dataset.name;
            document.getElementById('deleteForm').action = '/admin/services/' + this.dataset.id;
            deleteModal.show();
        });
    });     });
</script>
@endsection