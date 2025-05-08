@section('title', 'All Transactions')
<div>
    @if (session('message'))
        <x-alert type="info" :message="session('message')" />
    @endif
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Home</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Transactions</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">All Transactions</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap gap-3 align-items-center">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <select class="form-select" wire:model.live="perPage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search..."
                                    wire:model.live.500ms="search">
                                <span class="input-group-text" id="basic-addon1">
                                    <Iconify-icon icon="material-symbols-light:search" width="20"
                                        height="20"></Iconify-icon>
                                </span>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light btn-rounded d-flex" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <iconify-icon icon="line-md:filter" width="24" height="24"></iconify-icon>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="dropdown-header text-start d-flex justify-content-between mb-3">
                                            <h6 class="text-uppercase mb-0">Filters</h6>
                                            <h6 class="text-danger mb-0" wire:click="resetFilters"
                                                style="cursor: pointer;">Reset</h6>
                                        </div>
                                        <div class="dropdown-item mb-1">
                                            <label for="priceFilter" class="mb-1">Filter by Amount</label>
                                            <select class="form-select w-100" wire:model.live="amountFilter">
                                                <option value="" selected>Max Amount</option>
                                                <option value="10">Under $10</option>
                                                <option value="20">Under $20</option>
                                                <option value="50">Under $50</option>
                                                <option value="100">Under $100</option>
                                            </select>
                                        </div>
                                        <div class="dropdown-item">
                                            <label for="durationFilter" class="mb-1">Filter by Status</label>
                                            <select class="form-select w-100" wire:model.live="statusFilter">
                                                <option value="" selected>Status</option>
                                                <option value="active">Active</option>
                                                <option value="expired">Expired</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="table-box">
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Plan</th>
                                    <th>Amount Paid</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->id }}</td>
                                        <td>
                                            {{ Str::title($purchase->user->name) ?? 'N/A' }}
                                        </td>
                                        <td>{{ $purchase->plan->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($purchase->amount_paid, 2) }}</td>
                                        <td>{{ $purchase->start_date->toFormattedDateString() }}</td>
                                        <td>{{ optional($purchase->end_date)->toFormattedDateString() ?? 'N/A' }}</td>
                                        <td>
                                            <span
                                                class="badge badge-light-{{ $purchase->status == 'active' ? 'success' : ($purchase->status == 'expired' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($purchase->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($purchase->status != 'active')
                                                    @if (
                                                        ($purchase->status == 'expired' || $purchase->status == 'cancelled') &&
                                                            \Carbon\Carbon::parse($purchase->end_date)->isFuture())
                                                        <button type="button"
                                                            wire:click="$js.updateStatus({{ $purchase->id }}, 'active')"
                                                            @disabled($purchase->status == 'active')
                                                            class="btn btn-outline-success d-flex align-items-center justify-content-center">
                                                            <iconify-icon icon="material-symbols:check-circle"
                                                                width="20" height="20"></iconify-icon>
                                                        </button>
                                                    @endif
                                                @else
                                                    <button type="button"
                                                        wire:click="$js.updateStatus({{ $purchase->id }}, 'expired')"
                                                        @disabled($purchase->status == 'expired')
                                                        class="btn btn-outline-warning d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="mdi:clock-alert" width="20"
                                                            height="20"></iconify-icon>
                                                    </button>
                                                    <button type="button"
                                                        wire:click="$js.updateStatus({{ $purchase->id }}, 'cancelled')"
                                                        @disabled($purchase->status == 'cancelled')
                                                        class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="mdi:close-circle" width="20"
                                                            height="20"></iconify-icon>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No purchases found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $purchases->links('components.pagination', data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        new PerfectScrollbar('.table-box');

        $js('updateStatus', (id, status) => {
            let actionText = status === 'active' ? 'activate' : (status === 'expired' ? 'expire' : 'cancel');
            let actionBtnText = status === 'active' ? 'Yes, activate it!' : (status === 'expired' ?
                'Yes, expire it!' : 'Yes, cancel it!');

            Swal.fire({
                title: `Are you sure you want to ${actionText} this purchase?`,
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: actionBtnText
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.updateStatus(id, status);
                }
            });
        });

        $wire.on('sweetAlert', (event) => {
            Swal.fire({
                title: event.title,
                text: event.message,
                icon: event.type,
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
@endscript
@section('scripts')
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
@endsection
