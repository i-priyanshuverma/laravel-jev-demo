<?php

declare(strict_types=1);

namespace App\Services;

use Priyanshu\LaravelJev\Support\BatchResult;
use Priyanshu\LaravelJev\Support\JevResult;
use Priyanshu\LaravelJev\Support\Question;
use Priyanshu\LaravelJev\Testing\JevFake;

/**
 * Enhanced simulation driver for Laravel Jev demo.
 *
 * Extends the official JevFake to provide realistic heuristic decisions
 * in offline Simulation Mode for both preset scenarios and custom user inputs,
 * without requiring live API keys or external network connections.
 */
class DemoJevFake extends JevFake
{
    /**
     * @var list<string>
     */
    protected array $spamKeywords = [
        'crypto', 'airdrop', 'usdt', 'btc', 'bitcoin', 'giveaway', 'tokens',
        'free money', 'passive income', 'telegram', 't.me', 'airdrop-bonanza',
        'gift card', 'claim 100', 'claim 500', 'lottery', 'winner', 'click here',
        'scammer-alert', 'http://', 'https://',
    ];

    /**
     * @var list<string>
     */
    protected array $abusiveKeywords = [
        'scam', 'scammers', 'idiots', 'stole', 'garbage', 'useless', 'trash',
        'fraud', 'hate', 'terrible', 'worst ever', 'fuck', 'shit',
    ];

    /**
     * @var list<string>
     */
    protected array $urgentKeywords = [
        'crashed', 'crash', 'outage', 'down', '500 error', 'http 500', 'fatal', 'failing',
        'emergency', 'critical', 'losing transactions', 'dropped', 'breach',
        'ddos', 'flood', 'blocked', 'p0', 'broken',
    ];

    /**
     * @var list<string>
     */
    protected array $enterpriseKeywords = [
        'enterprise', 'seats', 'employees', 'rfp', 'sla', 'saml', 'sso',
        'soc2', 'procurement', 'volume pricing', 'annual contract', 'custom agreement',
        '14,000', '650', 'fortune',
    ];

    /**
     * Boolean classification: Does input match criteria?
     */
    public function is(string $input, string $criteria, ?float $threshold = null): bool
    {
        $key = "is:{$criteria}";

        if (array_key_exists($key, $this->expectations)) {
            return parent::is($input, $criteria, $threshold);
        }

        $result = $this->evaluateHeuristicIs($input, $criteria);

        $this->recordedIs[] = [
            'input' => $input,
            'criteria' => $criteria,
            'result' => $result,
        ];

        return $result;
    }

    /**
     * Categorical selection: Choose the best matching option.
     */
    public function choose(string $input, array $options, ?string $default = null): string
    {
        if (array_key_exists('choose', $this->expectations)) {
            return parent::choose($input, $options, $default);
        }

        $choice = $this->evaluateHeuristicChoose($input, $options, $default);

        $this->recordedChoose[] = [
            'input' => $input,
            'options' => $options,
            'choice' => $choice,
        ];

        return $choice;
    }

    /**
     * Numerical scoring: Rate criteria on a 0.0 to 1.0 scale.
     */
    public function score(string $input, string $criteria, array $levels = ['low', 'medium', 'high']): float
    {
        $key = "score:{$criteria}";

        if (array_key_exists($key, $this->expectations) || array_key_exists('score', $this->expectations)) {
            return parent::score($input, $criteria, $levels);
        }

        $score = $this->evaluateHeuristicScore($input, $criteria, $levels);

        $this->recordedScore[] = [
            'input' => $input,
            'criteria' => $criteria,
            'levels' => $levels,
            'score' => $score,
        ];

        return $score;
    }

    /**
     * Multi-question single round-trip batch evaluation.
     */
    public function runBatch(string $state, array $questions): BatchResult
    {
        $this->recordedBatch[] = [
            'input' => $state,
            'questions' => $questions,
        ];

        $answers = [];
        foreach ($questions as $name => $question) {
            if ($question instanceof Question) {
                $data = $question->toArray();
                $type = $data['type'] ?? 'choice';
                if ($type === 'choice') {
                    $options = array_keys($data['options'] ?? ['option_a' => 'option_a']);
                    $choice = $this->choose($state, $options);
                    $answers[$name] = [
                        'type' => 'choice',
                        'choice' => $choice,
                        'confidence' => 0.96,
                        'probabilities' => [$choice => 0.96],
                    ];
                } elseif ($type === 'noul') {
                    $criteria = $data['instructions'] ?? $name;
                    $isTrue = $this->is($state, $criteria);
                    $answers[$name] = [
                        'type' => 'noul',
                        'isTrue' => $isTrue,
                        'confidence' => 0.94,
                        'probability' => $isTrue ? 0.94 : 0.06,
                    ];
                } elseif ($type === 'score') {
                    $criteria = $data['instructions'] ?? $name;
                    $levels = $data['levels'] ?? ['low', 'medium', 'high'];
                    $val = $this->score($state, $criteria, $levels);
                    $answers[$name] = [
                        'type' => 'score',
                        'score' => $val,
                        'confidence' => 0.92,
                    ];
                }
            } else {
                $choice = $this->resolveValue("choose:{$name}", $this->resolveValue('choose', 'default'));
                $answers[$name] = [
                    'type' => 'choice',
                    'choice' => (string) $choice,
                    'confidence' => 0.95,
                    'probabilities' => [(string) $choice => 0.95],
                ];
            }
        }

        $rawResult = new JevResult(
            model: 'jev-demo-simulator',
            answers: $answers,
            usage: ['total_tokens' => 24],
            latencyMs: 2
        );

        return new BatchResult($rawResult, 0.80, 2);
    }

