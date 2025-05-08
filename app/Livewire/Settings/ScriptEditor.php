<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\File;
use Livewire\Component;

class ScriptEditor extends Component
{
    public array $files = [];
    public string $selectedFile = '';
    public string $fileContent = '';

    public function mount()
    {
        $this->loadFiles();
    }

    public function loadFiles()
    {
        $path = storage_path('app/private/scripts');
        $this->files = collect(File::files($path))
            ->filter(fn($file) => $file->getExtension() === 'sh')
            ->map(fn($file) => [
                'name' => $file->getFilename(),
                'last_modified' => $file->getMTime(), // or ->getATime()
            ])
            ->values()
            ->toArray();
    }

    public function openFile($filename)
    {
        if ($this->selectedFile) {
            $this->dispatch('sweetAlert', title: 'Warning', message: 'Please save or close the current file before opening a new one.', type: 'warning');
            return;
        }

        $path = storage_path("app/private/scripts/{$filename}");
        if (File::exists($path)) {
            $this->selectedFile = $filename;
            $this->fileContent = File::get($path);
            $this->dispatch('openEditor');
        }
    }

    public function saveFile()
    {
        $path = storage_path("app/private/scripts/{$this->selectedFile}");
        File::put($path, $this->fileContent);
        $this->dispatch('sweetAlert', title: 'Success', message: 'File saved successfully.', type: 'success');


        $this->closeFile();
        $this->loadFiles();
    }

    public function closeFile()
    {
        $this->selectedFile = '';
        $this->fileContent = '';
    }

    public function render()
    {
        /** @disregard @phpstan-ignore-line */
        return view('livewire.settings.script-editor')
            ->extends('layouts.app')
            ->section('content');
    }
}
