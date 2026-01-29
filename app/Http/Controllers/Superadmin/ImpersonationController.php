<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Start impersonation as target user.
     */
    public function start(Request $request, User $user)
    {
        $request->session()->put('impersonator_id', $request->user()->id);

        $this->auditService->logImpersonation($user, 'Superadmin impersonation');

        Auth::login($user);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Impersonasi berhasil dimulai.');
    }

    /**
     * Stop impersonation and return to original user.
     */
    public function stop(Request $request)
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if ($impersonatorId) {
            $impersonator = User::find($impersonatorId);
            if ($impersonator) {
                Auth::login($impersonator);
            }
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Impersonasi dihentikan.');
    }
}
