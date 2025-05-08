@section('title', 'Manage Ticket')
<div>
    <div class="chat-wrapper">
        <div class="chat-header d-flex align-items-center">
            <div>
                <h5 class="mb-1 font-weight-bold">Ticket: {{ Str::limit($ticket->subject, 50) }} </h5>
            </div>
            <div class="chat-top-header-menu d-flex align-items-center gap-2 ms-auto">
                @if ($ticket->status == 'open')
                    <span class="badge bg-success">Open</span>
                @elseif ($ticket->status == 'closed')
                    <span class="badge bg-danger">Closed</span>
                @else
                    <span class="badge bg-warning">Pending</span>
                @endif
                @if ($ticket->status !== 'closed')
                    <button type="button" wire:click="$js.updateStatus({{ $ticket->id }}, 'closed')"
                        class="d-flex align-items-center justify-content-center">
                        <Iconify-icon icon="material-symbols:close-rounded" width="20"
                            height="20"></Iconify-icon>
                    </button>
                @endif
                @if ($ticket->status !== 'open')
                    <button type="button" wire:click="$js.updateStatus({{ $ticket->id }}, 'open')"
                        class="d-flex align-items-center justify-content-center">
                        <Iconify-icon icon="material-symbols:check-circle-outline" width="20"
                            height="20"></Iconify-icon>
                    </button>
                @endif
                @if ($ticket->status !== 'pending')
                    <button type="button" wire:click="$js.updateStatus({{ $ticket->id }}, 'pending')"
                        class="d-flex align-items-center justify-content-center">
                        <Iconify-icon icon="material-symbols:hourglass-top" width="20"
                            height="20"></Iconify-icon>
                    </button>
                @endif
            </div>
        </div>
        <div class="chat-content">
            @forelse ($ticket->messages as $message)
                <div class="chat-content-{{ $message->is_admin ? 'rightside' : 'leftside' }} mb-2 position-relative">
                    <div class="d-flex {{ $message->is_admin ? 'ms-auto' : 'ms-0' }} flex-column">
                        <div class="flex-grow-1 ms-2">
                            <p class="mb-0 chat-time {{ $message->is_admin ? 'text-end' : 'text-start' }}">
                                {{ $message->is_admin ? 'You' : $message->user->name }},
                                {{ $message->created_at->diffForHumans() }}</p>
                            @if ($message->getMedia('attachments')->count())
                                <div
                                    class="flex-grow-1 d-flex gap-2 mb-2 flex-wrap justify-content-{{ $message->is_admin ? 'end' : 'start' }}">
                                    @foreach ($message->getMedia('attachments') as $media)
                                        @if ($editingMessageId === $message->id)
                                            @if (in_array($media->uuid, $existingImages))
                                                <div class="position-relative">
                                                    <img src="{{ $media->getFullUrl() }}" alt="attachment"
                                                        class="rounded border"
                                                        style="width: 70px; height: 70px; object-fit: cover;" />
                                                    <button wire:click="$js.confirmImageDelete('{{ $media->uuid }}')"
                                                        class="btn btn-sm btn-danger p-0 position-absolute translate-middle rounded-circle"
                                                        style="width: 16px;height: 16px;line-height: 1;top: 12px;right: -4px;color: #fff;">
                                                        <iconify-icon icon="mdi:close" width="14"
                                                            height="14"></iconify-icon>
                                                    </button>
                                                </div>
                                            @endif
                                        @else
                                            <a href="{{ $media->getFullUrl() }}" target="_blank">
                                                <img src="{{ $media->getFullUrl() }}" alt="attachment"
                                                    class="rounded border"
                                                    style="width: 70px; height: 70px; object-fit: cover;" />
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            <div
                                class="d-flex gap-2 align-items-center justify-content-{{ $message->is_admin ? 'end' : 'start' }}">
                                <div class="dropdown order-{{ $message->is_admin ? '1' : '2' }}">
                                    <button class="btn btn-light btn-rounded width-auto d-flex" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <iconify-icon icon="bi:three-dots-vertical" width="20"
                                            height="20"></iconify-icon>
                                    </button>
                                    <ul
                                        class="dropdown-menu chat-action dropdown-menu-{{ $message->is_admin ? 'end' : 'start' }}">
                                        <li>
                                            <div
                                                class="dropdown-item mb-1 d-flex align-items-center gap-2 cursor-pointer">
                                                <button type="button" class="width-auto d-flex transparent-btn"
                                                    wire:click="$js.copyMessage({{ $message->id }})">
                                                    <iconify-icon icon="solar:copy-line-duotone" width="24"
                                                        height="24"></iconify-icon>
                                                </button>
                                                <p class="mb-0">Copy</p>
                                            </div>
                                        </li>
                                        @if ($message->is_admin && $loop->last)
                                            <li>
                                                <div class="dropdown-item mb-1 d-flex align-items-center gap-2 cursor-pointer"
                                                    wire:click="startEditing({{ $message->id }})">
                                                    <button type="button" class="width-auto d-flex transparent-btn">
                                                        <iconify-icon icon="material-symbols:edit" width="24"
                                                            height="24"></iconify-icon>
                                                    </button>
                                                    <p class="mb-0">Edit</p>
                                                </div>
                                            </li>
                                        @endif
                                        <!-- Delete button (visible only for last admin message) -->
                                        @if ($message->is_admin && $loop->last)
                                            <li>
                                                <div class="dropdown-item mb-1 d-flex align-items-center gap-2 cursor-pointer"
                                                    wire:click="$js.confirmMessageDelete({{ $message->id }})">
                                                    <button type="button" class="width-auto d-flex transparent-btn">
                                                        <iconify-icon icon="mingcute:delete-2-line" width="24"
                                                            height="24"></iconify-icon>
                                                    </button>
                                                    <p class="mb-0">Delete</p>
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                @if ($editingMessageId === $message->id)
                                    <div class="d-flex flex-column gap-2">
                                        <textarea wire:model.defer="editMessageContent" class="form-control" rows="2"></textarea>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-sm btn-success"
                                                wire:click="updateMessage">Save</button>
                                            <button class="btn btn-sm btn-secondary"
                                                wire:click="cancelEditing">Cancel</button>
                                        </div>
                                    </div>
                                @else
                                    <p class="chat-{{ $message->is_admin ? 'right' : 'left' }}-msg mb-0 order-{{ $message->is_admin ? '2' : '1' }}"
                                        id="chat-message-{{ $message->id }}">
                                        {{ $message->message }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="chat-content-leftside">
                    <div class="d-flex ms-0 text-center">
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0 chat-right-msg">No messages yet</h6>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="chat-footer d-flex flex-column">
            @if ($uploadedImagesCount > 0)
                <div class="image-info text-info px-2 pt-1 small align-self-start d-flex align-items-center gap-2">
                    <span>
                        {{ $uploadedImagesCount }} image{{ $uploadedImagesCount > 1 ? 's' : '' }} attached to this
                        reply
                    </span>

                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#imageUploadModel"
                        class="text-decoration-underline">
                        View
                    </a>

                    <a href="javascript:;" wire:click="resetForm" class="text-danger text-decoration-underline">
                        Clear
                    </a>
                </div>
            @endif
            <div class="d-flex align-items-center w-100">
                <div class="flex-grow-1 pe-1 py-2">
                    <div class="input-group">
                        <textarea class="form-control reply-input" placeholder="Type your reply..." wire:model.defer="message"
                            @keydown.enter="!$event.shiftKey && ($event.preventDefault(), $wire.sendReply())"></textarea>
                    </div>
                </div>
                <div class="chat-footer-menu">
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#imageUploadModel">
                        <i class='bx bx-file'></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageUploadModel" tabindex="-1" wire:ignore.self
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Upload Images
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" enctype="multipart/form-data">
                        <div class="col-12">
                            <label for="image" class="form-label">Image (Max 5)</label>
                            <x-filepond::upload x-ref="filepond" wire:model="attachments"
                                allowImageValidateSize="true" maxFileSize="20MB" maxParallelUploads="5"
                                allowFileTypeValidation="true" maxFiles="5" allowReorder="true"
                                acceptedFileTypes="image/jpeg, image/png, image/jpg" multiple />
                            @error('attachments')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-outline-info d-flex align-items-center justify-content-center"
                        data-bs-dismiss="modal">Close</button>
                    <button type="button"
                        class="btn btn-outline-success d-flex align-items-center justify-content-center"
                        wire:click="resetForm">
                        Clear Images
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        new PerfectScrollbar('.reply-input', {
            wheelPropagation: true
        });

        $js('updateStatus', (id, status) => {
            let actionText = status === 'open' ? 'Reopen' : (status === 'pending' ? 'mark as pending' : 'close');
            let actionBtnText = status === 'open' ? 'Yes, Reopen it!' : (status === 'pending' ?
                'Yes, Mark as Pending!' : 'Yes, Close it!');

            Swal.fire({
                title: `Are you sure you want to ${actionText} this Ticket?`,
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: actionBtnText
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.updateStatus(id, status);
                }
            });
        });

        $js('copyMessage', (messageId) => {
            // Get the message content based on the message ID
            const messageContent = document.getElementById('chat-message-' + messageId).innerText;

            // Copy the message content to the clipboard
            navigator.clipboard.writeText(messageContent).then(() => {
                // Trigger a toast notification after successfully copying the message
                Swal.fire({
                    text: 'Message copied successfully!',
                    icon: 'success',
                    position: 'top-end',
                    toast: true,
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch((err) => {
                // Handle any error that occurs while copying
                Swal.fire({
                    text: 'Failed to copy message!',
                    icon: 'error',
                    position: 'top-end',
                    toast: true,
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });


        $js('confirmImageDelete', (imageId) => {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This image will be permanently deleted and cannot be recovered!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteImage(imageId);
                }
            });
        });

        $js('confirmMessageDelete', (messageId) => {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This message will be permanently deleted and cannot be recovered!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteMessage(messageId);
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
    </script>
@endscript
@section('scripts')
    @filepondScripts
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
@endsection
