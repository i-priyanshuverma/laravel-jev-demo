<?php

declare(strict_types=1);

namespace App\Services;

use Priyanshu\LaravelJev\Facades\Jev;

class TriageService
{
    public function __construct(
        protected bool $simulate = false
    ) {
        $this->simulate = (bool) config('jev.simulate', env('JEV_SIMULATE', true))
            || empty(config('jev.api_key'));

        if ($this->simulate && ! Jev::isFaking()) {
            Jev::fake();
        }
    }

    /**
     * Customer support inbound chat triage.
     */
    public function triageCustomerChat(string $message): array
    {
        $start = hrtime(true);

        $isSpam = Jev::is($message, 'spam, crypto promotion, or advertising', 0.80);

        $department = Jev::choose($message, [
            'billing',
            'technical_support',
            'sales',
            'security_incident',
            'general_inquiry',
        ], default: 'general_inquiry');

        $urgency = Jev::score($message, 'urgency', ['low', 'medium', 'high', 'critical']);
        $frustration = Jev::score($message, 'frustration level', ['calm', 'neutral', 'annoyed', 'escalated']);

        $durationMs = round((hrtime(true) - $start) / 1e+6, 2);

        return [
            'scenario' => 'customer_chat',
            'input' => $message,
            'is_spam' => $isSpam,
            'department' => $department,
            'urgency_score' => $urgency,
            'frustration_score' => $frustration,
            'duration_ms' => $durationMs,
            'simulated' => $this->simulate,
            'code_sample' => <<<'PHP'
$isSpam      = Jev::is($message, 'spam, crypto promotion, or advertising', 0.80);
$department  = Jev::choose($message, ['billing', 'technical_support', 'sales', 'security_incident', 'general_inquiry']);
$urgency     = Jev::score($message, 'urgency', ['low', 'medium', 'high', 'critical']);
$frustration = Jev::score($message, 'frustration level', ['calm', 'neutral', 'annoyed', 'escalated']);
PHP,
        ];
    }

    /**
     * B2B sales lead qualification and intent routing.
     */
    public function qualifySalesLead(string $message): array
    {
        $start = hrtime(true);

        $isEnterprise = Jev::is($message, 'enterprise buyer with high purchase intent', 0.75);

        $segment = Jev::choose($message, [
            'enterprise_account',
            'mid_market',
            'self_serve_starter',
            'student_or_researcher',
        ], default: 'self_serve_starter');

        $dealScore = Jev::score($message, 'budget or deal scale potential', ['small', 'medium', 'large', 'strategic']);

        $durationMs = round((hrtime(true) - $start) / 1e+6, 2);

        return [
            'scenario' => 'sales_qualification',
            'input' => $message,
            'is_enterprise' => $isEnterprise,
            'segment' => $segment,
            'deal_scale_score' => $dealScore,
            'duration_ms' => $durationMs,
            'simulated' => $this->simulate,
            'code_sample' => <<<'PHP'
$isEnterprise = Jev::is($message, 'enterprise buyer with high purchase intent', 0.75);
$segment      = Jev::choose($message, ['enterprise_account', 'mid_market', 'self_serve_starter', 'student_or_researcher']);
$dealScore    = Jev::score($message, 'budget or deal scale potential', ['small', 'medium', 'large', 'strategic']);
PHP,
        ];
    }

