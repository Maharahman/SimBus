@extends('index.components.main_index')

@section('content')
<div class="container-fluid p-0">
    {{-- Header Section --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3 class="text-warning fw-bold mb-1">
                {{ strtoupper(Auth::user()->level) }}'S DASHBOARD
            </h3>
            <p class="text-muted mb-0">System performance and operational overview.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="text-muted small">Current Session:</div>
            <div class="text-warning fw-bold">{{ now()->format('d M Y | H:i') }}</div>
        </div>
    </div>

    {{-- Role-Based Conditional View --}}
    @if(Auth::user()->level === 'developer')
        @include('index.components.dev_dashboard')
    @endif
</div>
@endsection