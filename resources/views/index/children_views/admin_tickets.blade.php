@extends('index.components.main_index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="text-white mb-0">My Tickets</h3>
        <p class="text-muted small">Report application issues or bugs to the developer</p>
    </div>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#reportIssueModal">
        <i class="bi bi-bug-fill me-1"></i> Report Issue
    </button>
</div>

<div class="card bg-dark border-secondary shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
    <thead class="table-cukur">
        <tr>
            <th class="ps-4">Ticket ID</th>
            <th>Issue</th>
            <th>Status</th>
            <th>Handled By</th>
            <th class="text-center">Reply</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tickets as $ticket)
        <tr>
            <td class="ps-4 text-warning fw-bold">{{ $ticket->ticket_id }}</td>
            <td>
                <div class="text-white fw-bold">{{ $ticket->subject }}</div>
                <div class="extra-small text-muted">{{ $ticket->created_at->format('d M, H:i') }}</div>
            </td>
            <td>
                @if($ticket->status == 'Open')
                    <span class="badge border border-warning text-light px-3">OPEN</span>
                @elseif($ticket->status == 'Progress')
                    <span class="badge border border-warning text-info px-3">PROGRESS</span>
                @else
                    <span class="badge border border-warning text-warning px-3">CLOSED</span>
                @endif
            </td>
            <td>
                @if($ticket->handled_by)
                    <div class="d-flex flex-column">
                        <span class="text-white small fw-bold">
                            <i class="bi bi-person-check-fill text-warning me-1"></i> {{ $ticket->handled_by }}
                        </span>
                        <span class="text-muted" style="font-size: 0.75rem;">
                            {{ $ticket->updated_at->diffForHumans() }}
                        </span>
                    </div>
                @else
                    <span class="text-muted italic small">Waiting for developer pick up ...</span>
                @endif
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-info" 
                        data-bs-toggle="popover" 
                        data-bs-trigger="focus"
                        title="Dev Response" 
                        data-bs-content="{{ $ticket->message ?? 'No reply yet.' }}">
                    <i class="bi bi-chat-dots"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
        </div>
    </div>
</div>

{{-- MODAL: REPORT ISSUE --}}
<div class="modal fade" id="reportIssueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary text-white">
                <h5 class="modal-title">Report New Issue</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tickets.store') }}" method="POST">
                @csrf
                <div class="modal-body text-white">
                    <div class="mb-3">
                        <label class="form-label small">Subject</label>
                        <input type="text" name="subject" class="form-control bg-dark text-white border-secondary shadow-inset" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Description</label>
                        <textarea name="message" rows="4" class="form-control bg-dark text-white border-secondary shadow-inset" placeholder="Please describe the problem..." required></textarea>
                    </div>
                    {{-- Hidden: Force app_issue type --}}
                    <input type="hidden" name="type" value="app_issue">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">SUBMIT REPORT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize popovers for Dev Replies
    document.addEventListener('DOMContentLoaded', function () {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
    })
</script>

<style>
    .shadow-inset { box-shadow: inset 0 2px 4px rgba(0,0,0,0.5); }
    .table-cukur th { background-color: #1a1a1a; font-size: 0.8rem; text-transform: uppercase; color: #6c757d; }
</style>
@endsection