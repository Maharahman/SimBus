@extends('index.components.main_index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white fw-bold">Crews Management</h3>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus-fill me-1"></i> Add New User
    </button>
</div>

<div class="card bg-dark border-secondary shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="table-cukur fw-bold">
                    <tr>
                        <th class="ps-4">Full Name</th>
                        <th class="text-center">Nickname</th>
                        <th class="text-center">Phone (Number)</th>
                        <th class="text-center">Crew Level</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                {{-- INITIAL EMBLEM LOGIC --}}
                                @if($user->profile_photo)
                                    <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                                         class="rounded-circle me-2 border border-secondary shadow-sm" 
                                         width="32" height="32" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle me-2 d-flex align-items-center justify-content-center shadow-sm"
                                         style="width: 32px; height: 32px; background-color: #ffb82b; border: 1px solid #000;">
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                            {{ strtoupper(substr($user->sur_name ?? $user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <span class="text-white">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-warning fw-bold">{{ $user->sur_name }}</td>
                        <td class="text-info fw-bold">{{ $user->num }}</td>
                        <td class="text-warning fw-bold">{{ strtoupper($user->level) }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-warning me-1 btn-edit-user" 
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                    data-surname="{{ $user->sur_name }}" data-num="{{ $user->num }}"
                                    data-level="{{ $user->level }}">
                                <i class="bi bi-pencil"></i>
                            </button>

                            {{-- Protection: Cannot delete other developers --}}
                            @if($user->level !== 'developer')
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-delete-trigger"
                                    data-id="{{ $user->id }}" data-name="{{ $user->sur_name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL: ADD USER --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary text-white">
                <h5 class="modal-title">New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body text-white">
                    <div class="mb-3"><label class="form-label small">Full Name</label><input type="text" name="name" class="form-control bg-dark text-white border-secondary shadow-inset" required></div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small">Nickname</label><input type="text" name="sur_name" class="form-control bg-dark text-white border-secondary shadow-inset" required></div>
                        <div class="col-6 mb-3">
                            <label class="form-label small">Level</label>
                            @if(Auth::user()->level === 'developer')
                                <div class="dropdown">
                                    <button class="btn btn-outline-warning dropdown-toggle w-100 text-start" type="button" id="addLevelDropdown" data-bs-toggle="dropdown">
                                        Select Level
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-dark border-secondary w-100">
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="setLevel('add', 'user', 'User')">User</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="setLevel('add', 'admin', 'Admin')">Admin</a></li>
                                    </ul>
                                    <input type="hidden" name="level" id="add_level_input" required>
                                </div>
                            @else
                                <input type="text" class="form-control bg-dark text-white-50 border-secondary" value="USER" readonly>
                                <input type="hidden" name="level" value="user">
                            @endif
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label small">Phone (Num)</label><input type="number" name="num" class="form-control bg-dark text-white border-secondary shadow-inset" required></div>
                    <div class="mb-3"><label class="form-label small">Password</label><input type="password" name="pass" class="form-control bg-dark text-white border-secondary shadow-inset" required></div>
                </div>
                <div class="modal-footer border-secondary border-0 pt-0">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">CREATE</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: EDIT USER --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary text-white">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body text-white">
                    <div class="mb-3"><label class="form-label small">Full Name</label><input type="text" name="name" id="edit_name" class="form-control bg-dark text-white border-secondary shadow-inset"></div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small">Nickname</label><input type="text" name="sur_name" id="edit_surname" class="form-control bg-dark text-white border-secondary shadow-inset"></div>
                        <div class="col-6 mb-3">
                            <label class="form-label small">Level</label>
                            @if(Auth::user()->level === 'developer')
                                <div class="dropdown">
                                    <button class="btn btn-outline-warning dropdown-toggle w-100 text-start" type="button" id="editLevelDropdown" data-bs-toggle="dropdown">
                                        User
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-dark border-secondary w-100">
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="setLevel('edit', 'user', 'User')">User</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="setLevel('edit', 'admin', 'Admin')">Admin</a></li>
                                    </ul>
                                    <input type="hidden" name="level" id="edit_level_input" required>
                                </div>
                            @else
                                <input type="text" class="form-control bg-dark text-white-50 border-secondary" id="edit_level_display" readonly>
                                <input type="hidden" name="level" id="edit_level_input">
                            @endif
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label small">Phone (Num)</label><input type="number" name="num" id="edit_num" class="form-control bg-dark text-white border-secondary shadow-inset"></div>
                    <div class="mb-3"><label class="form-label small">New Password (Leave blank to keep)</label><input type="password" name="pass" class="form-control bg-dark text-white border-secondary shadow-inset"></div>
                </div>
                <div class="modal-footer border-secondary border-0 pt-0">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('index.components.delete_modal')

<script>
function setLevel(mode, value, text) {
    const input = document.getElementById(mode + '_level_input');
    const button = document.getElementById(mode + 'LevelDropdown');
    if(input && button) {
        input.value = value;
        button.innerText = text;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    const editForm = document.getElementById('editUserForm');

    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.onclick = function() {
            const data = this.dataset;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_surname').value = data.surname;
            document.getElementById('edit_num').value = data.num;
            document.getElementById('edit_level_input').value = data.level;

            @if(Auth::user()->level === 'developer')
                document.getElementById('editLevelDropdown').innerText = data.level.charAt(0).toUpperCase() + data.level.slice(1);
            @else
                document.getElementById('edit_level_display').value = data.level.toUpperCase();
            @endif

            editForm.action = '/admin/users/' + data.id;
            editModal.show();
        };
    });

    const deleteForm = document.getElementById('deleteForm');
    const deleteName = document.getElementById('delete_name');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteServiceModal'));

    document.querySelectorAll('.btn-delete-trigger').forEach(btn => {
        btn.onclick = function() {
            deleteName.innerText = this.dataset.name;
            deleteForm.action = '/admin/users/' + this.dataset.id;
            deleteModal.show();
        };
    });
});
</script>

<style>

    .shadow-inset { box-shadow: inset 0 2px 4px rgba(0,0,0,0.5); }
    .table-cukur th { background-color: #1a1a1a; font-size: 0.8rem; text-transform: uppercase; color: #6c757d; }
</style>
@endsection