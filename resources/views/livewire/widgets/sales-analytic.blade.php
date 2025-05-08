<div class="row">
    <div class="col-12 col-md-3 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="mb-0">Top Most Bought Plans</h5>
                    </div>
                </div>
                <div class="mt-5" id="chart15"></div>
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($topPlans as $plan)
                    <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">
                        {{ $plan['label'] }}
                        <span class="badge bg-white rounded-pill text-dark">{{ $plan['value'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-12 col-md-3 d-flex">
        <div class="card radius-10 w-100 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="mb-0">Sales Overiew</h5>
                    </div>
                </div>
                <div class="mt-5" id="chart20"></div>
            </div>
            <div class="card-footer bg-transparent border-top-0">
                <div class="d-flex align-items-center justify-content-between text-center">
                    <div>
                        <h6 class="mb-1 font-weight-bold">${{ number_format($weekSales, 2) }}</h6>
                        <p class="mb-0">Last Week</p>
                    </div>
                    <div>
                        <h6 class="mb-1 font-weight-bold">${{ number_format($monthSales, 2) }}</h6>
                        <p class="mb-0">Last Month</p>
                    </div>
                    <div>
                        <h6 class="mb-1 font-weight-bold">${{ number_format($yearSales, 2) }}</h6>
                        <p class="mb-0">Last Year</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-header border-bottom-0">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="mb-1">Top Plans</h5>
                        <p class="mb-0 font-13"><i class='bx bxs-calendar'></i> in last 30 days revenue</p>
                    </div>
                </div>
            </div>

            <div class="product-list p-3">
                @forelse($boughtPlans as $plan)
                    <div class="row border mx-0 {{ $loop->last ? 'mb-0' : 'mb-2' }} py-2 radius-10 cursor-pointer">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <div class="ms-2">
                                    <h6 class="mb-1">{{ $plan->name }}</h6>
                                    <p class="mb-0">${{ number_format($plan->price, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 text-end">
                            <h6 class="mb-1">${{ number_format($plan->total_revenue, 2) }}</h6>
                            <p class="mb-0">{{ $plan->total_sales }} Sales</p>
                        </div>
                    </div>
                @empty
                    <div class="row border mx-0 py-2 radius-10 cursor-pointer">
                        <div class="col-sm-12">
                            <div class="d-flex align-items-center text-center">
                                <div class="ms-2">
                                    <h6 class="mb-1">No plans bought in the last 30 days</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@script
    <script>
        new PerfectScrollbar('.product-list');

        const planChartOptions = {
            series: @json(collect($topPlans)->pluck('value')),
            chart: {
                height: 240,
                type: 'donut'
            },
            legend: {
                position: 'bottom',
                show: false
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '80%'
                    }
                }
            },
            colors: ['rgba(255, 255, 255, 0.70)', 'rgba(255, 255, 255, 0.85)', 'rgba(255, 255, 255, 0.55)',
                'rgba(255, 255, 255, 0.25)'
            ],
            dataLabels: {
                enabled: false
            },
            tooltip: {
                enabled: true,
                theme: 'dark',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Poppins, sans-serif',
                    fontWeight: '500',
                    color: '#000',
                },
                y: {
                    formatter: function(e) {
                        return e + ' sales'
                    }
                }
            },
            labels: @json(collect($topPlans)->pluck('label')),
        }

        const SalesChartOptions = {
            series: [{{ $totalSalesPercent }}],
            chart: {
                height: 310,
                type: "radialBar",
                offsetY: -10
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    hollow: {
                        margin: 0,
                        size: "70%",
                        background: "transparent"
                    },
                    track: {
                        background: "rgba(255, 255, 255, 0.25)",
                        strokeWidth: "100%",
                        dropShadow: {
                            enabled: !1,
                            top: -3,
                            left: 0,
                            blur: 4,
                            opacity: .12
                        }
                    },
                    dataLabels: {
                        name: {
                            fontSize: "16px",
                            color: "#fff",
                            offsetY: 5
                        },
                        value: {
                            offsetY: 20,
                            fontSize: "30px",
                            color: "#fff",
                            formatter: function(e) {
                                return e + "%"
                            }
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    shadeIntensity: 0.15,
                    inverseColors: false,
                    gradientToColors: ['#fff'],
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 65, 91]
                }
            },
            colors: ["#fff"],
            stroke: {
                dashArray: 4
            },
            labels: ["Total Sales"],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    }
                }
            }]
        };

        const plansChart = new ApexCharts(document.querySelector("#chart15"), planChartOptions).render();

        const salesChart = new ApexCharts(document.querySelector("#chart20"), SalesChartOptions).render();
    </script>
@endscript
