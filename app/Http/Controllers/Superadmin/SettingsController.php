<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Show settings page.
     */
    public function index(SettingService $settings)
    {
        return view('superadmin.settings.index', [
            'settings' => $settings->all(),
        ]);
    }

    /**
     * Update settings based on section.
     */
    public function update(Request $request, SettingService $settings)
    {
        $section = $request->input('section');

        return match ($section) {
            'general' => $this->updateGeneral($request, $settings),
            'smtp' => $this->updateSmtp($request, $settings),
            'sso' => $this->updateSso($request, $settings),
            default => redirect()->route('superadmin.settings.index')
                ->with('error', 'Bagian pengaturan tidak dikenali.'),
        };
    }

    private function updateGeneral(Request $request, SettingService $settings)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_tagline' => 'nullable|string|max:255',
            'app_description' => 'nullable|string|max:1000',
            'app_logo' => 'nullable|image|max:2048',
            'remove_logo' => 'nullable|boolean',
        ]);

        $updates = [
            'general.app_name' => $validated['app_name'] ?? null,
            'general.app_tagline' => $validated['app_tagline'] ?? null,
            'general.app_description' => $validated['app_description'] ?? null,
        ];

        if ($request->boolean('remove_logo')) {
            $currentLogo = $settings->get('general.app_logo');
            if ($currentLogo) {
                Storage::disk('public')->delete($currentLogo);
            }
            $updates['general.app_logo'] = null;
        }

        if ($request->hasFile('app_logo')) {
            $currentLogo = $settings->get('general.app_logo');
            if ($currentLogo) {
                Storage::disk('public')->delete($currentLogo);
            }
            $path = $request->file('app_logo')->store('settings', 'public');
            $updates['general.app_logo'] = $path;
        }

        $settings->setMany($updates);

        return redirect()
            ->route('superadmin.settings.index')
            ->with('success', 'Pengaturan umum berhasil diperbarui.');
    }

    private function updateSmtp(Request $request, SettingService $settings)
    {
        $validated = $request->validate([
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
            'smtp_from_name' => 'nullable|string|max:255',
            'smtp_from_address' => 'nullable|email|max:255',
        ]);

        $updates = [
            'smtp.host' => $validated['smtp_host'] ?? null,
            'smtp.port' => $validated['smtp_port'] ?? null,
            'smtp.username' => $validated['smtp_username'] ?? null,
            'smtp.encryption' => $validated['smtp_encryption'] === 'none' ? null : ($validated['smtp_encryption'] ?? null),
            'smtp.from_name' => $validated['smtp_from_name'] ?? null,
            'smtp.from_address' => $validated['smtp_from_address'] ?? null,
        ];

        if (!empty($validated['smtp_password'])) {
            $updates['smtp.password'] = $validated['smtp_password'];
        }

        $settings->setMany($updates);

        return redirect()
            ->route('superadmin.settings.index')
            ->with('success', 'Pengaturan SMTP berhasil diperbarui.');
    }

    private function updateSso(Request $request, SettingService $settings)
    {
        $validated = $request->validate([
            'sso_base_url' => 'nullable|url|max:255',
            'sso_client_id' => 'nullable|string|max:255',
            'sso_client_secret' => 'nullable|string|max:255',
            'sso_redirect_uri' => 'nullable|url|max:255',
            'sso_authorize_endpoint' => 'nullable|string|max:255',
            'sso_token_endpoint' => 'nullable|string|max:255',
            'sso_userinfo_endpoint' => 'nullable|string|max:255',
            'sso_scopes' => 'nullable|string|max:255',
        ]);

        $updates = [
            'sso.base_url' => $validated['sso_base_url'] ?? null,
            'sso.client_id' => $validated['sso_client_id'] ?? null,
            'sso.redirect_uri' => $validated['sso_redirect_uri'] ?? null,
            'sso.authorize_endpoint' => $validated['sso_authorize_endpoint'] ?? null,
            'sso.token_endpoint' => $validated['sso_token_endpoint'] ?? null,
            'sso.userinfo_endpoint' => $validated['sso_userinfo_endpoint'] ?? null,
            'sso.scopes' => $validated['sso_scopes'] ?? null,
        ];

        if (!empty($validated['sso_client_secret'])) {
            $updates['sso.client_secret'] = $validated['sso_client_secret'];
        }

        $settings->setMany($updates);

        return redirect()
            ->route('superadmin.settings.index')
            ->with('success', 'Pengaturan SSO berhasil diperbarui.');
    }
}
