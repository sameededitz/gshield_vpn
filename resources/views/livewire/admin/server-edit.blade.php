@section('title', 'All Servers')
<div>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Home</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Server</li>
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
                    <h3 class="card-title mb-0">Edit Server</h3>
                </div>
                <div class="card-body">
                    <form class="row g-2" wire:submit.prevent="store" enctype="multipart/form-data">
                        <div class="col-12">
                            <label for="image" class="form-label">Old Image</label>
                            <div class="mb-3">
                                <img src="{{ $server->getFirstMediaUrl('image') }}" alt="Old Image"
                                    class="img-fluid" width="80px" />
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="image" class="form-label">Image</label>
                            <x-filepond::upload wire:model="image" allowImageValidateSize="true" maxFileSize="20MB"
                                allowFileTypeValidation="true" acceptedFileTypes="image/jpeg, image/png, image/jpg" />
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Name"
                                wire:model.defer="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select w-100" id="type" wire:model="type">
                                <option value="" selected>Select Type</option>
                                <option value="free">Free</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select w-100" id="status" wire:model="status">
                                <option value="" selected>Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input class="form-control" id="longitude" rows="3" placeholder="Longitude"
                                wire:model.defer="longitude"></input>
                            @error('longitude')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude" placeholder="Latitude"
                                wire:model.defer="latitude">
                            @error('latitude')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="latitude" class="form-label">Platforms</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" wire:model="android" type="checkbox"
                                    id="flexSwitchCheckChecked">
                                <label class="form-check-label" for="flexSwitchCheckChecked">Android</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" wire:model="ios" type="checkbox"
                                    id="flexSwitchCheckCheckedIos">
                                <label class="form-check-label" for="flexSwitchCheckCheckedIos">iOS</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" wire:model="macos" type="checkbox"
                                    id="flexSwitchCheckCheckedMacos">
                                <label class="form-check-label" for="flexSwitchCheckCheckedMacos">macOS</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" wire:model="windows" type="checkbox"
                                    id="flexSwitchCheckCheckedWindows">
                                <label class="form-check-label" for="flexSwitchCheckCheckedWindows">Windows</label>
                            </div>
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

@section('scripts')
    @filepondScripts
@endsection
