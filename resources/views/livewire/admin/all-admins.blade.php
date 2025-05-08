@section('title', 'All Admins')
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
                    <li class="breadcrumb-item active" aria-current="page">Admins</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">All Admins</h3>
                    <button type="button" class="btn btn-light btn-outline-primary px-3 radius-30"
                        data-bs-toggle="modal" data-bs-target="#userModel" wire:click="resetForm">
                        Create Admin
                    </button>
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
                                <ul class="dropdown-menu dropdown-menu-end" wire:ignore.self>
                                    <li>
                                        <div class="dropdown-header text-start d-flex justify-content-between mb-3">
                                            <h6 class="text-uppercase mb-0">Filters</h6>
                                            <h6 class="text-danger mb-0" wire:click="resetFilters"
                                                style="cursor: pointer;">Reset</h6>
                                        </div>
                                        <div class="dropdown-item mb-3">
                                            <label class="mb-1">Last Login From</label>
                                            <input type="date" class="form-control" wire:model.live="lastLoginStart">
                                        </div>
                                        <div class="dropdown-item mb-3">
                                            <label class="mb-1">Last Login To</label>
                                            <input type="date" class="form-control" wire:model.live="lastLoginEnd">
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Last Login</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->last_login ? $user->last_login->toFormattedDateString() : 'N/A' }}
                                    </td>
                                    <td>{{ $user->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" wire:click="editUser({{ $user->id }})"
                                                data-bs-toggle="modal" data-bs-target="#userModel"
                                                class="btn btn-outline-warning d-flex align-items-center justify-content-center">
                                                <iconify-icon icon="material-symbols:edit" width="20"
                                                    height="20"></iconify-icon>
                                            </button>
                                            <button type="button" wire:click="$js.confirmDelete({{ $user->id }})"
                                                class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                                <Iconify-icon icon="mingcute:delete-2-line" width="20"
                                                    height="20"></Iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No Users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $users->links('components.pagination', data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userModel" tabindex="-1" wire:ignore.self aria-labelledby="userModelLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ $isEdit ? 'Edit Admin' : 'Add New Admin' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"
                        aria-label="Close"></button>
                </div>
                <form class="row g-2" wire:submit.prevent="saveUser">
                    <div class="modal-body">
                        <div class="col-12 mb-2">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Name"
                                wire:model.defer="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Email"
                                wire:model.defer="email">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @if (!$isEdit)
                            <div class="col-sm-12 mb-2">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="text" class="form-control" id="password"
                                            wire:model="password" placeholder="Password">
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <input type="text" class="form-control" id="password_confirmation"
                                            wire:model.defer="password_confirmation" placeholder="Confirm Password">
                                        @error('password_confirmation')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($isEdit)
                            <div class="col-12 mb-2">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select w-100" id="role" wire:model.defer="role">
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                                @error('role')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-outline-info d-flex align-items-center justify-content-center"
                            wire:click="resetForm" data-bs-dismiss="modal">Close</button>
                        <button type="submit"
                            class="btn btn-outline-success d-flex align-items-center justify-content-center">
                            {{ $isEdit ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
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
                    $wire.deleteUser(id);
                }
            });
        });

        $wire.on('closeModel', (event) => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('userModel'));
            modal.hide();
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
