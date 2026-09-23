<?php

declare(strict_types=1);

namespace App\Enums;

enum TriageScenario: string
{
    case CustomerChat = 'customer_chat';
    case SalesQualification = 'sales_qualification';
    case ReviewModeration = 'review_moderation';
    case BatchAnalysis = 'batch_analysis';

    public function label(): string
    {
        return match ($this) {
            self::CustomerChat => 'Customer Support Triage',
            self::SalesQualification => 'Lead Qualification',
            self::ReviewModeration => 'Review & Content Moderation',
            self::BatchAnalysis => 'Single Round-Trip Batch Analysis',
        };
    }

    public function defaultMessage(): string
    {
        return match ($this) {
            self::CustomerChat => 'Our production database crashed and returning 500 errors on checkout. Please escalate!',
            self::SalesQualification => 'We are evaluating your platform for our 500 engineers. Looking for enterprise pricing.',
            self::ReviewModeration => 'THIS PRODUCT IS A COMPLETE SCAM DO NOT BUY FROM THESE THIEVES.',
            self::BatchAnalysis => 'Customer card renewal failed. Account past due 3 days. Send dunning notice.',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
