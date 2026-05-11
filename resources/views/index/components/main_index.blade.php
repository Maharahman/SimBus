<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUKURUKUK | Admin Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        :root {
            --cukur-gold: #ffb82b;
            --sidebar-bg: #111111;
            --body-bg: #1a1d20;
        }

        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: var(--body-bg);
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100%;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            min-height: 100vh;
            width: 260px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid #333;
            transition: margin 0.25s ease-out;
            flex-shrink: 0;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 2rem 1.25rem;
            text-align: center;
        }

        .menu-label {
            padding: 1.5rem 1.5rem 0.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #555;
            font-weight: 700;
        }

        .nav-link {
            color: #aaa;
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            transition: 0.2s;
            border-left: 4px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--cukur-gold);
            background: rgba(255, 184, 43, 0.05);
            border-left-color: var(--cukur-gold);
        }

        /* Responsive Toggle */
        #wrapper.toggled #sidebar-wrapper {
            margin-left: -260px;
        }

        #page-content-wrapper {
            width: 100%;
        }

        .navbar {
            background-color: var(--sidebar-bg) !important;
            border-bottom: 1px solid #333;
        }

        /* Custom Table Header Styles */
        .table-cukur {
            background-color: var(--cukur-gold) !important;
        }

        .table-cukur th {
            background-color: var(--cukur-gold) !important;
            color: #111111 !important;
            font-weight: 700;
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        /* Dropdown custom */
        .dropdown-menu {
            background-color: #222;
            border: 1px solid #444;
        }

        .dropdown-item:hover {
            color: #000000 !important;
            background-color: var(--cukur-gold);
        }

        .dropdown-item:hover i {
            color: #000000 !important;
        }

        .dropdown-item.text-danger:hover {
            color: #000000 !important;
            background-color: #ff0000 !important;
            /* A darker red for contrast against the gold */
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <img src="{{ asset('icons/cukurukuk.svg') }}" width="150" alt="CUKURUKUK">
            </div>

            <div class="list-group list-group-flush">
                <div class="menu-label">Overview</div>

                <a href="{{ url('/admin/dashboard') }}"
                    class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>

                <div class="menu-label">Management Menu</div>
                <a href="{{ route('transactions.index') }}"
                    class="nav-link {{ Request::is('admin/transactions*') ? 'active' : '' }}"><i
                        class="bi bi-cash-coin me-2"></i> Transactions</a>
                <a href="{{ route('services.index') }}"
                    class="nav-link {{ Request::is('admin/services*') ? 'active' : '' }}"><i
                        class="bi bi-scissors me-2"></i> Services</a>
                <a href="{{ route('inventory.index') }}"
                    class="nav-link {{ Request::is('admin/inventory*') ? 'active' : '' }}"><i
                        class="bi bi-box-seam me-2"></i> Inventory</a>
                @if (Auth::user()->level !== 'user')
                    <a href="{{ route('users.index') }}"
                        class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}"><i
                            class="bi bi-people me-2"></i> Crews</a>

                    <a href="{{ route('tickets.index') }}"
                        class="nav-link {{ Request::is('admin/tickets*') ? 'active' : '' }}"><i
                            class="bi bi-ticket-perforated me-2"></i> Tickets</a>
                    @if (Auth::user()->level === 'developer')
                        <div class="menu-label">System</div>
                        <a href="#" class="nav-link"><i class="bi bi-cpu me-2"></i> Settings</a>
                    @endif
                @endif
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-dark px-4 py-3">
                <div class="container-fluid">
                    <button class="btn btn-outline-warning" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center">
                        <div class="text-end me-3 d-none d-md-block">
                            <small class="text-muted d-block">Welcome,</small>
                            <span class="fw-bold text-white">{{ Auth::user()->sur_name ?? 'Fulan' }} |
                                {{ Auth::user()->level ?? 'user' }}</span>
                        </div>

                        <div class="dropdown">
                            <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . (Auth::user()->name ?? 'AS') . '&background=ffb82b&color=fff' }}"
                                    width="50" height="50"
                                    class="rounded-circle border border-3 border-warning shadow"
                                    style="object-fit: cover;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                        data-bs-toggle="modal" data-bs-target="#myProfileModal">
                                        <i class="bi bi-person me-2 text-warning"></i> My Profile
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider border-secondary">
                                </li>

                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold">
                                            <i class="bi bi-power me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <div class="modal fade" id="myProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-warning shadow-lg">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white"><i class="bi bi-person-circle me-2"></i>My Profile</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body text-white">
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . Auth::user()->sur_name . '&background=ffb82b&color=fff' }}"
                                    id="profilePreview" class="rounded-circle shadow" width="120" height="120">
                                <label for="photoInput"
                                    class="btn btn-sm btn-warning position-absolute bottom-0 end-0 rounded-circle">
                                    <i class="bi bi-camera"></i>
                                </label>
                                <input type="file" name="profile_photo" id="photoInput" class="d-none"
                                    accept="image/*">
                            </div>
                            <p class="small text-muted mt-2">Click camera to change photo</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small">Full Name</label>
                                <input type="text" name="name"
                                    class="form-control bg-dark text-white border-secondary shadow-inset"
                                    value="{{ Auth::user()->name }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small">Nickname</label>
                                <input type="text" name="sur_name"
                                    class="form-control bg-dark text-white border-secondary shadow-inset"
                                    value="{{ Auth::user()->sur_name }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Phone Number</label>
                            <input type="text" name="num"
                                class="form-control bg-dark text-white border-secondary shadow-inset"
                                value="{{ Auth::user()->num }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">New Password (Leave blank to keep current)</label>
                            <input type="password" name="pass"
                                class="form-control bg-dark text-white border-secondary shadow-inset">
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="submit" class="btn btn-warning w-100 fw-bold text-dark">SAVE CHANGES</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
    </script>


    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('wrapper').classList.toggle('toggled');
        });

        document.getElementById('photoInput').onchange = function(evt) {
            const [file] = this.files;
            if (file) {
                document.getElementById('profilePreview').src = URL.createObjectURL(file);
            }
        };
    </script>

    <script>
        function toggleRejection(id) {
            const accessSection = document.getElementById('accessLevelSection' + id);
            const rejectSection = document.getElementById('rejectionReasonSection' + id);
            const decisionInput = document.getElementById('decision' + id);
            const submitBtn = document.getElementById('btnSubmit' + id);
            const rejectToggle = document.getElementById('btnRejectToggle' + id);

            if (decisionInput.value === 'approve') {
                // Switch to Reject Mode
                decisionInput.value = 'reject';
                accessSection.classList.add('d-none');
                rejectSection.classList.remove('d-none');
                submitBtn.innerText = 'CONFIRM REJECTION';
                submitBtn.classList.replace('btn-warning', 'btn-danger');
                rejectToggle.innerText = 'CANCEL';
            } else {
                // Switch back to Approve Mode
                decisionInput.value = 'approve';
                accessSection.classList.remove('d-none');
                rejectSection.classList.add('d-none');
                submitBtn.innerText = 'GRANT ACCESS & CLOSE';
                submitBtn.classList.replace('btn-danger', 'btn-warning');
                rejectToggle.innerText = 'REJECT';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @yield('scripts')
</body>

</html>
