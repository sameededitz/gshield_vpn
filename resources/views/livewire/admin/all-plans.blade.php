@section('title', 'All Plans')
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
                    <li class="breadcrumb-item active" aria-current="page">Plans</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">All Plans</h3>
                    <button type="button" class="btn btn-light btn-outline-primary px-3 radius-30"
                        data-bs-toggle="modal" data-bs-target="#planModel" wire:click="resetForm">
                        Create Plan
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
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="dropdown-header text-start d-flex justify-content-between mb-3">
                                            <h6 class="text-uppercase mb-0">Filters</h6>
                                            <h6 class="text-danger mb-0" wire:click="resetFilters" style="cursor: pointer;">Reset</h6>
                                        </div>
                                        <div class="dropdown-item mb-3">
                                            <label for="priceFilter" class="mb-1">Filter by Price</label>
                                            <select id="priceFilter" class="form-select w-100"
                                                wire:model.live="priceFilter">
                                                <option value="" selected>Max Price</option>
                                                <option value="10">Under $10</option>
                                                <option value="20">Under $20</option>
                                                <option value="50">Under $50</option>
                                                <option value="100">Under $100</option>
                                            </select>
                                        </div>
                                        <div class="dropdown-item">
                                            <label for="durationFilter" class="mb-1">Filter by Duration</label>
                                            <select class="form-select w-100"
                                                wire:model.live="durationUnitFilter">
                                                <option value="" selected>Duration Unit</option>
                                                <option value="day">Day</option>
                                                <option value="week">Week</option>
                                                <option value="month">Month</option>
                                                <option value="year">Year</option>
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
                                <th>Name</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($plans as $plan)
                                <tr>
                                    <td>{{ $plan->id }}</td>
                                    <td>{{ $plan->name }}</td>
                                    <td>${{ $plan->price }}</td>
                                    <td> {{ $plan->duration }} {{ Str::title($plan->duration_unit) }}</td>
                                    <td>{{ $plan->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" wire:click="editPlan({{ $plan->id }})"
                                                data-bs-toggle="modal" data-bs-target="#planModel"
                                                class="btn btn-outline-warning d-flex align-items-center justify-content-center">
                                                <iconify-icon icon="material-symbols:edit" width="20"
                                                    height="20"></iconify-icon>
                                            </button>
                                            <button type="button" wire:click="$js.confirmDelete({{ $plan->id }})"
                                                class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                                <Iconify-icon icon="mingcute:delete-2-line" width="20"
                                                    height="20"></Iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No Plans found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $plans->links('components.pagination', data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="planModel" tabindex="-1" wire:ignore.self aria-labelledby="planModel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ $isEdit ? 'Edit Plan' : 'Add New Plan' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"
                        aria-label="Close"></button>
                </div>
                <form class="row g-2" wire:submit.prevent="savePlan">
                    <div class="modal-body">
                        <div class="col-12">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Name"
                                wire:model.defer="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" placeholder="Description"
                                wire:model.defer="description">
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" step="0.01"
                                placeholder="Price" wire:model.defer="price">
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <label for="duration" class="form-label">Duration</label>
                                    <input type="number" class="form-control" id="duration" wire:model="duration">
                                    @error('duration')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="duration_unit" class="form-label">Duration Unit</label>
                                    <select class="form-select w-100" id="duration_unit" wire:model="duration_unit">
                                        <option value="" selected>Select Unit</option>
                                        <option value="day">Day</option>
                                        <option value="week">Week</option>
                                        <option value="month">Month</option>
                                        <option value="year">Year</option>
                                    </select>
                                    @error('duration_unit')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

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
                    $wire.deletePlan(id);
                }
            });
        });

        $wire.on('closeModel', (event) => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('planModel'));
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
