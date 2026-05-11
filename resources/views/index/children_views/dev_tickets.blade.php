@extends('index.components.main_index')

@section('content')
    <div class="mb-4">
        <h3 class="text-white fw-bold">Developer Control Center</h3>
        <p class="text-muted">Manage system registrations and technical support tickets.</p>
    </div>

    {{-- Navigation Tabs --}}
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active text-uppercase fw-bold" id="pills-issue-tab" data-bs-toggle="pill"
                data-bs-target="#pills-issue" type="button">App Issues</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-uppercase fw-bold" id="pills-reg-tab" data-bs-toggle="pill"
                data-bs-target="#pills-reg" type="button">Registrations</button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">

        {{-- SECTION 1: APP ISSUES (SUPPORT) --}}
        <div class="tab-pane fade show active" id="pills-issue" role="tabpanel">
            <div class="card bg-dark border-secondary">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead class="table-cukur">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Reported By</th>
                                <th>Issue</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets->where('category', 'app_issue') as $issue)
                                <tr>
                                    <td class="ps-4 text-warning fw-bold">{{ $issue->ticket_id }}</td>
                                    <td class="text-warning fw-bold">{{ $issue->user->sur_name ?? 'Guest' }}</td>
                                    <td>
                                        <div class="text-white fw-bold">{{ $issue->subject }}</div>
                                        <div class="small text-muted">{{ Str::limit($issue->message, 40) }}</div>
                                    </td>
                                    <td>
                                        {{-- REVISED BADGE LOGIC --}}
                                        @if ($issue->status == 'Open')
                                            <span class="badge border border-warning text-light px-3">OPEN</span>
                                        @elseif($issue->status == 'Progress')
                                            <span class="badge border border-info text-info px-3">PROGRESS</span>
                                        @elseif($issue->status == 'Closed')
                                            <span class="badge border border-success text-success px-3">CLOSED</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                            data-bs-target="#replyModal{{ $issue->id }}">
                                            <i class="bi bi-reply-fill"></i> Handle
                                        </button>
                                    </td>
                                </tr>

                                {{-- REPLY/STATUS MODAL --}}
                                <div class="modal fade" id="replyModal{{ $issue->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark border-secondary text-white">
                                            <form action="{{ route('tickets.updateStatus', $issue->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title">Handle Ticket {{ $issue->ticket_id }}</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="small text-muted text-uppercase fw-bold">Reported Message:</label>
                                                        <div class="p-2 bg-black bg-opacity-25 rounded border border-secondary small italic mt-1 text-info">
                                                            "{{ $issue->message }}"
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="small fw-bold">CHANGE STATUS</label>
                                                        <select name="status" class="form-select bg-dark text-white border-secondary">
                                                            <option value="Open" {{ $issue->status == 'Open' ? 'selected' : '' }}>Open</option>
                                                            <option value="Progress" {{ $issue->status == 'Progress' ? 'selected' : '' }}>Pick Up (Progress)</option>
                                                            {{-- REVISED VALUE PASSING TO 'Closed' --}}
                                                            <option value="Closed" {{ $issue->status == 'Closed' ? 'selected' : '' }}>Closed (Resolved)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="small fw-bold">YOUR REPLY TO CREW</label>
                                                        <textarea name="dev_reply" rows="3" class="form-control bg-dark text-white border-secondary"
                                                            placeholder="Write your response here..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="submit" class="btn btn-warning w-100 fw-bold text-uppercase">Update & Stamp</button>
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

        {{-- SECTION 2: USER REGISTRATIONS --}}
        <div class="tab-pane fade" id="pills-reg" role="tabpanel">
            <div class="card bg-dark border-secondary">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead class="table-cukur">
                            <tr>
                                <th class="ps-4">Ticket ID</th>
                                <th>Candidate</th>
                                <th>Phone (Num)</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets->where('category', 'registration') as $reg)
                                @php $data = json_decode($reg->registration_data); @endphp
                                <tr>
                                    <td class="ps-4 text-warning fw-bold">{{ $reg->ticket_id }}</td>
                                    <td class="text-white">
                                        <div class="fw-bold">{{ $data->sur_name ?? 'Unknown' }}</div>
                                        <div class="extra-small text-muted">{{ $data->name ?? '' }}</div>
                                    </td>
                                    <td class="text-info">{{ $reg->user_num }}</td>
                                    <td>
                                        {{-- REVISED BADGE LOGIC FOR REGISTRATION --}}
                                        @if ($reg->status == 'Open')
                                            <span class="badge border border-warning text-light px-3">OPEN</span>
                                        @elseif($reg->status == 'Approve')
                                            <span class="badge border border-success text-success px-3">APPROVE</span>
                                        @elseif($reg->status == 'Reject')
                                            <span class="badge border border-danger text-danger px-3">REJECT</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($reg->status == 'Open')
                                            <button class="btn btn-sm btn-info fw-bold px-3 shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#approveModal{{ $reg->id }}">
                                                <i class="bi bi-search me-1"></i> REVIEW
                                            </button>
                                        @else
                                            <div class="small text-info italic">Processed by {{ $reg->handled_by }}</div>
                                        @endif
                                    </td>
                                </tr>

                                {{-- ENHANCED APPROVAL MODAL --}}
                                <div class="modal fade" id="approveModal{{ $reg->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark border-warning border-opacity-50">
                                            <form action="{{ route('tickets.approve', $reg->id) }}" method="POST" id="regForm{{ $reg->id }}">
                                                @csrf
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title text-white">Candidate Verification</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-white">
                                                    <div class="p-3 bg-black bg-opacity-50 rounded border border-secondary mb-3">
                                                        <div class="row mb-2">
                                                            <div class="col-4 text-muted small text-uppercase">Full Name</div>
                                                            <div class="col-8 fw-bold">{{ $data->name ?? '-' }}</div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-4 text-muted small text-uppercase">Nickname</div>
                                                            <div class="col-8 text-warning fw-bold">{{ $data->sur_name ?? '-' }}</div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4 text-muted small text-uppercase">Note</div>
                                                            <div class="col-8 italic small">"{{ $reg->message }}"</div>
                                                        </div>
                                                    </div>

                                                    {{-- REVISED HIDDEN DECISION VALUE --}}
                                                    <input type="hidden" name="decision" id="decision{{ $reg->id }}" value="approve">

                                                    <div id="accessLevelSection{{ $reg->id }}">
                                                        <label class="form-label small fw-bold text-muted uppercase">Set Access Level</label>
                                                        <select name="level" class="form-select bg-dark text-white border-secondary mb-3">
                                                            <option value="user">Crew</option>
                                                            <option value="admin">Admin</option>
                                                        </select>
                                                    </div>

                                                    <div id="rejectionReasonSection{{ $reg->id }}" class="d-none">
                                                        <label class="form-label small fw-bold text-danger uppercase">Rejection Reason</label>
                                                        <textarea name="reject_reason" class="form-control bg-dark text-white border-danger mb-3" rows="2"
                                                            placeholder="Why is this candidate being rejected?"></textarea>
                                                    </div>
                                                </div>

                                                <div class="modal-footer border-0 pt-0 d-flex gap-2">
                                                    <button type="button" class="btn btn-outline-danger fw-bold"
                                                        id="btnRejectToggle{{ $reg->id }}"
                                                        onclick="toggleRejection({{ $reg->id }})">REJECT</button>

                                                    <button type="submit" id="btnSubmit{{ $reg->id }}" class="btn btn-warning flex-grow-1 fw-bold">
                                                        GRANT ACCESS & APPROVE
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
            </div>
        </div>
    </div>

    <style>
        .nav-pills .nav-link { color: #6c757d; border: 1px solid transparent; margin-right: 5px; }
        .nav-pills .nav-link.active { background-color: #ffb82b; color: #000; }
        .table-cukur th { background-color: #1a1a1a; font-size: 0.8rem; text-transform: uppercase; color: #6c757d; }
        .bg-black { background-color: #000; }
        .extra-small { font-size: 0.75rem; }
    </style>

    <script>
        function toggleRejection(id) {
            const access = document.getElementById('accessLevelSection' + id);
            const reject = document.getElementById('rejectionReasonSection' + id);
            const decision = document.getElementById('decision' + id);
            const submit = document.getElementById('btnSubmit' + id);
            const toggle = document.getElementById('btnRejectToggle' + id);

            if (reject.classList.contains('d-none')) {
                // Change to REJECT Mode
                reject.classList.remove('d-none');
                access.classList.add('d-none');
                decision.value = 'reject'; // Passing 'reject' to Controller
                submit.innerText = 'CONFIRM REJECTION';
                submit.className = 'btn btn-danger flex-grow-1 fw-bold';
                toggle.innerText = 'CANCEL';
            } else {
                // Change back to APPROVE Mode
                reject.classList.add('d-none');
                access.classList.remove('d-none');
                decision.value = 'approve'; // Passing 'approve' to Controller
                submit.innerText = 'GRANT ACCESS & APPROVE';
                submit.className = 'btn btn-warning flex-grow-1 fw-bold';
                toggle.innerText = 'REJECT';
            }
        }
    </script>
@endsection