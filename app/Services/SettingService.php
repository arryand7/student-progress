<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SettingService
{
    private const CACHE_KEY = 'settings.all';
    private const SENSITIVE_KEYS = [
        'smtp.password',
        'sso.client_secret',
    ];

    public function all(): array
    {
        if (!Schema::hasTable('settings')) {
            return [];
        }

        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::query()
                ->get()
                ->reduce(function (array $carry, Setting $setting) {
                    $value = $this->maybeDecrypt($setting->key, $setting->value);
                    data_set($carry, $setting->key, $value);

                    return $carry;
                }, []);
        });
    }

    public function get(string $key, $default = null)
    {
        return data_get($this->all(), $key, $default);
    }

    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $value = $this->maybeEncrypt($key, $value);
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }

    public function applyConfig(): void
    {
        $settings = $this->all();
        if (empty($settings)) {
            return;
        }

        if (Arr::has($settings, 'general.app_name')) {
            config(['app.name' => data_get($settings, 'general.app_name')]);
        }

        if (Arr::has($settings, 'smtp.host')) {
            config(['mail.mailers.smtp.host' => data_get($settings, 'smtp.host')]);
        }
        if (Arr::has($settings, 'smtp.port')) {
            config(['mail.mailers.smtp.port' => data_get($settings, 'smtp.port')]);
        }
        if (Arr::has($settings, 'smtp.username')) {
            config(['mail.mailers.smtp.username' => data_get($settings, 'smtp.username')]);
        }
        if (Arr::has($settings, 'smtp.password')) {
            config(['mail.mailers.smtp.password' => data_get($settings, 'smtp.password')]);
        }
        if (Arr::has($settings, 'smtp.encryption')) {
            config(['mail.mailers.smtp.encryption' => data_get($settings, 'smtp.encryption')]);
        }
        if (Arr::has($settings, 'smtp.from_address')) {
            config(['mail.from.address' => data_get($settings, 'smtp.from_address')]);
        }
        if (Arr::has($settings, 'smtp.from_name')) {
            config(['mail.from.name' => data_get($settings, 'smtp.from_name')]);
        }

        if (Arr::has($settings, 'sso.base_url')) {
            config(['sso.base_url' => data_get($settings, 'sso.base_url')]);
        }
        if (Arr::has($settings, 'sso.client_id')) {
            config(['sso.client_id' => data_get($settings, 'sso.client_id')]);
        }
        if (Arr::has($settings, 'sso.client_secret')) {
            config(['sso.client_secret' => data_get($settings, 'sso.client_secret')]);
        }
        if (Arr::has($settings, 'sso.redirect_uri')) {
            config(['sso.redirect_uri' => data_get($settings, 'sso.redirect_uri')]);
        }
        if (Arr::has($settings, 'sso.authorize_endpoint')) {
            config(['sso.authorize_endpoint' => data_get($settings, 'sso.authorize_endpoint')]);
        }
        if (Arr::has($settings, 'sso.token_endpoint')) {
            config(['sso.token_endpoint' => data_get($settings, 'sso.token_endpoint')]);
        }
        if (Arr::has($settings, 'sso.userinfo_endpoint')) {
            config(['sso.userinfo_endpoint' => data_get($settings, 'sso.userinfo_endpoint')]);
        }
        if (Arr::has($settings, 'sso.scopes')) {
            config(['sso.scopes' => data_get($settings, 'sso.scopes')]);
        }
    }

    private function maybeEncrypt(string $key, $value)
    {
        if (!in_array($key, self::SENSITIVE_KEYS, true)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return null;
        }

        return Crypt::encryptString((string) $value);
    }

    private function maybeDecrypt(string $key, $value)
    {
        if (!in_array($key, self::SENSITIVE_KEYS, true)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString((string) $value);
        } catch (Throwable $e) {
            return $value;
        }
    }
}
