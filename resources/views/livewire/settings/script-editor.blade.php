@section('title', 'Script Editor')
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.14/codemirror.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.14/theme/dracula.min.css" />
@endsection
<div>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Home</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Scripts</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="fm-search">
                        <div class="mb-0">
                            <div class="input-group input-group-lg"> <span class="input-group-text bg-transparent"><i
                                        class="bx bx-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search the files">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-striped table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Name <i class="bx bx-up-arrow-alt ms-2"></i>
                                    </th>
                                    <th>Last Modified</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $file)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <i class="bx bxs-file-pdf me-2 font-24"></i>
                                                </div>
                                                <div class="font-weight-bold">{{ $file['name'] }}</div>
                                            </div>
                                        </td>
                                        <td>{{ \Carbon\Carbon::createFromTimestamp($file['last_modified'])->diffForHumans() }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" wire:click="openFile('{{ $file['name'] }}')"
                                                    @if ($selectedFile) disabled @endif
                                                    class="btn btn-outline-warning d-flex align-items-center justify-content-center">
                                                    <iconify-icon icon="material-symbols:edit" width="20"
                                                        height="20"></iconify-icon>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if ($selectedFile)
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-2">Editing: {{ $selectedFile }}</h5>

                        @if (session()->has('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="alert alert-warning">
                            <strong>Warning!</strong> Please be careful when editing this file. Make sure to backup
                            your files before making any changes.
                        </div>
                        <div wire:ignore>
                            <textarea id="code-editor" hidden>{{ $fileContent }}</textarea>
                        </div>

                        <div class="d-flex align-items-center mt-3 gap-2">
                            <button type="button" wire:click="saveFile"
                                class="btn btn-outline-success d-flex align-items-center justify-content-center">
                                💾 Save
                            </button>
                            <button type="button" wire:click="closeFile"
                                class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                                ❌ Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@script
    <script>
        $wire.on('openEditor', () => {
            setTimeout(() => {
                let editor;

                const textarea = document.getElementById('code-editor');
                if (!textarea) return;
                editor = CodeMirror.fromTextArea(textarea, {
                    mode: 'shell',
                    theme: 'dracula',
                    lineNumbers: true,
                });

                editor.on('change', () => {
                    @this.set('fileContent', editor.getValue());
                });
            }, 2000);
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.14/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.14/mode/shell/shell.min.js"></script>
@endsection
