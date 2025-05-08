@section('title', 'Manage User')
<div>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Home</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Users</li>
                    <li class="breadcrumb-item active" aria-current="page">Manage</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Personal Info</h5>
                </div>
                <div class="card-body">
                    <ul class="ps-0 fs-6 mb-0">
                        <li class="d-flex align-items-center gap-1 mb-12">
                            <p class="w-30 fw-semibold text-primary-light">Full Name</p>
                            <p class="w-70 fw-normal">: {{ $user->name }} </p>
                        </li>
                        <li class="d-flex align-items-center gap-1 mb-12">
                            <p class="w-30 text-md fw-semibold text-primary-light"> Email</p>
                            <p class="w-70 text-secondary-light fw-normal">: {{ $user->email }} </p>
                        </li>
                        <li class="d-flex align-items-center gap-1 mb-12">
                            <p class="w-30 text-md fw-semibold text-primary-light"> Role</p>
                            <p class="w-70 text-secondary-light fw-normal">: {{ Str::title($user->role) }}
                            </p>
                        </li>
                        <li class="d-flex align-items-center gap-1 mb-12">
                            <p class="w-30 text-md fw-semibold text-primary-light"> Last Login</p>
                            <p class="w-70 text-secondary-light fw-normal">:
                                {{ $user->last_login ? $user->last_login->diffForHumans() : 'Never' }} </p>
                        </li>
                        <li class="d-flex align-items-center gap-1 mb-12">
                            <p class="w-30 text-md fw-semibold text-primary-light"> Email Verified</p>
                            <p class="w-70 text-secondary-light fw-normal">:
                                {{ $user->hasVerifiedEmail() ? $user->email_verified_at->toDayDateTimeString() : 'No' }}
                            </p>
                        </li>
                        @if ($user->isBanned())
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <p class="w-30 text-md fw-semibold text-primary-light"> Banned At</p>
                                <p class="w-70 text-secondary-light fw-normal">:
                                    {{ $user->banned_at->toDayDateTimeString() }} </p>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <p class="w-30 text-md fw-semibold text-primary-light"> Ban Reason</p>
                                <p class="w-70 text-secondary-light fw-normal">:
                                    {{ $user->ban_reason }} </p>
                            </li>
                        @endif
                        <li class="d-flex align-items-center gap-1 mb-12">
                            <p class="w-30 text-md fw-semibold text-primary-light mb-0"> Registered</p>
                            <p class="w-70 text-secondary-light fw-normal mb-0">:
                                {{ $user->created_at->toDayDateTimeString() }}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Actions</h5>
                </div>
                <div class="card-body d-flex flex-column gap-2">

                    {{-- Email Verification Actions --}}
                    @if (!$user->hasVerifiedEmail())
                        <button class="btn btn-outline-success _effect--ripple" wire:click="verifyEmailManually">
                            <i class="bx bx-check-circle me-1"></i>
                            Verify Email Manually
                        </button>

                        <button class="btn btn-outline-primary _effect--ripple" wire:click="resendVerificationEmail">
                            <i class="bx bx-mail-send me-1"></i>
                            Resend Verification Email
                        </button>
                    @endif

                    @if ($user->isBanned())
                        <button class="btn btn-outline-success _effect--ripple" wire:click="unbanUser">
                            <i class="bx bx-user-check me-1"></i>
                            Unban User
                        </button>
                    @else
                        <button class="btn btn-outline-danger _effect--ripple" wire:click="$js.confirmBanUser()">
                            <i class="bx bx-user-x me-1"></i>
                            Ban User
                        </button>
                    @endif

                    <button class="btn btn-outline-danger _effect--ripple" wire:click="$js.confirmDelete()">
                        <i class="bx bx-trash me-1"></i>
                        Delete User
                    </button>

                </div>
            </div>
        </div>
        @if ($user->role == 'user')
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Manage Plan</h5>
                    </div>
                    <div class="card-body">
                        <h5>Active Plan</h5>
                        <ul class="ps-0 fs-6 border-bottom pb-2">
                            @if ($user->activePlan)
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <p class="w-30 fw-semibold text-primary-light">Plan</p>
                                    <p class="w-70 fw-medium">: {{ $user->activePlan->plan->name }} </p>
                                </li>
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <p class="w-30 text-md fw-semibold text-primary-light"> Expires At </p>
                                    <p class="w-70 text-secondary-light fw-normal">:
                                        {{ $user->activePlan->end_date->toDayDateTimeString() }} </p>
                                </li>
                                <button class="btn btn-outline-info mb-2 _effect--ripple waves-effect waves-light"
                                    wire:click="$js.confirmCancelPlan()">
                                    Cancel Plan
                                </button>
                            @else
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <p class="w-20 text-md fw-semibold text-primary-light">Plan</p>
                                    <p class="w-80 text-secondary-light fw-normal">: No Active Plan </p>
                                </li>
                            @endif
                        </ul>
                        <h6>Add or Extend Plan</h6>
                        @if ($errors->any())
                            <x-alert type="warning" message="{{ $errors->first() }}" />
                        @endif
                        <div class="form-group mb-3">
                            <select class="form-select w-100" id="exampleFormControlSelect1" wire:model="selectedPlan">
                                <option value="" selected>Select Plan</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}
                                        ({{ $plan->duration }}
                                        {{ Str::plural($plan->duration_unit, $plan->duration) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-outline-info _effect--ripple waves-effect waves-light"
                            wire:click="$js.confirmAddPlan()">
                            @if ($user->activePlan)
                                Extend Plan
                            @else
                                Add Plan
                            @endif
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payment History</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse ($user->purchases->sortByDesc('created_at') as $purchase)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="me-auto">
                                        <div class="fw-bold title">{{ $purchase->plan->name }}</div>
                                        <p class="sub-title mb-0">({{ $purchase->start_date->toFormattedDateString() }}
                                            -
                                            {{ $purchase->end_date->toFormattedDateString() }})
                                            - {{ Str::title($purchase->status) }}</p>
                                    </div>
                                    <span
                                        class="pay-pricing align-self-center me-3">${{ $purchase->amount_paid }}</span>
                                </div>
                            @empty
                                <div class="list-group-item">
                                    <p class="mb-0">No purchase history available.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@script
    <script>
        $js('confirmCancelPlan', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to cancel this plan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.cancelPurchase();
                }
            });
        });

        $js('confirmAddPlan', () => {
            Swal.fire({
                title: 'Confirm Plan Change?',
                text: 'Are you sure you want to add or extend this plan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.addPlan();
                }
            });
        });

        $js('confirmBanUser', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will prevent the user from accessing the system.',
                icon: 'warning',
                input: 'text',
                inputLabel: 'Reason for banning',
                inputPlaceholder: 'Enter reason for banning',
                showCancelButton: true,
                confirmButtonText: 'Yes, ban user!',
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('Reason is required');
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $wire.banUser(result.value);
                }
            });
        });

        $js('confirmDelete', () => {
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
                    $wire.deleteUser();
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
