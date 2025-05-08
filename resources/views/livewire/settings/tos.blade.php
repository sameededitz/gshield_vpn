@section('title', 'Terms of Service and Privacy Policy')
<div>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Home</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Terms of Service and Privacy Policy</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Terms of Service and Privacy Policy</h5>
                </div>
                <div class="card-body">
                    <form class="row g-2" wire:submit.prevent="save">
                        <div class="col-12" wire:ignore>
                            <label class="form-label" for="privacy_policy">Privacy Policy</label>
                            <textarea name="privacy_policy" id="myeditorinstance" wire:model="privacy_policy" class="form-control tinymce-editor"></textarea>
                        </div>
                        <div class="col-12" wire:ignore>
                            <label class="form-label" for="terms_of_service">Terms of Service</label>
                            <textarea id="tosEditor" name="tos" wire:model="tos" class="form-control tinymce-editor"></textarea>
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
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            skin: 'oxide',
            plugins: 'code table lists',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table',
            setup: function(editor) {
                editor.on('blur', function() {
                    let content = editor.getContent();
                    let livewireField = editor.getElement().getAttribute('wire:model');
                    @this.set(livewireField, content);
                });
                editor.on('change', function() {
                    let content = editor.getContent();
                    let livewireField = editor.getElement().getAttribute('wire:model');
                    @this.set(livewireField, content);
                });
            },
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
    <script src="https://cdn.tiny.cloud/1/profov2dlbtwaoggjfvbncp77rnjhgyfnl3c2hx3kzpmhif1/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
@endsection
