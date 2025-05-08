@section('title', 'All Servers')
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
                    <li class="breadcrumb-item active" aria-current="page">Servers</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">All Servers</h3>
                    <a href="{{ route('servers.add') }}">
                        <button type="button" class="btn btn-light btn-outline-primary px-3 radius-30">Create
                            Server</button>
                    </a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap row-gap-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <select class="form-select form-select-sm" wire:model.live="perPage">
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
                                            <label for="statusFilter" class="mb-1">Filter by Status</label>
                                            <select class="form-select w-100" id="statusFilter"
                                                wire:model.live="statusFilter">
                                                <option value="" selected>Status</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="dropdown-item mb-1">
                                            <label for="typeFilter" class="mb-1">Filter by Type</label>
                                            <select class="form-select w-100" id="typeFilter"
                                                wire:model.live="typeFilter">
                                                <option value="" selected>Type</option>
                                                <option value="free">Free</option>
                                                <option value="premium">Premium</option>
                                            </select>
                                        </div>
                                        <div class="dropdown-item mb-1">
                                            <label for="platformFilter" class="mb-1">Filter by Platform</label>
                                            <select class="form-select w-100" id="platformFilter"
                                                wire:model.live="platformFilter">
                                                <option value="" selected>Platform</option>
                                                <option value="windows">Windows</option>
                                                <option value="macos">Mac</option>
                                                <option value="ios">iOS</option>
                                                <option value="android">Android</option>
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
                                <th>Image</th>
                                <th>Name</th>
                                <th>Platforms</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($servers as $server)
                                <tr>
                                    <td>{{ $server->id }}</td>
                                    <td>
                                        <img src="{{ $server->getFirstMediaUrl('image') }}" alt="Server Image"
                                            width="80px">
                                    </td>
                                    <td>{{ $server->name }}</td>
                                    <td>
                                        <span
                                            class="badge badge-light-primary">{{ $server->android ? 'Android' : '' }}</span>
                                        <span class="badge badge-light-secondary">{{ $server->ios ? 'iOS' : '' }}</span>
                                        <span
                                            class="badge badge-light-warning">{{ $server->macos ? 'MacOS' : '' }}</span>
                                        <span
                                            class="badge badge-light-success">{{ $server->windows ? 'Windows' : '' }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $server->isPremium() ? 'badge-light-primary' : 'badge-light-secondary' }}">
                                            {{ ucfirst($server->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $server->isActive() ? 'badge-light-success' : 'badge-light-danger' }}">
                                            {{ $server->isActive() ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a type="button" href="{{ route('all.sub-servers', $server->id) }}"
                                                class="btn btn-outline-info d-flex align-items-center justify-content-center">
                                                <Iconify-icon icon="solar:server-square-broken" width="20"
                                                    height="20"></Iconify-icon>
                                            </a>
                                            <a type="button" href="{{ route('servers.edit', $server->id) }}"
                                                class="btn btn-outline-warning d-flex align-items-center justify-content-center">
                                                <iconify-icon icon="material-symbols:edit" width="20"
                                                    height="20"></iconify-icon>
                                            </a>
                                            <button type="button"
                                                wire:click="$js.confirmDelete({{ $server->id }})"
                                                class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                                <Iconify-icon icon="mingcute:delete-2-line" width="20"
                                                    height="20"></Iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No Servers found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $servers->links('components.pagination', data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $js('confirmDelete', (id) => {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteServer(id);
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
