@extends('index.components.main_index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white">Inventory Management</h3>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#addItemModal">
        <i class="bi bi-box-seam me-1"></i> Add New Item
    </button>
</div>

<div class="card bg-dark border-secondary shadow">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="table-cukur">
                    <tr>
                        <th class="ps-4">ITEM DESCRIPTION</th>
                        <th>SELLING PRICE (PER-ITEM)</th>
                        <th>STOCK</th>
                        <th>LAST UPDATE</th>
                        <th>BY CREW</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $item->item_name }}</td>
                        <td>Rp {{ number_format($item->item_price, 0, ',', '.') }}</td>
                        <td class="text-info fs-5">{{ $item->stock }}</td>
                        <td class="small text-muted">{{ $item->updated_at->format('d/m/y H:i') }}</td>
                        <td>
                            <span class="text-warning fw-bold">
                                {{ $item->editor->sur_name ?? 'System' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editItem{{ $item->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-delete-trigger"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->item_name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="editItem{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark border-warning text-white">
                                <form action="{{ route('inventory.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="small text-warning">ITEM NAME</label>
                                            <input type="text" name="item_name" class="form-control bg-dark text-white border-secondary" value="{{ $item->item_name }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-warning">SELLING PRICE (Rp)</label>
                                            <input type="number" name="item_price" class="form-control bg-dark text-white border-secondary" value="{{ $item->item_price }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-warning">STOCK</label>
                                            <input type="number" name="stock" class="form-control bg-dark text-white border-secondary" value="{{ $item->stock }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-secondary">
                                        <button type="submit" class="btn btn-warning w-100 fw-bold">UPDATE ITEM</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-warning text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">New Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small text-warning">ITEM NAME</label>
                        <input type="text" name="item_name" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-3">
                        <label class="small text-warning">@ SELLING PRICE</label>
                        <input type="number" name="item_price" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-3">
                        <label class="small text-warning">INITIAL STOCK</label>
                        <input type="number" name="stock" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-warning w-100 fw-bold text-dark">SAVE ITEM</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
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
document.querySelectorAll('.btn-delete-trigger').forEach(btn => {
    btn.onclick = function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        
        document.getElementById('delete_name').innerText = name;
        
        // Use a relative path from the root
        document.getElementById('deleteForm').action = '/admin/inventory/' + id; 
        
        // Make sure this matches the ID of your modal
        const myModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
        myModal.show();
    };
});
</script>
@endsection