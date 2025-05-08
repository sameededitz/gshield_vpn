<?php

namespace App\Livewire\Admin;

use App\Models\Server;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;

class ServerEdit extends Component
{
    use WithFileUploads, WithFilePond;

    public Server $server;
    public $image, $name, $android = false, $ios = false, $macos = false, $windows = false, $longitude, $latitude, $type, $status;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'android' => 'required|boolean',
            'ios' => 'required|boolean',
            'macos' => 'required|boolean',
            'windows' => 'required|boolean',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'type' => 'required|in:free,premium',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20420',
        ];
    }

    public function mount(Server $server)
    {
        $this->server = $server;
        $this->name = $server->name;
        $this->android = $server->android;
        $this->ios = $server->ios;
        $this->macos = $server->macos;
        $this->windows = $server->windows;
        $this->longitude = $server->longitude;
        $this->latitude = $server->latitude;
        $this->type = $server->type;
        $this->status = $server->status;
    }

    public function store()
    {
        $this->validate();

        $this->server->update([
            'name' => $this->name,
            'android' => $this->android,
            'ios' => $this->ios,
            'macos' => $this->macos,
            'windows' => $this->windows,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'type' => $this->type,
            'status' => $this->status,
        ]);

        if ($this->image) {
            $this->server->clearMediaCollection('image');
            $this->server->addMedia($this->image->getRealPath())
                ->usingFileName(time() . '_server_' . $this->server->id . '.' . $this->image->getClientOriginalExtension())
                ->toMediaCollection('image');
        }

        $this->reset(['image','name', 'android', 'ios', 'macos', 'windows','longitude', 'latitude', 'type', 'status']);

        return redirect()->intended(route('servers.all'))->with('message', 'Server added successfully.');
    }

    public function render()
    {
        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.server-edit')
            ->extends('layouts.app')
            ->section('content');
    }
}
