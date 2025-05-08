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
                    <li class="breadcrumb-item" aria-current="page">VPS Server</li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Create Vps Server</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <x-alert type="danger" message="Something went wrong!" />
                    @endif
                    <form class="row g-2" wire:submit.prevent="store">
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
                        <div class="col-12">
                            <button type="submit"
                                class="btn btn-outline-info d-flex align-items-center justify-content-center">Create</button>
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

        $wire.on('sweetToast', (event) => {
            Swal.fire({
                text: event.message,
                icon: event.type,
                position: 'top-end',
                toast: true,
                timer: 2000,
                showConfirmButton: false
            });
        });

        $wire.on('redirect', (event) => {
            setTimeout(() => {
                window.location.href = event.url;
            }, 2000);
        });
    </script>
@endscript
