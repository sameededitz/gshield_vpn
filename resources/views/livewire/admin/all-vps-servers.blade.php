@section('title', 'All Vps Servers')
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
                    <li class="breadcrumb-item active" aria-current="page">Vps Servers</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">All Vps Servers</h3>
                    <a href="{{ route('vps-servers.add') }}">
                        <button type="button" class="btn btn-light btn-outline-primary px-3 radius-30">Create
                            Vps Server</button>
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
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
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
                                <th>IP Address</th>
                                <th>Username</th>
                                <th>Port</th>
                                <th>Domain</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vpsServers as $vpsServer)
                                <tr>
                                    <td>{{ $vpsServer->id }}</td>
                                    <td>{{ $vpsServer->name }}</td>
                                    <td>{{ $vpsServer->ip_address }}</td>
                                    <td>{{ $vpsServer->username }}</td>
                                    <td>{{ $vpsServer->port }}</td>
                                    <td>{{ $vpsServer->domain }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $vpsServer->isActive() ? 'badge-light-success' : 'badge-light-danger' }}">
                                            {{ $vpsServer->isActive() ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a type="button" href="{{ route('vps-servers.manage', $vpsServer->id) }}"
                                                class="btn btn-outline-info d-flex align-items-center justify-content-center">
                                                <Iconify-icon icon="famicons:stats-chart-outline" width="20"
                                                    height="20"></Iconify-icon>
                                            </a>
                                            <button type="button" wire:click="editVpsServer({{ $vpsServer->id }})"
                                                data-bs-toggle="modal" data-bs-target="#editVpsServer"
                                                class="btn btn-outline-warning d-flex align-items-center justify-content-center">
                                                <iconify-icon icon="material-symbols:edit" width="20"
                                                    height="20"></iconify-icon>
                                            </button>
                                            <button type="button" wire:click="$js.confirmDelete({{ $vpsServer->id }})"
                                                class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                                <Iconify-icon icon="mingcute:delete-2-line" width="20"
                                                    height="20"></Iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No Vps Servers found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $vpsServers->links('components.pagination', data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editVpsServer" tabindex="-1" wire:ignore.self aria-labelledby="editVpsServer"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit <b>{{ $name }}</b></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-2" wire:submit.prevent="updateVpsServer">
                        <div class="col-12">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Name"
                                wire:model.defer="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="ip_address">IP Address</label>
                            <input type="text" class="form-control" id="ip_address" placeholder="IP Address"
                                wire:model="ip_address">
                            @error('ip_address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" placeholder="Username"
                                wire:model="username">
                            @error('username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="port">Port</label>
                            <input type="number" class="form-control" id="port" placeholder="Port"
                                wire:model="port">
                            @error('port')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="domain">Domain</label>
                            <input type="text" class="form-control" id="domain" placeholder="Domain"
                                wire:model="domain">
                            @error('domain')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select w-100" id="status" wire:model="status">
                                <option value="" selected>Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="password">Password</label>
                            <input type="text" class="form-control" id="password" placeholder="Password"
                                wire:model="password">
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="confirm_password">Private Key</label>
                            <textarea class="form-control" id="private_key" placeholder="Private Key" wire:model="private_key"></textarea>
                            @error('private_key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info d-flex align-items-center justify-content-center" data-bs-dismiss="modal">Close</button>
                    <button type="button"
                        class="btn btn-outline-success d-flex align-items-center justify-content-center"
                        wire:click.prevent="updateVpsServer">Update</button>
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
                    $wire.deleteVpsServer(id);
                }
            });
        });

        $wire.on('closeEditModal', () => {
            let modalElement = document.getElementById('editVpsServer');
            let myModal = bootstrap.Modal.getInstance(modalElement);

            if (!myModal) {
                myModal = new bootstrap.Modal(modalElement);
            }
            myModal.hide();
            $wire.dispatch('sweetAlert', {
                title: 'Success!',
                message: 'VPS server updated successfully',
                type: 'success'
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
