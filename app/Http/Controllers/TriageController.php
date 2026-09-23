<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TriageScenario;
use App\Http\Requests\AnalyzeScenarioRequest;
use App\Http\Requests\CustomSandboxRequest;
use App\Http\Requests\FeedbackSubmissionRequest;
use App\Services\TriageService;
use App\Support\PresetCatalog;
use Illuminate\Http\JsonResponse;

class TriageController extends Controller
{
    public function __construct(
        protected TriageService $triage,
        protected PresetCatalog $presets
    ) {}

    /**
     * Evaluate predefined scenarios.
     */
    public function analyze(AnalyzeScenarioRequest $request): JsonResponse
    {
        $input = $request->inputMessage();

        $result = match ($request->scenario()) {
            TriageScenario::CustomerChat => $this->triage->triageCustomerChat($input),
            TriageScenario::SalesQualification => $this->triage->qualifySalesLead($input),
            TriageScenario::ReviewModeration => $this->triage->moderateReview($input),
            TriageScenario::BatchAnalysis => $this->triage->executeBatch($input),
        };

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    /**
     * Run custom rule sandbox evaluation.
     */
    public function sandbox(CustomSandboxRequest $request): JsonResponse
    {
        $result = $this->triage->evaluateCustom(
            text: $request->inputMessage(),
            mode: $request->mode(),
            params: $request->modeParams()
        );

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    /**
     * Submit user feedback with JevRule form validation.
     */
    public function submitFeedback(FeedbackSubmissionRequest $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Feedback passed validation.',
            'payload' => $request->validated(),
        ]);
    }

    /**
     * Scenario presets for the console.
     */
    public function presets(): JsonResponse
    {
        return response()->json($this->presets->all());
    }
}
