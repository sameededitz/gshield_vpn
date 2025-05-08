@section('title', 'Manage VPS Server')
<div>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Home</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Vps Server</li>
                    <li class="breadcrumb-item active" aria-current="page">Manage</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" wire:init="fetchServerUsage">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 text-end">
            <button type="button" wire:click="fetchServerUsage"
                class="btn btn-outline-info d-flex align-items-center justify-content-center float-end gap-2">

                <iconify-icon icon="radix-icons:reload" width="24" height="24" wire:loading.remove
                    wire:target="fetchServerUsage" class="transition-all duration-300"></iconify-icon>

                <iconify-icon icon="radix-icons:reload" width="24" height="24" wire:loading
                    wire:target="fetchServerUsage" class="animate-spin transition-all duration-300"></iconify-icon>

                <span wire:loading.remove wire:target="fetchServerUsage">
                    Refresh
                </span>

                <span wire:loading wire:target="fetchServerUsage">
                    Loading...
                </span>

            </button>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="text-center">
                        <div class="widgets-icons rounded-circle mx-auto ">
                            <iconify-icon icon="fad:cpu" width="48" height="48"></iconify-icon>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" wire:ignore>
                            <div id="cpu-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="text-center">
                        <div class="widgets-icons rounded-circle mx-auto">
                            <iconify-icon icon="ri:ram-2-line" width="38" height="38"></iconify-icon>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" wire:ignore>
                            <div id="ram-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="text-center">
                        <div class="widgets-icons rounded-circle mx-auto">
                            <iconify-icon icon="clarity:hard-disk-line" width="38" height="38"></iconify-icon>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" wire:ignore>
                            <div id="disk-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="widget-heading d-flex justify-content-between align-items-center">
                        <h6 class="mb-1">IKEv2</h6>
                        <span
                            class="badge badge-light-{{ $ikev2Status == 'Running' ? 'success' : 'danger' }}">{{ $ikev2Status == 'Running' ? 'Running' : $ikev2Status }}</span>
                    </div>
                    <div class="text-info">
                        {{ $ikev2ConnectedUsers }} connected users
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="widget-heading d-flex justify-content-between align-items-center">
                        <h6 class="mb-1">Wire guard</h6>
                        <span
                        class="badge badge-light-{{ $wireguardStatus == 'Running' ? 'success' : 'danger' }}">{{ $wireguardStatus == 'Running' ? 'Running' : 'Not Running' }}</span>
                    </div>
                    <div class="text-info">
                        {{ $ikev2ConnectedUsers }} connected users
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" wire:init="fetchConnectedUsers">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 text-end">
            <button type="button" wire:click="fetchConnectedUsers"
                class="btn btn-outline-info d-flex align-items-center justify-content-center float-end gap-2">

                <iconify-icon icon="radix-icons:reload" width="24" height="24" wire:loading.remove
                    wire:target="fetchConnectedUsers" class="transition-all duration-300"></iconify-icon>

                <iconify-icon icon="radix-icons:reload" width="24" height="24" wire:loading
                    wire:target="fetchConnectedUsers" class="animate-spin transition-all duration-300"></iconify-icon>

                <span wire:loading.remove wire:target="fetchConnectedUsers">
                    Fetch Connected Users
                </span>

                <span wire:loading wire:target="fetchConnectedUsers">
                    Loading...
                </span>

            </button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Connected Users</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap row-gap-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <select class="form-select form-select-sm" wire:model.live="vpnTypeFilter">
                                <option value="all">All</option>
                                <option value="wireguard">WireGuard</option>
                                <option value="ikev2">IKEv2</option>
                            </select>
                        </div>
                    </div>
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>IP</th>
                                <th>Uptime</th>
                                <th>VPN Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $filteredUsers = collect($connectedUsers)->filter(function ($user) use (
                                    $vpnTypeFilter,
                                ) {
                                    return $vpnTypeFilter === 'all' || $user['vpn_type'] === $vpnTypeFilter;
                                });
                            @endphp

                            @forelse ($filteredUsers as $index => $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucfirst(preg_replace('/_[^_]+$/', '', $user['name'])) }}</td>
                                    <td>{{ $user['ip'] }}</td>
                                    <td>{{ $user['uptime'] }}</td>
                                    <td>{{ ucfirst($user['vpn_type']) }}</td>
                                    <td>
                                        <a href=""
                                            class="btn btn-light-info btn-rounded btn-icon me-1 d-inline-flex align-items-center">
                                            <iconify-icon icon="ic:round-manage-accounts" width="20"
                                                height="20"></iconify-icon>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No connected users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 text-end">
            <button type="button" wire:click="runScript"
                class="btn btn-outline-info d-flex align-items-center justify-content-center float-end gap-2">

                <iconify-icon icon="carbon:script" width="24" height="24" wire:loading.remove
                    wire:target="runScript" class="transition-all duration-300"></iconify-icon>

                <iconify-icon icon="radix-icons:reload" width="24" height="24" wire:loading
                    wire:target="runScript" class="animate-spin transition-all duration-300"></iconify-icon>

                <span wire:loading.remove wire:target="runScript">
                    Run IKEv2 Script
                </span>

                <span wire:loading wire:target="runScript">
                    Loading...
                </span>

            </button>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    Script Output
                </div>
                <div class="card-body p-0">
                    <pre id="script-output" class="mb-0 terminal-output" wire:stream="output"
                        style="height: 400px; overflow-y: auto; padding: 1rem; font-family: 'Courier New', monospace; font-size: 14px; line-height: 1.5; background-color: #1e1e1e; color: #00ff00; white-space: pre-wrap; word-wrap: break-word;">{{ $output }}</pre>
                </div>
            </div>
        </div>
    </div>


