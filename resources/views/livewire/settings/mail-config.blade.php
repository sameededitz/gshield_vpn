@section('title', 'Mail Configuration')
<div>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Home</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Mail Configuration</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Mail Configuration</h3>
                </div>
                <div class="card-body">
                    <form class="row g-2" wire:submit.prevent="store">
                        <div class="col-sm-12">
                            <label for="mail_host" class="form-label">Mail Host</label>
                            <input type="text" class="form-control" id="mail_host" placeholder="Mail Host"
                                name="mail_host" wire:model="mail_host">
                        </div>
                        <div class="col-sm-12">
                            <label for="mail_port" class="form-label">Mail Port</label>
                            <input type="text" class="form-control" id="mail_port" placeholder="Mail Port"
                                name="mail_port" wire:model="mail_port">
                        </div>
                        <div class="col-sm-12">
                            <label for="mail_username" class="form-label">Mail Username</label>
                            <input type="text" class="form-control" id="mail_username" placeholder="Mail Username"
                                name="mail_username" wire:model="mail_username">
                        </div>
                        <div class="col-sm-12">
                            <label for="mail_password" class="form-label">Mail Password</label>
                            <input type="text" class="form-control" id="mail_password" placeholder="Mail Password"
                                name="mail_password" wire:model="mail_password">
                        </div>
                        <div class="col-sm-12">
                            <label for="mail_from_address" class="form-label">Mail From Address</label>
                            <input type="text" class="form-control" id="mail_from_address"
                                placeholder="Mail From Address" name="mail_from_address" wire:model="mail_from_address">
                        </div>
                        <div class="col-sm-12">
                            <label for="mail_from_name" class="form-label">Mail From Name</label>
                            <input type="text" class="form-control" id="mail_from_name" placeholder="Mail From Name"
                                name="mail_from_name" wire:model="mail_from_name">
                        </div>
                        <div class="col-12">
                            <button type="submit"
                                class="btn btn-outline-info d-flex align-items-center justify-content-center">Save</button>
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
