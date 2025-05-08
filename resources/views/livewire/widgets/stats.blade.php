<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Total VPS Servers</p>
                        <h4 class="my-1">{{ $totalVpsServers }}</h4>
                    </div>
                    <div class="widgets-icons ms-auto">
                        <iconify-icon icon="solar:server-2-linear" width="24" height="24"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Total VPN Servers</p>
                        <h4 class="my-1">{{ $totalServers }}</h4>
                    </div>
                    <div class="widgets-icons ms-auto">
                        <iconify-icon icon="material-symbols-light:vpn-key-outline" width="24" height="24"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Total Plans</p>
                        <h4 class="my-1">{{ $totalPlans }}</h4>
                    </div>
                    <div class="widgets-icons ms-auto">
                        <iconify-icon icon="streamline:subscription-cashflow" width="24" height="24"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Total Users</p>
                        <h4 class="my-1">{{ $totalUsers }}</h4>
                        <p class="mb-0 font-13"><i class='bx bxs-up-arrow align-middle'></i>{{ $userChangePercentage }}% Since last week</p>
                    </div>
                    <div class="widgets-icons ms-auto">
                        <iconify-icon icon="flowbite:users-group-outline" width="24" height="24"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
