<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Services\EvaluationService;
use Illuminate\Http\Request;

class EvaluationLockController extends Controller
{
    protected EvaluationService $evaluationService;

    public function __construct(EvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    /**
     * Unlock an evaluation (superadmin only).
     */
    public function unlock(Request $request, Evaluation $evaluation)
    {
        $reason = $request->input('reason', 'Manual unlock by superadmin');

        try {
            $this->evaluationService->unlockEvaluation($evaluation, $request->user(), $reason);

            return redirect()
                ->route('pembina.evaluations.show', $evaluation)
                ->with('success', 'Evaluasi berhasil dibuka.');
        } catch (\Exception $e) {
            return redirect()
                ->route('pembina.evaluations.show', $evaluation)
                ->with('error', $e->getMessage());
        }
    }
}
