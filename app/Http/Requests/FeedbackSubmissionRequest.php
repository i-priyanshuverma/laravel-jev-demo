<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Priyanshu\LaravelJev\Rules\JevRule;

class FeedbackSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'category' => ['required', 'string', 'in:feature_request,bug_report,general_feedback'],
            'message' => [
                'required',
                'string',
                'min:10',
                'max:2000',
                JevRule::not('spam, cryptocurrency advertisement, or abusive language')
                    ->message('The message appears to contain spam or prohibited content.'),
                JevRule::is('constructive product feedback or genuine inquiry')
                    ->message('Please provide constructive and relevant product feedback.'),
            ],
        ];
    }
}