    /**
     * Evaluate boolean match heuristics.
     */
    protected function evaluateHeuristicIs(string $input, string $criteria): bool
    {
        $inputLower = mb_strtolower($input);
        $criteriaLower = mb_strtolower($criteria);

        // Spam & Scam detection
        if (str_contains($criteriaLower, 'spam') || str_contains($criteriaLower, 'crypto') || str_contains($criteriaLower, 'advertis')) {
            foreach ($this->spamKeywords as $word) {
                if (str_contains($inputLower, $word)) {
                    return true;
                }
            }

            return false;
        }

        // Constructive product feedback check
        if (str_contains($criteriaLower, 'constructive') || str_contains($criteriaLower, 'feedback')) {
            // If it's spam or abusive, it's not constructive
            foreach (array_merge($this->spamKeywords, $this->abusiveKeywords) as $bad) {
                if (str_contains($inputLower, $bad)) {
                    return false;
                }
            }

            // Gibberish / too short check
            return mb_strlen(trim($input)) >= 8;
        }

        // Enterprise buyer intent
        if (str_contains($criteriaLower, 'enterprise')) {
            foreach ($this->enterpriseKeywords as $ent) {
                if (str_contains($inputLower, $ent)) {
                    return true;
                }
            }

            return false;
        }

        // Outage / Incident detection
        if (str_contains($criteriaLower, 'outage') || str_contains($criteriaLower, 'crash') || str_contains($criteriaLower, 'incident') || str_contains($criteriaLower, 'critical')) {
            foreach ($this->urgentKeywords as $urg) {
                if (str_contains($inputLower, $urg)) {
                    return true;
                }
            }

            return false;
        }

        // Profanity / Harassment / Abusive check
        if (str_contains($criteriaLower, 'profanity') || str_contains($criteriaLower, 'harassment') || str_contains($criteriaLower, 'abusive') || str_contains($criteriaLower, 'defamation')) {
            foreach ($this->abusiveKeywords as $abu) {
                if (str_contains($inputLower, $abu)) {
                    return true;
                }
            }

            return false;
        }

        // Actionable check
        if (str_contains($criteriaLower, 'actionable')) {
            return ! str_contains($inputLower, 'airdrop') && ! str_contains($inputLower, 'free crypto');
        }

        // Billing or refund request
        if (str_contains($criteriaLower, 'billing') || str_contains($criteriaLower, 'refund')) {
            return str_contains($inputLower, 'refund') || str_contains($inputLower, 'charge') || str_contains($inputLower, 'invoice') || str_contains($inputLower, 'order');
        }

        // General word overlap heuristic
        $criteriaWords = array_filter(explode(' ', preg_replace('/[^\w\s]/', '', $criteriaLower)));
        foreach ($criteriaWords as $word) {
            if (mb_strlen($word) > 3 && str_contains($inputLower, $word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evaluate categorical selection heuristics.
     *
     * @param  list<string>|array<string, string>  $options
     */
    protected function evaluateHeuristicChoose(string $input, array $options, ?string $default = null): string
    {
        $inputLower = mb_strtolower($input);
        $scores = [];

        foreach ($options as $opt) {
            $optClean = (string) $opt;
            $optLower = mb_strtolower($optClean);
            $score = 0;

            // Direct match
            if (str_contains($inputLower, $optLower)) {
                $score += 10;
            }

            // Keyword associations
            if ($optLower === 'billing' && (str_contains($inputLower, 'invoice') || str_contains($inputLower, 'charge') || str_contains($inputLower, 'refund') || str_contains($inputLower, 'card') || str_contains($inputLower, 'payment') || str_contains($inputLower, 'past due') || str_contains($inputLower, 'dunning'))) {
                $score += 8;
            }

            if (($optLower === 'technical_support' || $optLower === 'infrastructure' || $optLower === 'product_support') && (str_contains($inputLower, 'crash') || str_contains($inputLower, '500 error') || str_contains($inputLower, 'http 500') || str_contains($inputLower, 'database') || str_contains($inputLower, 'cluster') || str_contains($inputLower, 'server') || str_contains($inputLower, 'down') || str_contains($inputLower, 'latency'))) {
                $score += 8;
            }

            if (($optLower === 'sales' || $optLower === 'enterprise_account' || $optLower === 'mid_market') && (str_contains($inputLower, 'enterprise') || str_contains($inputLower, 'seats') || str_contains($inputLower, 'contract') || str_contains($inputLower, 'rfp') || str_contains($inputLower, 'quote') || str_contains($inputLower, 'pricing') || str_contains($inputLower, 'procurement') || str_contains($inputLower, 'employees'))) {
                $score += 8;
            }

            if (($optLower === 'security_incident' || $optLower === 'security') && (str_contains($inputLower, 'crypto') || str_contains($inputLower, 'spam') || str_contains($inputLower, 'usdt') || str_contains($inputLower, 'airdrop') || str_contains($inputLower, 'ddos') || str_contains($inputLower, 'flood') || str_contains($inputLower, 'attack') || str_contains($inputLower, 'breach') || str_contains($inputLower, 'asn'))) {
                $score += 12;
            }

            if ($optLower === 'product_quality' && (str_contains($inputLower, 'latency') || str_contains($inputLower, 'dispatch') || str_contains($inputLower, 'performance') || str_contains($inputLower, 'fast') || str_contains($inputLower, 'documentation') || str_contains($inputLower, 'export'))) {
                $score += 8;
            }

            if ($optLower === 'unrelated_spam' && (str_contains($inputLower, 'crypto') || str_contains($inputLower, 'scam') || str_contains($inputLower, 'usdt') || str_contains($inputLower, 'airdrop'))) {
                $score += 9;
            }

            if ($optLower === 'student_or_researcher' && (str_contains($inputLower, 'student') || str_contains($inputLower, 'university') || str_contains($inputLower, 'hobby') || str_contains($inputLower, 'personal use'))) {
                $score += 8;
            }

            $scores[$optClean] = $score;
        }

        arsort($scores);
        $topScore = reset($scores);

        if ($topScore > 0) {
            return (string) key($scores);
        }

        return $default ?? (string) (reset($options) ?: 'default');
    }

    /**
     * Evaluate numeric scoring heuristics.
     *
     * @param  list<string>  $levels
     */
    protected function evaluateHeuristicScore(string $input, string $criteria, array $levels): float
    {
        $inputLower = mb_strtolower($input);
        $critLower = mb_strtolower($criteria);

        // Urgency or Frustration
        if (str_contains($critLower, 'urgency') || str_contains($critLower, 'frustration')) {
            foreach ($this->urgentKeywords as $urg) {
                if (str_contains($inputLower, $urg)) {
                    return 0.95;
                }
            }

            if (str_contains($inputLower, 'charge') || str_contains($inputLower, 'refund') || str_contains($inputLower, 'past due') || str_contains($inputLower, 'failed')) {
                return 0.72;
            }

            if (str_contains($inputLower, 'enterprise') || str_contains($inputLower, 'seats')) {
                return 0.60;
            }

            if (str_contains($inputLower, 'student') || str_contains($inputLower, 'crypto') || str_contains($inputLower, 'free')) {
                return 0.10;
            }

            return 0.50;
        }

        // Deal scale or budget potential
        if (str_contains($critLower, 'deal') || str_contains($critLower, 'budget') || str_contains($critLower, 'scale')) {
            if (str_contains($inputLower, '14,000') || str_contains($inputLower, '650') || str_contains($inputLower, 'enterprise') || str_contains($inputLower, 'rfp')) {
                return 0.92;
            }

            if (str_contains($inputLower, 'series a') || str_contains($inputLower, '2.5 million')) {
                return 0.70;
            }

            if (str_contains($inputLower, 'student') || str_contains($inputLower, 'personal')) {
                return 0.15;
            }

            return 0.40;
        }

        // Sentiment or Customer Satisfaction
        if (str_contains($critLower, 'sentiment') || str_contains($critLower, 'satisfaction')) {
            foreach ($this->abusiveKeywords as $abu) {
                if (str_contains($inputLower, $abu)) {
                    return 0.08;
                }
            }

            if (str_contains($inputLower, 'reduced') || str_contains($inputLower, 'saved') || str_contains($inputLower, 'love') || str_contains($inputLower, 'great') || str_contains($inputLower, 'excellent') || str_contains($inputLower, 'fantastic')) {
                return 0.92;
            }

            if (str_contains($inputLower, 'cluttered') || str_contains($inputLower, 'difficult') || str_contains($inputLower, 'slow')) {
                return 0.45;
            }

            return 0.65;
        }

        // Default normalized rating
        return 0.75;
    }
}
