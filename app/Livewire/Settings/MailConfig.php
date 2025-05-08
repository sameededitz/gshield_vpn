<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Artisan;

class MailConfig extends Component
{
    public $mail_host, $mail_port, $mail_username, $mail_password, $mail_from_address, $mail_from_name;

    public function mount()
    {
        $settings = SmtpSetting::first();

        $this->mail_host = $settings->host ?? config('mail.mailers.smtp.host');
        $this->mail_port = $settings->port ?? config('mail.mailers.smtp.port');
        $this->mail_username = $settings->username ?? config('mail.mailers.smtp.username');
        $this->mail_password = $settings->password ?? config('mail.mailers.smtp.password');
        $this->mail_from_address = $settings->from_address ?? config('mail.from.address');
        $this->mail_from_name = $settings->from_name ?? config('mail.from.name');
    }

    public function store()
    {
        $this->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        SmtpSetting::updateOrCreate(
            ['id' => 1], // only one row
            [
                'host' => $this->mail_host,
                'port' => $this->mail_port,
                'username' => $this->mail_username,
                'password' => $this->mail_password,
                'from_address' => $this->mail_from_address,
                'from_name' => $this->mail_from_name,
            ]
        );

        Artisan::call('config:clear');
        Artisan::call('queue:restart');

        $this->dispatch('sweetAlert', title: 'Success', message: 'Mail settings updated successfully.', type: 'success');
    }

    public function render()
    {
        /** @disregard @phpstan-ignore-line */
        return view('livewire.settings.mail-config')
            ->extends('layouts.app')
            ->section('content');
    }
}