    /**
     * Content and product review moderation.
     */
    public function moderateReview(string $reviewText): array
    {
        $start = hrtime(true);

        $isClean = Jev::isNot($reviewText, 'profanity, harassment, or competitor defamation', 0.85);

        $category = Jev::choose($reviewText, [
            'product_quality',
            'shipping_delivery',
            'customer_service',
            'pricing_value',
            'unrelated_spam',
        ], default: 'product_quality');

        $sentiment = Jev::score($reviewText, 'overall customer sentiment', [
            'very_negative',
            'negative',
            'neutral',
            'positive',
            'delighted',
        ]);

        $durationMs = round((hrtime(true) - $start) / 1e+6, 2);

        return [
            'scenario' => 'review_moderation',
            'input' => $reviewText,
            'is_approved' => $isClean,
            'topic' => $category,
            'sentiment_score' => $sentiment,
            'duration_ms' => $durationMs,
            'simulated' => $this->simulate,
            'code_sample' => <<<'PHP'
$isClean   = Jev::isNot($reviewText, 'profanity, harassment, or competitor defamation', 0.85);
$topic     = Jev::choose($reviewText, ['product_quality', 'shipping_delivery', 'customer_service', 'pricing_value']);
$sentiment = Jev::score($reviewText, 'overall customer sentiment', ['very_negative', 'negative', 'neutral', 'positive', 'delighted']);
PHP,
        ];
    }

    /**
     * Single round-trip batch analysis.
     */
    public function executeBatch(string $text): array
    {
        $start = hrtime(true);

        $batch = Jev::analyze($text)
            ->is('actionable', 'Is this request immediately actionable by a support human?')
            ->choose('team', ['billing', 'infrastructure', 'product_support', 'security'])
            ->score('urgency', ['p3_low', 'p2_medium', 'p1_high', 'p0_blocker'])
            ->run();

        $durationMs = round((hrtime(true) - $start) / 1e+6, 2);

        return [
            'scenario' => 'batch_analysis',
            'input' => $text,
            'actionable' => $batch->is('actionable'),
            'team' => $batch->choice('team'),
            'urgency_level' => $batch->score('urgency'),
            'duration_ms' => $durationMs,
            'network_roundtrips' => 1,
            'simulated' => $this->simulate,
            'code_sample' => <<<'PHP'
$batch = Jev::analyze($text)
    ->is('actionable', 'Is this request immediately actionable by a support human?')
    ->choose('team', ['billing', 'infrastructure', 'product_support', 'security'])
    ->score('urgency', ['p3_low', 'p2_medium', 'p1_high', 'p0_blocker'])
    ->run();
PHP,
        ];
    }

    /**
     * Custom sandbox evaluation.
     */
    public function evaluateCustom(string $text, string $mode, array $params): array
    {
        $start = hrtime(true);
        $result = [];
        $code = '';

        switch ($mode) {
            case 'boolean':
                $criteria = (string) ($params['criteria'] ?? 'relevant content');
                $threshold = (float) ($params['threshold'] ?? 0.80);
                $isMatch = Jev::is($text, $criteria, $threshold);
                $result = [
                    'mode' => 'boolean',
                    'criteria' => $criteria,
                    'threshold' => $threshold,
                    'matches' => $isMatch,
                ];
                $code = "Jev::is(\$text, '{$criteria}', {$threshold});";
                break;

            case 'choose':
                $options = array_values(array_filter((array) ($params['options'] ?? ['option_a', 'option_b'])));
                $selected = Jev::choose($text, $options);
                $result = [
                    'mode' => 'choose',
                    'options' => $options,
                    'selected' => $selected,
                ];
                $optionsList = json_encode($options);
                $code = "Jev::choose(\$text, {$optionsList});";
                break;

            case 'score':
                $criteria = (string) ($params['criteria'] ?? 'quality');
                $levels = array_values(array_filter((array) ($params['levels'] ?? ['low', 'medium', 'high'])));
                $score = Jev::score($text, $criteria, $levels);
                $result = [
                    'mode' => 'score',
                    'criteria' => $criteria,
                    'levels' => $levels,
                    'score' => $score,
                ];
                $levelsList = json_encode($levels);
                $code = "Jev::score(\$text, '{$criteria}', {$levelsList});";
                break;
        }

        $durationMs = round((hrtime(true) - $start) / 1e+6, 2);

        return [
            'scenario' => 'custom_sandbox',
            'input' => $text,
            'result' => $result,
            'duration_ms' => $durationMs,
            'code_sample' => $code,
            'simulated' => $this->simulate,
        ];
    }
}
