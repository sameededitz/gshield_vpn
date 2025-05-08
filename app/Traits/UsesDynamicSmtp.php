<?php

namespace App\Traits;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

trait UsesDynamicSmtp
{
    public function applySmtpConfig()
    {
        $smtp = SmtpSetting::first();

        if ($smtp) {
            Config::set('mail.mailers.smtp.host', $smtp->host);
            Config::set('mail.mailers.smtp.port', $smtp->port);
            Config::set('mail.mailers.smtp.username', $smtp->username);
            Config::set('mail.mailers.smtp.password', $smtp->password);
            Config::set('mail.from.address', $smtp->from_address);
            Config::set('mail.from.name', $smtp->from_name);
        } else {
            Log::error('SMTP settings not found in the database.');
        }
    }
}