</div>
@script
    <script>
        function extractNumber(value) {
            return parseFloat(value.replace(/[^\d.]/g, '')) || 0;
        }

        function createGaugeChart(element, value, label) {
            var options = {
                series: [value],
                chart: {
                    height: 250,
                    type: "radialBar",
                    offsetY: -10
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        track: {
                            background: "#e0e0e0"
                        },
                        dataLabels: {
                            name: {
                                fontSize: "16px",
                                color: "#888",
                                offsetY: 120
                            },
                            value: {
                                offsetY: 76,
                                fontSize: "22px",
                                formatter: val => val + "%"
                            }
                        }
                    }
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "light", // Optional: Gives a darker tone
                        type: "horizontal", // You can also use 'vertical' or 'diagonal'
                        gradientToColors: ["#A8E063"], // The end color for the gradient (lighter green)
                        stops: [0, 50, 65, 91], // The gradient stops
                        colorStops: [{
                                offset: 0,
                                color: "#004D40", // Dark green
                                opacity: 1
                            },
                            {
                                offset: 50,
                                color: "#388E3C", // Medium green
                                opacity: 1
                            },
                            {
                                offset: 75,
                                color: "#66BB6A", // Light green
                                opacity: 1
                            },
                            {
                                offset: 100,
                                color: "#A8E063", // Very light green
                                opacity: 1
                            }
                        ]
                    }
                },
                stroke: {
                    dashArray: 4
                },
                labels: [label]
            };

            var chart = new ApexCharts(document.querySelector(element), options);
            chart.render();
            return chart;
        }

        var cpuChart = createGaugeChart("#cpu-chart", 0, "CPU Usage");
        var ramChart = createGaugeChart("#ram-chart", 0, "RAM Usage");
        var diskChart = createGaugeChart("#disk-chart", 0, "Disk Usage");

        $wire.on('updateUsage', (event) => {
            function extractNumber(value) {
                return parseFloat(value.replace(/[^\d.]/g, '')) || 0;
            }

            let cpuUsage = extractNumber(event.cpu);
            let [ramUsed, ramTotal] = (event.ram.match(/\d+/g) || [0, 1]).map(Number);
            let ramPercent = (ramUsed / ramTotal) * 100;
            let [diskUsed, diskTotal] = (event.disk.match(/([\d.]+)/g) || [0, 1]).map(
                Number);
            let diskPercent = (diskUsed / diskTotal) * 100;

            cpuChart.updateSeries([cpuUsage]);
            ramChart.updateSeries([ramPercent.toFixed(2)]);
            diskChart.updateSeries([diskPercent.toFixed(2)]);
        });

        $wire.on('sweetToast', (event) => {
            Swal.fire({
                text: event.message,
                icon: event.type,
                position: 'top-end',
                toast: true,
                timer: 5000,
                showConfirmButton: false
            });
        });

        $wire.on('scrollToBottom', () => {
            var outputElement = document.getElementById('script-output');
            outputElement.scrollTop = outputElement.scrollHeight;
        });
    </script>
@endscript

@section('scripts')
    <script src="{{ asset('assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
@endsection
