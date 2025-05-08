@section('title', 'All Tickets')
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
                    <li class="breadcrumb-item active" aria-current="page">Tickets</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">All Tickets</h3>
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
                                        <div class="dropdown-item mb-3">
                                            <label for="status" class="mb-1">Filter by Status</label>
                                            <select id="status" class="form-select w-100" wire:model.live="status">
                                                <option value="" selected>All</option>
                                                <option value="open">Open</option>
                                                <option value="closed">Closed</option>
                                                <option value="pending">Pending</option>
                                            </select>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->id }}</td>
                                    <td>{{ $ticket->user->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.ticket.details', $ticket->id) }}"
                                            class="text-primary">{{ $ticket->subject }}</a>
                                    </td>
                                    <td>
                                        @if ($ticket->status == 'open')
                                            <span class="badge bg-success">Open</span>
                                        @elseif ($ticket->status == 'closed')
                                            <span class="badge bg-danger">Closed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($ticket->status !== 'closed')
                                                <button type="button"
                                                    wire:click="$js.updateStatus({{ $ticket->id }}, 'close')"
                                                    class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                                    <Iconify-icon icon="material-symbols:close-rounded" width="20"
                                                        height="20"></Iconify-icon>
                                                </button>
                                            @endif
                                            @if ($ticket->status !== 'open')
                                                <button type="button"
                                                    wire:click="$js.updateStatus({{ $ticket->id }}, 'open')"
                                                    class="btn btn-outline-success d-flex align-items-center justify-content-center">
                                                    <Iconify-icon icon="material-symbols:check-circle-outline"
                                                        width="20" height="20"></Iconify-icon>
                                                </button>
                                            @endif
                                            @if ($ticket->status !== 'pending')
                                                <button type="button"
                                                    wire:click="$js.updateStatus({{ $ticket->id }}, 'pending')"
                                                    class="btn btn-outline-warning d-flex align-items-center justify-content-center">
                                                    <Iconify-icon icon="material-symbols:hourglass-top" width="20"
                                                        height="20"></Iconify-icon>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No Tickets found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $tickets->links('components.pagination', data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $js('updateStatus', (id, status) => {
            let actionText = status === 'open' ? 'Reopen' : (status === 'pending' ? 'mark as pending' : 'close');
            let actionBtnText = status === 'open' ? 'Yes, Reopen it!' : (status === 'pending' ?
                'Yes, Mark as Pending!' : 'Yes, Close it!');

            Swal.fire({
                title: `Are you sure you want to ${actionText} this Ticket?`,
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
