@extends('index.components.main_index')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">Service List</h3>
    </div>

    <div class="card bg-dark border-secondary shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-cukur ">
                        <tr>
                            <th class="ps-4 fw-bold">Name</th>
                            <th class="text-start fw-bold">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td class="ps-4 fw-bold text-warning">{{ $service->name }}</td>
                                <td class="text-start text-info fw-bold">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Initialize Bootstrap Modals
            const editModal = new bootstrap.Modal(document.getElementById('editServiceModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteServiceModal'));
        });
    </script>
@endsection
