<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackSubmissionRequest;
use App\Services\TriageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriageController extends Controller
{
    public function __construct(
        protected TriageService $triage
    ) {}

    /**
     * Evaluate predefined scenarios.
     */
    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario' => ['required', 'string', 'in:customer_chat,sales_qualification,review_moderation,batch_analysis'],
            'input' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        $result = match ($validated['scenario']) {
            'customer_chat' => $this->triage->triageCustomerChat($validated['input']),
            'sales_qualification' => $this->triage->qualifySalesLead($validated['input']),
            'review_moderation' => $this->triage->moderateReview($validated['input']),
            'batch_analysis' => $this->triage->executeBatch($validated['input']),
        };

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    /**
     * Run custom rule sandbox evaluation.
     */
    public function sandbox(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'input' => ['required', 'string', 'min:2', 'max:5000'],
            'mode' => ['required', 'string', 'in:boolean,choose,score'],
            'params' => ['nullable', 'array'],
        ]);

        $result = $this->triage->evaluateCustom(
            text: $validated['input'],
            mode: $validated['mode'],
            params: $validated['params'] ?? []
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
        return response()->json([
            'customer_chat' => [
                [
                    'id' => 'chat_urgent_outage',
                    'title' => 'Critical Database Outage',
                    'tag' => 'Technical P0',
                    'message' => 'Our production database cluster just crashed and returning 500 errors on all user checkouts. We are losing transactions right now, please escalate!',
                ],
                [
                    'id' => 'chat_crypto_spam',
                    'title' => 'Crypto Spam Bot',
                    'tag' => 'Spam',
                    'message' => 'Claim your 500 FREE USDT right now on http://crypto-airdrop-bonanza.xyz! Limited spots available for the next 10 minutes only!!',
                ],
                [
                    'id' => 'chat_billing_dispute',
                    'title' => 'Double Charge Dispute',
                    'tag' => 'Billing',
                    'message' => 'Hi team, I noticed invoice #9402 charged my credit card twice this morning ($299 x 2). Can someone review and issue a refund for the duplicate charge?',
                ],
                [
                    'id' => 'chat_sales_quote',
                    'title' => 'Enterprise Procurement',
                    'tag' => 'Sales',
                    'message' => 'We are evaluating your platform for our 650 engineers. Could we schedule a call this week to review enterprise SLAs and annual volume pricing?',
                ],
            ],
            'sales_qualification' => [
                [
                    'id' => 'lead_enterprise_procurement',
                    'title' => 'Enterprise RFP',
                    'tag' => 'Enterprise',
                    'message' => 'We are a global logistics company with 14,000 employees. We need SOC2 Type II compliance, custom SAML SSO, and a dedicated account executive.',
                ],
                [
                    'id' => 'lead_student_hobbyist',
                    'title' => 'Student Inquiry',
                    'tag' => 'Education',
                    'message' => 'Hey, I am doing a university class project on webhooks and was wondering if you offer a student discount or free trial credits for personal use.',
                ],
                [
                    'id' => 'lead_midmarket_saas',
                    'title' => 'Series A SaaS',
                    'tag' => 'Mid-Market',
                    'message' => 'We just raised our Series A and are hitting rate limits on our current provider. Looking to migrate ~2.5 million monthly events starting next month.',
                ],
            ],
            'review_moderation' => [
                [
                    'id' => 'review_constructive_praise',
                    'title' => 'Product Praise',
                    'tag' => 'Positive',
                    'message' => 'Migrating to this tool reduced our webhook dispatch latency by 80%. The documentation and test helpers saved our engineering team weeks of boilerplate.',
                ],
                [
                    'id' => 'review_toxic_competitor',
                    'title' => 'Abusive Defamation',
                    'tag' => 'Flagged',
                    'message' => 'THIS COMPANY IS A TOTAL SCAM RUN BY IDIOTS DO NOT BUY THEY STOLE MY MONEY GO TO SCAMMER-ALERT.ORG RIGHT NOW',
                ],
                [
                    'id' => 'review_critical_feature',
                    'title' => 'Layout Feedback',
                    'tag' => 'Feedback',
                    'message' => 'Great performance overall, but the dashboard UI feels slightly cluttered on mobile screens. Would love to see better responsiveness on tablet viewports.',
                ],
            ],
            'batch_analysis' => [
                [
                    'id' => 'batch_payment_failure',
                    'title' => 'Stripe Webhook Failure',
                    'tag' => 'Billing Alert',
                    'message' => 'Customer cust_8819 renewal payment failed due to card_declined. Account is currently past due 3 days. Send dunning notice and pause premium features.',
                ],
                [
                    'id' => 'batch_ddos_alert',
                    'title' => 'High Traffic Anomaly',
                    'tag' => 'Security Alert',
                    'message' => 'Incoming request rate from ASN 13335 jumped 4,500% in 90 seconds. Potential Layer-7 HTTP flood detected on /api/v1/ingest endpoint.',
                ],
            ],
            'custom_sandbox' => [
                [
                    'id' => 'sandbox_spam',
                    'title' => 'Spam & Phishing Filter',
                    'tag' => 'Boolean Jev::is',
                    'mode' => 'boolean',
                    'criteria' => 'spam, cryptocurrency promotion, or advertising',
                    'threshold' => 0.80,
                    'message' => 'Claim your 500 FREE USDT right now on http://crypto-airdrop-bonanza.xyz! Limited spots available for the next 10 minutes only!!',
                ],
                [
                    'id' => 'sandbox_outage',
                    'title' => 'Critical Infrastructure Outage',
                    'tag' => 'Boolean Jev::is',
                    'mode' => 'boolean',
                    'criteria' => 'urgent production outage or data corruption',
                    'threshold' => 0.75,
                    'message' => 'Our production database cluster just crashed and returning 500 errors on all user checkouts. We are losing transactions right now, please escalate!',
                ],
                [
                    'id' => 'sandbox_routing',
                    'title' => 'Department Router',
                    'tag' => 'Choose Jev::choose',
                    'mode' => 'choose',
                    'options' => 'billing, technical_support, sales, security_incident, general_inquiry',
                    'message' => 'We are evaluating your platform for our 650 engineers. Could we schedule a call this week to review enterprise SLAs and annual volume pricing?',
                ],
                [
                    'id' => 'sandbox_priority',
                    'title' => 'Incident Priority Level',
                    'tag' => 'Choose Jev::choose',
                    'mode' => 'choose',
                    'options' => 'p3_low, p2_medium, p1_high, p0_blocker',
                    'message' => 'All checkout endpoints are throwing 500 errors during Black Friday traffic rush!',
                ],
                [
                    'id' => 'sandbox_sentiment',
                    'title' => 'Customer Satisfaction',
                    'tag' => 'Score Jev::score',
                    'mode' => 'score',
                    'criteria' => 'overall customer satisfaction',
                    'options' => 'very_negative, negative, neutral, positive, delighted',
                    'message' => 'Migrating to this tool reduced our webhook dispatch latency by 80%. The documentation and test helpers saved our engineering team weeks of boilerplate.',
                ],
                [
                    'id' => 'sandbox_urgency',
                    'title' => 'Response Urgency',
                    'tag' => 'Score Jev::score',
                    'mode' => 'score',
                    'criteria' => 'immediate response urgency',
                    'options' => 'low, medium, high, critical',
                    'message' => 'Customer cust_8819 renewal payment failed due to card_declined. Account is currently past due 3 days. Send dunning notice and pause premium features.',
                ],
            ],
            'form_validation' => [
                [
                    'id' => 'form_valid_feedback',
                    'title' => 'Constructive Feature Request',
                    'tag' => 'Valid',
                    'name' => 'Sarah Jenkins',
                    'email' => 'sarah@company.com',
                    'category' => 'feature_request',
                    'message' => 'I really love the webhook replay feature! It would be great if you could also add batch CSV export with email notifications for high-volume logs.',
                ],
                [
                    'id' => 'form_valid_bug',
                    'title' => 'Genuine Bug Report',
                    'tag' => 'Valid',
                    'name' => 'David Chen',
                    'email' => 'david@chen-engineering.io',
                    'category' => 'bug_report',
                    'message' => 'When filtering webhook logs by custom metadata tags with hyphens, the query times out on large datasets. Here is the reproduction query.',
                ],
                [
                    'id' => 'form_spam_crypto',
                    'title' => 'Crypto Airdrop Spam',
                    'tag' => 'Fails Rule::not',
                    'name' => 'Crypto Bot',
                    'email' => 'promo@crypto-airdrop-bonanza.xyz',
                    'category' => 'general_feedback',
                    'message' => 'Claim 500 FREE USDT right now on http://crypto-airdrop-bonanza.xyz! Limited spots available for the next 10 minutes only!!',
                ],
                [
                    'id' => 'form_abusive_rant',
                    'title' => 'Abusive Defamation Rant',
                    'tag' => 'Fails Rule::is',
                    'name' => 'Angry User',
                    'email' => 'angry@burner-mail.cc',
                    'category' => 'general_feedback',
                    'message' => 'THIS COMPANY IS A TOTAL SCAM RUN BY IDIOTS DO NOT BUY THEY STOLE MY MONEY GO TO SCAMMER-ALERT.ORG RIGHT NOW',
                ],
            ],
        ]);
    }
}
