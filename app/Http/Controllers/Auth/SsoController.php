<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    /**
     * Redirect user to Gate SSO authorization endpoint.
     */
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('sso_state', $state);

        $baseUrl = rtrim(config('sso.base_url'), '/');
        $authorizeUrl = $baseUrl . config('sso.authorize_endpoint');

        $query = http_build_query([
            'client_id' => config('sso.client_id'),
            'redirect_uri' => config('sso.redirect_uri'),
            'response_type' => 'code',
            'scope' => config('sso.scopes'),
            'state' => $state,
        ]);

        return redirect()->away($authorizeUrl . '?' . $query);
    }

    /**
     * Handle Gate SSO callback and sign in locally.
     */
    public function callback(Request $request)
    {
        $state = $request->input('state');

        if (!$state || $state !== $request->session()->pull('sso_state')) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'SSO state tidak valid. Silakan coba lagi.']);
        }

        $code = $request->input('code');
        if (!$code) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Kode SSO tidak ditemukan.']);
        }

        $baseUrl = rtrim(config('sso.base_url'), '/');

        $tokenResponse = Http::asForm()->post($baseUrl . config('sso.token_endpoint'), [
            'grant_type' => 'authorization_code',
            'client_id' => config('sso.client_id'),
            'client_secret' => config('sso.client_secret'),
            'redirect_uri' => config('sso.redirect_uri'),
            'code' => $code,
        ]);

        if (!$tokenResponse->ok()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Gagal mendapatkan token dari SSO.']);
        }

        $accessToken = $tokenResponse->json('access_token');
        if (!$accessToken) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Token akses SSO tidak valid.']);
        }

        $userInfoResponse = Http::withToken($accessToken)
            ->get($baseUrl . config('sso.userinfo_endpoint'));

        if (!$userInfoResponse->ok()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Gagal mengambil data pengguna dari SSO.']);
        }

        $claims = $userInfoResponse->json();

        try {
            $user = $this->upsertUserFromClaims($claims);
        } catch (\Throwable $e) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => $e->getMessage()]);
        }

        Auth::login($user, true);

        return redirect()->intended('/dashboard');
    }

    /**
     * Create or update local user from SSO claims.
     */
    protected function upsertUserFromClaims(array $claims): User
    {
        $ssoId = $claims['sub'] ?? null;
        $email = $claims['email'] ?? null;

        if (!$ssoId || !$email) {
            throw new \Exception('Data SSO tidak lengkap (sub/email).');
        }

        $user = User::where('sso_id', $ssoId)->first();
        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
            $user = new User();
            $user->password = Str::random(32);
        }

        $user->name = $claims['name'] ?? $user->name ?? $email;
        $user->email = $email;
        $user->sso_id = $ssoId;
        $user->is_active = true;

        $metadata = $user->metadata ?? [];
        $user->metadata = array_merge($metadata, [
            'type' => $claims['type'] ?? null,
            'nis' => $claims['nis'] ?? null,
            'nip' => $claims['nip'] ?? null,
            'sso_roles' => $claims['roles'] ?? [],
        ]);

        $user->save();

        $this->syncRoles($user, $claims['roles'] ?? []);

        return $user;
    }

    /**
     * Sync local roles based on SSO roles.
     */
    protected function syncRoles(User $user, array $ssoRoles): void
    {
        $roleMap = config('sso.role_map', []);

        $mappedRoles = collect($ssoRoles)
            ->map(fn($role) => $roleMap[$role] ?? null)
            ->filter()
            ->unique()
            ->values();

        if ($mappedRoles->isEmpty()) {
            throw new \Exception('Role SSO tidak memiliki akses ke aplikasi ini.');
        }

        $roleIds = Role::whereIn('name', $mappedRoles)->pluck('id')->toArray();
        $user->roles()->sync($roleIds);
    }
}
