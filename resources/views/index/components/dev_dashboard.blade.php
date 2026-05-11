{{-- Stats Row --}}
<div class="row g-2 mb-4 flex-nowrap"> {{-- flex-nowrap ensures they stay on one line --}}
    {{-- Element 1: Total --}}
    <div class="col">
        <div class="card bg-dark border-secondary shadow-sm h-100">
            <div class="card-body d-flex align-items-center p-2"> {{-- Reduced padding --}}
                <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-2">
                    <i class="bi bi-ticket-detailed text-warning fs-5"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">Total Tickets</div>
                    <h4 class="text-white fw-bold mb-0">{{ $totalTickets }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Element 2: Resolved --}}
    <div class="col">
        <div class="card bg-dark border-secondary shadow-sm h-100">
            <div class="card-body d-flex align-items-center p-2">
                <div class="rounded-circle bg-info bg-opacity-10 p-2 me-2">
                    <i class="bi bi-check2-circle text-info fs-5"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">Tickets Closed</div>
                    <h4 class="text-white fw-bold mb-0">{{ $completedTickets }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Element 3: Open --}}
    <div class="col">
        <div class="card bg-dark border-secondary shadow-sm h-100">
            <div class="card-body d-flex align-items-center p-2">
                <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2">
                    <i class="bi bi-radioactive text-danger fs-5"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">Tickets Open</div>
                    <h4 class="text-white fw-bold mb-0">{{ $OpenTickets }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Element 4: Regist Tickets --}}
    <div class="col">
        <div class="card bg-dark border-secondary shadow-sm h-100">
            <div class="card-body d-flex align-items-center p-2">
                <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-2">
                    <i class="bi bi-person-exclamation text-warning fs-5"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">People Need Review</div>
                    <h4 class="text-white fw-bold mb-0">{{ $RegistTickets }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Element 5: Progress --}}
    <div class="col">
        <div class="card bg-dark border-secondary shadow-sm h-100">
            <div class="card-body d-flex align-items-center p-2">
                <div class="rounded-circle bg-white bg-opacity-10 p-2 me-2">
                    <i class="bi bi-clock-history text-white fs-5"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">In Progress</div>
                    <h4 class="text-white fw-bold mb-0">{{ $OGPTickets }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    /* Helper to keep text from breaking on small screens */
    .extra-small {
        font-size: 0.65rem;
    }
</style>

<div class="row">
    {{-- Radial Chart --}}
    <div class="col-md-3">
        <div class="card bg-dark border-secondary shadow-sm">
            <div class="card-header bg-transparent border-secondary py-3">
                <h6 class="text-warning fw-bold mb-0">OVERALL</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div id="radialChart"></div>
            </div>
        </div>
    </div>

    {{-- System Status Table --}}
    <div class="col-lg-7 mb-4">
        <div class="card bg-dark border-secondary shadow-sm">
            <div class="card-header bg-transparent border-secondary py-3">
                <h6 class="text-warning fw-bold mb-0">RECENT SYSTEM ACTIVITY</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle small">
                    <thead>
                        <tr class="text-muted">
                            <th class="ps-3 border-secondary">MODULE</th>
                            <th class="border-secondary">STATUS</th>
                            <th class="text-end pe-3 border-secondary">LATENCY</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Database Row --}}
                        <tr>
                            <td class="ps-3 text-info fw-bold">Database MySQL</td>
                            <td>
                                <span class="text-{{ $dbStatus['class'] }}">
                                    <i class="bi bi-circle-fill me-2 small"></i>{{ $dbStatus['label'] }}
                                </span>
                            </td>
                            <td class="text-end pe-3">{{ $dbStatus['latency'] }}</td>
                        </tr>

                        {{-- Storage Row --}}
                        <tr>
                            <td class="ps-3 text-info fw-bold">Asset Storage</td>
                            <td>
                                <span class="text-{{ $storageStatus['class'] }}">
                                    <i class="bi bi-circle-fill me-2 small"></i>{{ $storageStatus['label'] }}
                                </span>
                            </td>
                            <td class="text-end pe-3 small text-muted">{{ $storageStatus['latency'] }}</td>
                        </tr>

                        {{-- Environment Row --}}
                        <tr>
                            <td class="ps-3 text-info fw-bold">Laravel Core</td>
                            <td>
                                <span class="text-{{ $envStatus['class'] }}">
                                    <i class="bi bi-circle-fill me-2 small"></i>{{ $envStatus['label'] }}
                                </span>
                            </td>
                            <td class="text-end pe-3 small text-muted">{{ $envStatus['latency'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        var options = {
            chart: {
                height: 180, // Slightly taller for better visibility
                type: "radialBar",
                foreColor: '#6c757d' // Matches your muted text color
            },

            // Injecting the real calculation from your Controller
            series: [{{ $completionRate }}],

            plotOptions: {
                radialBar: {
                    hollow: {
                        margin: 20,
                        size: "65%",
                        background: "transparent"
                    },
                    track: {
                        background: '#2c2c2c', // Dark track for the "unfilled" part
                        strokeWidth: '100%',
                    },
                    dataLabels: {
                        showOn: "always",
                        name: {
                            offsetY: -10,
                            show: true,
                            color: "#6c757d",
                            fontSize: "10px",
                            fontWeight: 600,
                        },
                        value: {
                            offsetY: 1,
                            color: "#ffffff", // Your signature Cukur Gold
                            fontSize: "24px",
                            fontWeight: 700,
                            show: true,
                            // Adds the % sign automatically
                            formatter: function(val) {
                                return val + "%";
                            }
                        }
                    }
                }
            },

            // Gradient makes it look more "Developer Premium"
            fill: {
                type: 'solid',
                colors: ['#ffb82b'] // Solid Cukur Gold
            },

            stroke: {
                lineCap: "round", // Smooth rounded ends
            },

            labels: ["Solved Tasks"]
        };

        // Ensure the selector matches your HTML ID (e.g., #radialChart)
        var chart = new ApexCharts(document.querySelector("#radialChart"), options);
        chart.render();
    </script>
@endsection
