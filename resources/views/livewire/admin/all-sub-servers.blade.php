@section('title', 'All Sub Servers')
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
                    <li class="breadcrumb-item" aria-current="page">Servers</li>
                    <li class="breadcrumb-item active" aria-current="page">Sub Servers</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">All Sub Servers</h3>
                    <a href="{{ route('sub-server.add', $server->id) }}">
                        <button type="button" class="btn btn-light btn-outline-primary px-3 radius-30">Create
                            Sub Server</button>
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
                            <select class="form-select form-select-sm" wire:model.live="statusFilter">
                                <option value="" selected>Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="search-input">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" placeholder="Search..."
                                    wire:model.live.500ms="search">
                                <span class="input-group-text" id="basic-addon1">
                                    <Iconify-icon icon="material-symbols-light:search" width="20"
                                        height="20"></Iconify-icon>
                                </span>
                            </div>
                        </div>
                    </div>
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Linked VPS Server</th>
                                <th>VPS Server Username</th>
                                <th>VPS Server IP Address</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subServers as $subServer)
                                <tr>
                                    <td>{{ $subServer->id }}</td>
                                    <td>{{ $subServer->name }}</td>
                                    <td>{{ $subServer->vpsServer->name ?? 'N/A' }}</td>
                                    <td>{{ $subServer->vpsServer->username }}</td>
                                    <td>{{ $subServer->vpsServer->ip_address }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $subServer->isActive() ? 'badge-light-success' : 'badge-light-danger' }}">
                                            {{ $subServer->status === 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $subServer->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a type="button" href="{{ route('sub-server.edit', [$server->id, $subServer->id]) }}"
                                                class="btn btn-outline-warning d-flex align-items-center justify-content-center">
                                                <iconify-icon icon="material-symbols:edit" width="20"
                                                    height="20"></iconify-icon>
                                            </a>
                                            <button type="button" wire:click="$js.confirmDelete({{ $subServer->id }})"
                                                class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                                <Iconify-icon icon="mingcute:delete-2-line" width="20"
                                                    height="20"></Iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No Sub Servers found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $subServers->links('components.pagination', data: ['scrollTo' => false]) }}
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
                    $wire.deleteSubServer(id);
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
