<?php

declare(strict_types=1);

namespace Tests\Feature;

use Priyanshu\LaravelJev\Facades\Jev;
use Tests\TestCase;

class TriageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Jev::fake([
            'urgent db down' => [
                'is:spam' => false,
                'choose:department' => 'technical_support',
                'score:urgency' => 0.95,
                'score:frustration' => 0.85,
            ],
            'claim 500 usdt free' => [
                'is:spam' => true,
                'choose:department' => 'security_incident',
                'score:urgency' => 0.10,
                'score:frustration' => 0.05,
            ],
            'enterprise contract quote' => [
                'is:enterprise' => true,
                'choose:segment' => 'enterprise_account',
                'score:deal' => 0.90,
            ],
            'abusive scam product review' => [
                'is:clean' => false,
                'choose:topic' => 'unrelated_spam',
                'score:sentiment' => 0.10,
            ],
        ]);
    }

    public function test_it_returns_triage_dashboard_view(): void
    {
        $response = $this->get(route('console'));

        $response->assertStatus(200);
        $response->assertSee('Laravel Jev Operations Console');
        $response->assertSee('Real-time classification and decision playground');
    }

    public function test_it_returns_presets_list(): void
    {
        $response = $this->getJson(route('api.triage.presets'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'customer_chat',
                'sales_qualification',
                'review_moderation',
                'batch_analysis',
            ]);
    }

    public function test_it_analyzes_customer_chat_scenario(): void
    {
        $response = $this->postJson(route('api.triage.analyze'), [
            'scenario' => 'customer_chat',
            'input' => 'Our production database crashed and returning 500 errors on checkout.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => [
                    'scenario',
                    'input',
                    'is_spam',
                    'department',
                    'urgency_score',
                    'frustration_score',
                    'duration_ms',
                    'code_sample',
                ],
            ]);
    }

    public function test_it_qualifies_sales_lead_scenario(): void
    {
        $response = $this->postJson(route('api.triage.analyze'), [
            'scenario' => 'sales_qualification',
            'input' => 'Looking to purchase 500 enterprise seats for our engineering team.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.scenario', 'sales_qualification')
            ->assertJsonStructure([
                'data' => [
                    'is_enterprise',
                    'segment',
                    'deal_scale_score',
                    'duration_ms',
                ],
            ]);
    }

    public function test_it_moderates_content_reviews(): void
    {
        $response = $this->postJson(route('api.triage.analyze'), [
            'scenario' => 'review_moderation',
            'input' => 'This package reduced our response latency by 80%. Highly recommended!',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.scenario', 'review_moderation')
            ->assertJsonStructure([
                'data' => [
                    'is_approved',
                    'topic',
                    'sentiment_score',
                    'duration_ms',
                ],
            ]);
    }

    public function test_it_executes_batch_analysis_with_single_roundtrip(): void
    {
        $response = $this->postJson(route('api.triage.analyze'), [
            'scenario' => 'batch_analysis',
            'input' => 'Customer payment failed. Account past due 3 days. Send dunning notice.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.scenario', 'batch_analysis')
            ->assertJsonPath('data.network_roundtrips', 1);
    }

    public function test_it_evaluates_custom_sandbox(): void
    {
        $response = $this->postJson(route('api.triage.sandbox'), [
            'input' => 'Can you please issue a refund for order #99402?',
            'mode' => 'boolean',
            'params' => [
                'criteria' => 'billing or refund request',
                'threshold' => 0.80,
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.result.mode', 'boolean')
            ->assertJsonStructure([
                'data' => [
                    'result' => [
                        'criteria',
                        'threshold',
                        'matches',
                    ],
                    'duration_ms',
                    'code_sample',
                ],
            ]);
    }

    public function test_feedback_form_passes_with_constructive_content(): void
    {
        Jev::fake([
            'is:spam, cryptocurrency advertisement, or abusive language' => false,
            'is:constructive product feedback or genuine inquiry' => true,
        ]);

        $response = $this->postJson('/api/feedback/submit', [
            'name' => 'Alex Rivera',
            'email' => 'alex@example.com',
            'category' => 'feature_request',
            'message' => 'Would love to see native Redis cluster support for response memoization.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }

    public function test_feedback_form_fails_validation_for_spam(): void
    {
        Jev::fake([
            'is:spam, cryptocurrency advertisement, or abusive language' => true,
            'is:constructive product feedback or genuine inquiry' => false,
        ]);

        $response = $this->postJson(route('api.feedback.submit'), [
            'name' => 'Spam Bot',
            'email' => 'bot@spammer.xyz',
            'category' => 'general_feedback',
            'message' => 'Claim 100 free crypto tokens right now on http://airdrop-bot.ru !!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_analyze_fails_validation_with_invalid_scenario(): void
    {
        $response = $this->postJson(route('api.triage.analyze'), [
            'scenario' => 'unknown_scenario',
            'input' => 'Some test message here',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['scenario']);
    }

    public function test_sandbox_fails_validation_with_invalid_mode(): void
    {
        $response = $this->postJson(route('api.triage.sandbox'), [
            'input' => 'Some test input',
            'mode' => 'unsupported_mode',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mode']);
    }
}
