@extends('index.components.main_index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white">Inventory View</h3>
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection