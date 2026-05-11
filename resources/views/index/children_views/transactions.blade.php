@extends('index.components.main_index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white">Receipts & Sales</h3>
</div>

<div class="card bg-dark border-secondary shadow">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="table-cukur">
                    <tr>
                        <th class="ps-4">DATE</th>
                        <th>SUMMARY</th>
                        <th>GRAND TOTAL</th>
                        <th>CREW</th>
                        <th class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $trx)
                    <tr>
                        <td class="ps-4 small">{{ \Carbon\Carbon::parse($trx->date)->format('d M, H:i') }}</td>
                        <td>
                            <span class="text-muted small">
                                {{ $trx->details->count() }} line items
                            </span>
                        </td>
                        <td class="text-info fw-bold">Rp {{ number_format($trx->total_amount) }}</td>
                        <td><span class="text-warning fw-bold">{{ $trx->crew->sur_name ?? 'System' }}</span></td>
                        <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $trx->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button class="btn btn-sm btn-outline-secondary btn-delete-trigger" 
                                        data-id="{{ $trx->id }}" 
                                        data-name="Receipt #{{ $trx->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                        </td>
                    </tr>
                    
                    <tr class="collapse bg-black" id="details-{{ $trx->id }}">
                        <td colspan="5" class="p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-warning border-bottom border-secondary pb-1 small fw-bold">RECEIPT BREAKDOWN</h6>
                                    <table class="table table-sm table-borderless text-white-50 mb-0 small">
                                        @foreach($trx->details as $detail)
                                        <tr>
                                            <td>
                                                <i class="bi bi-dot text-warning"></i>
                                                @if($detail->item_type == 'service')
                                                    {{ $detail->serviceDetail->name ?? 'Service' }}
                                                @else
                                                    {{ $detail->inventoryItem->item_name ?? 'Product' }}
                                                @endif
                                            </td>
                                            <td>x{{ $detail->quantity }}</td>
                                            <td class="text-end text-white">Rp {{ number_format($detail->price_at_time * $detail->quantity) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="border-top border-secondary">
                                            <td colspan="2" class="fw-bold text-warning">TOTAL</td>
                                            <td class="text-end fw-bold text-warning">Rp {{ number_format($trx->total_amount) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>    
    </div>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#receiptModal">
                <i class="bi bi-receipt me-1"></i> Create New Receipt
    </button>
</div>

<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-lg border-warning">
        <div class="modal-content bg-dark text-white">
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-warning"><i class="bi bi-cart-check me-2"></i>New Transaction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Services</label>
                        <div id="services-container">
                            <div class="row g-2 mb-2 service-row">
                                <div class="col-md-7">
                                    <select name="services[0][id]" class="form-select bg-dark text-white border-secondary select-price" onchange="updateRowPrice(this)" required>
                                        <option value="" data-price="0">Select Service...</option>
                                        @foreach($services as $s)
                                            <option value="{{ $s->id }}" data-price="{{ $s->price }}">{{ $s->name }} (Rp {{ number_format($s->price) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="services[0][qty]" class="form-control bg-dark text-white border-secondary qty-input" oninput="updateRowPrice(this)" value="1" min="1">
                                </div>
                                <div class="col-md-3 d-flex align-items-center justify-content-between">
                                    <span class="text-success fw-bold row-total">Rp 0</span>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="addRow('service')"><i class="bi bi-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Products / Add-ons</label>
                        <div id="items-container"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="addRow('item')">
                            <i class="bi bi-plus-circle me-1"></i> Add Product
                        </button>
                    </div>

                    <div class="mt-4 p-3 rounded-3" style="background: rgba(0,0,0,0.3); border: 1px dashed #444;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">TOTAL TO PAY</span>
                            <h3 class="text-warning mb-0 fw-black" id="grand-total-display">Rp 0</h3>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-warning w-100 fw-bold py-3 text-dark shadow-sm">
                        COMPLETE & SAVE RECEIPT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-dark border-danger">
            <div class="modal-body text-center text-white py-4">
                <p>Delete <strong id="delete_name" class="text-warning"></strong>?</p>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
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
let serviceCount = 1;
let itemCount = 0;

function addRow(type) {
    if (type === 'service') {
        const container = document.getElementById('services-container');
        const html = `
            <div class="row g-2 mb-2">
                <div class="col-md-7">
                    <select name="services[${serviceCount}][id]" class="form-select bg-dark text-white border-secondary select-price" onchange="updateRowPrice(this)" required>
                        <option value="" data-price="0">Select Service...</option>
                        @foreach($services as $s) <option value="{{ $s->id }}" data-price="{{ $s->price }}">{{ $s->name }} (Rp {{ number_format($s->price) }})</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="services[${serviceCount}][qty]" class="form-control bg-dark text-white border-secondary qty-input" oninput="updateRowPrice(this)" value="1" min="1">
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-between">
                    <span class="text-success fw-bold row-total">Rp 0</span>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.row').remove(); calculateGrandTotal();"><i class="bi bi-dash text-danger"></i></button>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        serviceCount++;
    } else {
        const container = document.getElementById('items-container');
        const html = `
            <div class="row g-2 mb-2">
                <div class="col-md-7">
                    <select name="items[${itemCount}][id]" class="form-select bg-dark text-white border-secondary select-price" onchange="updateRowPrice(this)" required>
                        <option value="" data-price="0">Select Product...</option>
                        @foreach($items as $i) <option value="{{ $i->id }}" data-price="{{ $i->item_price }}">{{ $i->item_name }}, STOCK : {{ $i->stock }}pcs  @(Rp {{ number_format($i->item_price, 0, ',', '.') }})</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${itemCount}][qty]" class="form-control bg-dark text-white border-secondary qty-input" oninput="updateRowPrice(this)" value="1" min="1">
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-between">
                    <span class="text-success fw-bold row-total">Rp 0</span>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.row').remove(); calculateGrandTotal();"><i class="bi bi-dash text-danger"></i></button>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        itemCount++;
    }
}

function updateRowPrice(element) {
    const row = element.closest('.row');
    const select = row.querySelector('.select-price');
    const selectedOption = select.options[select.selectedIndex];
    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const total = price * qty;
    row.querySelector('.row-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.row-total').forEach(el => {
        const val = parseInt(el.innerText.replace(/[^0-9]/g, '')) || 0;
        grandTotal += val;
    });
    document.getElementById('grand-total-display').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.getElementById('deleteForm');
    const deleteName = document.getElementById('delete_name');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));

    document.querySelectorAll('.btn-delete-trigger').forEach(btn => {
        btn.onclick = function() {
            const id = this.dataset.id;
            deleteName.innerText = this.dataset.name;
            deleteForm.action = '/admin/transactions/' + id; 
            deleteModal.show();
        };
    });
});
</script>
@endsection