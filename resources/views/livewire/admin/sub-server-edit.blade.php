@section('title', 'Create Sub Server')
<div>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Home</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Sub Server</li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Edit Sub Server</h3>
                </div>
                <div class="card-body">
                    <form class="row g-2" wire:submit.prevent="store">
                        <div class="col-12">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Name"
                                wire:model.defer="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <label for="vps_server_id" class="form-label">Linked VPS Server</label>
                            <select id="vps_server_id" class="form-select w-100" wire:model.defer="vps_server">
                                <option value="" selected>Select a VPS Server</option>
                                @foreach ($vpsServers as $vpsServer)
                                    <option value="{{ $vpsServer->id }}">
                                        {{ $vpsServer->name ?? 'Unnamed VPS' }} ({{ $vpsServer->ip_address }}) -
                                        {{ $vpsServer->status ? 'Active' : 'Inactive' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vps_server')
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
                            <button type="submit"
                                class="btn btn-outline-info d-flex align-items-center justify-content-center">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
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
