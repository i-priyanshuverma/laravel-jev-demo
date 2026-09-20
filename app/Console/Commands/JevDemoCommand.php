<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\TriageService;
use Illuminate\Console\Command;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class JevDemoCommand extends Command
{
    protected $signature = 'jev:demo';

    protected $description = 'Run interactive evaluations using the Laravel Jev decision engine';

    public function handle(TriageService $triage): int
    {
        intro('Laravel Jev Decision Engine Demo');

        $scenario = select(
            label: 'Select evaluation scenario:',
            options: [
                'customer_chat' => 'Customer Support Triage',
                'sales_qualification' => 'Lead Qualification',
                'review_moderation' => 'Review & Content Moderation',
                'batch_analysis' => 'Single Round-Trip Batch Analysis',
                'custom' => 'Custom Rule Sandbox',
            ],
            default: 'customer_chat'
        );

        if ($scenario === 'custom') {
            $this->runCustomSandbox($triage);

            return self::SUCCESS;
        }

        $defaultMessage = match ($scenario) {
            'customer_chat' => 'Our production database crashed and returning 500 errors on checkout. Please escalate!',
            'sales_qualification' => 'We are evaluating your platform for our 500 engineers. Looking for enterprise pricing.',
            'review_moderation' => 'THIS PRODUCT IS A COMPLETE SCAM DO NOT BUY FROM THESE THIEVES.',
            'batch_analysis' => 'Customer card renewal failed. Account past due 3 days. Send dunning notice.',
        };

        $input = text(
            label: 'Enter input text to evaluate:',
            default: $defaultMessage,
            required: true
        );

        $result = match ($scenario) {
            'customer_chat' => $triage->triageCustomerChat($input),
            'sales_qualification' => $triage->qualifySalesLead($input),
            'review_moderation' => $triage->moderateReview($input),
            'batch_analysis' => $triage->executeBatch($input),
        };

        $rows = [];
        foreach ($result as $key => $val) {
            if ($key === 'code_sample') {
                continue;
            }
            $formattedVal = is_bool($val) ? ($val ? 'true' : 'false') : (string) $val;
            $rows[] = [$key, $formattedVal];
        }

        table(['Metric / Decision', 'Value'], $rows);

        $this->newLine();
        $this->line('<fg=gray>PHP Code Executed:</>');
        $this->line('<fg=cyan>'.$result['code_sample'].'</>');
        $this->newLine();

        outro("Execution completed in {$result['duration_ms']}ms");

        return self::SUCCESS;
    }

    protected function runCustomSandbox(TriageService $triage): void
    {
        $mode = select(
            label: 'Select decision mode:',
            options: [
                'boolean' => 'Jev::is() — Boolean Decision',
                'choose' => 'Jev::choose() — Categorical Choice',
                'score' => 'Jev::score() — Numerical Scale',
            ]
        );

        $text = text(
            label: 'Enter test text:',
            default: 'Can you please issue a refund for duplicate charge #4092?',
            required: true
        );

        $params = [];

        if ($mode === 'boolean') {
            $params['criteria'] = text('Enter criteria to check:', default: 'a refund or billing request');
            $params['threshold'] = 0.80;
        } elseif ($mode === 'choose') {
            $optionsStr = text('Enter comma-separated options:', default: 'billing, technical_support, sales');
            $params['options'] = array_map('trim', explode(',', $optionsStr));
        } elseif ($mode === 'score') {
            $params['criteria'] = text('Enter criteria to rate:', default: 'urgency');
            $levelsStr = text('Enter ordered levels (comma-separated):', default: 'low, medium, high, critical');
            $params['levels'] = array_map('trim', explode(',', $levelsStr));
        }

        $res = $triage->evaluateCustom($text, $mode, $params);

        $this->newLine();
        table(['Key', 'Value'], [
            ['Mode', $mode],
            ['Result', json_encode($res['result'])],
            ['Latency', "{$res['duration_ms']}ms"],
            ['Code', $res['code_sample']],
        ]);

        outro('Custom sandbox evaluation complete.');
    }
}
